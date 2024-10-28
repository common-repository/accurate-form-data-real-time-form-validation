<?php
/*
    Plugin Name: Accurate Data WP
    Plugin URI: http://www.accurateformdata.com
    Description: Plugin for checking the accuracy of user's entries (E-mail and Physical Address) in forms.
    Author: Hernan Marino
    Version: 1.2
    Author URI: http://www.accurateformdata.com
*/

@session_start();

/* Make sure jQuery is loaded */
add_action( 'wp_enqueue_script', 'load_jquery' );
function load_jquery() {
    wp_enqueue_script( 'jquery' );
}

/* Create the Options Page Menu - Admin*/
function accudata_admin_action(){
    add_options_page("Accu Data WP", "Accu Data WP", 1, "Accu_Data_WP", "accudata_admin");
        
}
add_action('admin_menu', 'accudata_admin_action'); 

/*
 * Handle the Options Page display and form submitting
 */

function accudata_admin(){
    include 'accu-data-admin.php';
}


/*
 * Functions for the front end
 */

//
//enqueue scripts
function accudata_put_scripts_1(){
//    wp_register_script( 'tooltip-script', plugins_url( '/js/opentip-jquery.min.js', __FILE__ ), array('jquery'), '1.0', false );
    wp_register_script( 'tipsy-script', plugins_url( '/js/jquery.tooltipster.js', __FILE__ ), array('jquery'), '1.0', false );
    wp_register_script( 'accudata-main-js', plugins_url( '/js/accudata-main.js', __FILE__ ), array('jquery'), '1.0', false );
//    wp_enqueue_script( 'tooltip-script' );
    wp_enqueue_script( 'tipsy-script' );
    wp_enqueue_script( 'accudata-main-js' );
    
}
add_action( 'wp_enqueue_scripts', 'accudata_put_scripts_1' );  

//
//enqueue styles
//function accudata_put_styles_1(){
//    wp_register_style( 'custom-style', plugins_url( '/css/opentip.css', __FILE__ ), array(), '2.1', 'all' );    
//    wp_enqueue_style( 'custom-style' );  
//    
//    wp_register_style( 'settings-style', plugins_url( '/css/settings.css', __FILE__ ), array(), '1.0', 'all' );    
//    wp_enqueue_style( 'settings-style' );  
//}
//add_action( 'wp_enqueue_scripts', 'accudata_put_styles_1' ); 



add_action('init', 'load_accu_scripts');

function load_accu_scripts() {
//    wp_register_style('opentip_style', plugins_url('/css/opentip.css', __FILE__));
//    wp_enqueue_style('opentip_style');
    wp_register_style('tooltipser_style', plugins_url('/css/tooltipster.css', __FILE__));
    wp_enqueue_style('tooltipser_style');
    wp_register_style('settings_style', plugins_url('/css/settings.css', __FILE__));
    wp_enqueue_style('settings_style');
}

function accudata_footer_code(){
    include 'accu-data-front-end.php';
}
add_action('wp_footer', 'accudata_footer_code');


/*
 * Check the Address
 */
function accudata_ajax_check_address(){
    $client_key = get_option('accudata_client_key');
    $block_attempts = get_option('accudata_physical_address_block_attempts');
         
    //if the session time stamp for the max number of queries is higher than 30 minutes, delete it
    if(!isset($_SESSION['accudata-session-timeout']) || ($_SESSION['accudata-session-timeout'] + (30 * 60 * 60)) < time()){
        $_SESSION['accudata-session-timeout'] = time();
        $_SESSION['accudata-max-number-of-queries'] = 0;
    }
    
    
    //figure out the address
    if(isset($_POST['wAddress']) && trim($_POST['wAddress']) != ''){
        $post_address = $_POST['wAddress'];
        $post_zip = '';
        $post_country = '';
        
        if(!isset($_POST['wZip']) || !isset($_POST['wCountry'])){
            //the address comes in a single field, try to disect it
            //the goal is to determine, first the country, then the zip
            $t = explode(',', $post_address);
            if(sizeof($t) > 3){
                //we could expect that the first argument will be the number and street
                $number_and_street = trim($t[0]);
                
                //then if the lenght of $t is 4, we can expect an address in this format:
                //StreeNumber StreetName, City, Zip Code, State, Country
                //if its 3, maybe either of City or Zip Code is missing
                //if the zip/city field is composed mainly by letters, then it should be a City
                //its difficult to determine the zip, so first determine the country, supposedly the last parameter
                
                $country = trim($t[(sizeof($t) - 1)]);
                $corrected_country = accudata_find_country($country);
                if(!$corrected_country){
                    //Please enter your address in this format: Address, City, Zip Code, State, Country
                    $ret['address']['error'] = 'Y';
                    $ret['address']['message'] = 'Your Country can not be found. Please enter your address in this format: Address, City, Zip Code, State, Country';
                    echo json_encode($ret);
                    exit(0);
                }
                
                //with this data try to verify the address remotely
                $address_to_query = '';
                for($i=0; $i < (sizeof($t) - 1); $i++){
                    $address_to_query .= trim($t[$i]).', ';
                }
                $address_to_query .= $corrected_country;
                //echo $address_to_query;
                //exit(0);
                if(isset($_SESSION['accudata_address_checked'][$address_to_query])){
                    $ret['address'] = $_SESSION['accudata_address_checked'][$address_to_query];
                    $ret['address']->{'fromsession'} = 'yes';
                    echo json_encode($ret);
                    exit(0);
                }
                $_SESSION['accudata-max-number-of-queries'] = (empty($_SESSION['accudata-max-number-of-queries']) ? 1 : ($_SESSION['accudata-max-number-of-queries'] + 1));
                if($_SESSION['accudata-max-number-of-queries'] > $block_attempts){
                    $ret_array = array(
                        'error' => 'N',
                        'message' => 'Too many attempts. Considering the address correct.'
                    );
                    $ret['address'] = $ret_array;
                    echo json_encode($ret);
                    exit(0);
                }
                
                
                $url = 'http://accu-query.accurateformdata.com/address/check_address.new.2.php?api_key='.$client_key.'&address='.urlencode($address_to_query);
                $response = file_get_contents($url);
                $response = json_decode($response);
                if($response == NULL){
                    $response->error = 'Y';
                    $response->{'message'} = 'Address Incorrect, Null response from query server';
                }
                $_SESSION['accudata_address_checked'][$address_to_query] = $response;
                $ret['address'] = $response;
                $ret['query'] = $address_to_query;
                echo json_encode($ret);
                exit(0);
                
                
            }else{
                //Please enter your address in this format: Address, City, Zip Code, State, Country
                $ret['address']['error'] = 'Y';
                $ret['address']['message'] = 'Please enter your address in this format: Address, City, Zip Code, State, Country';
                echo json_encode($ret);
                exit(0);
            }
            
        //if isset Zip & Country
        }else{
            $address_to_query = $post_address.', '.(isset($_POST['wCity']) ? $_POST['wCity'].', ' : '').(isset($_POST['wZip']) ? $_POST['wZip'].', ' : '').$_POST['wCountry'];
            
            if(isset($_SESSION['accudata_address_checked'][$address_to_query])){
                $ret['address'] = $_SESSION['accudata_address_checked'][$address_to_query];
                $ret['address']->{'fromsession'} = 'yes';
                echo json_encode($ret);
                exit(0);
            }

            $_SESSION['accudata-max-number-of-queries'] = (empty($_SESSION['accudata-max-number-of-queries']) ? 1 : ($_SESSION['accudata-max-number-of-queries'] + 1));
            if($_SESSION['accudata-max-number-of-queries'] > $block_attempts){
                $ret_array = array(
                    'error' => 'N',
                    'message' => 'Too many attempts. Considering the address correct.'
                );
                $ret['address'] = $ret_array;
                echo json_encode($ret);
                exit(0);
            }
            
            //make the call
            $url = 'http://accu-query.accurateformdata.com/address/check_address.new.2.php?api_key='.$client_key.'&address='.urlencode($address_to_query);
            $response = file_get_contents($url);
            $response = json_decode($response);
            
            if($response == NULL){
                $response->error = 'Y';
                $response->{'message'} = 'Address Incorrect, Null response from query server';
            }
            
            //if the zip exists both in the query and in the response, but it does not match, then it is an error
            //error_reporting(E_ALL);
            //ini_set('show_errors', "1");
            //ini_set('display_errors', "1");
            if($response->error == 'N' || $response->error === '0'){
                if($response->zipcode !== NULL && $_POST['wZip'] != $response->zipcode){
                    //make sure parts of the zip code are not in the response, for example for England
                    $t = explode(' ', $response->zipcode);
                    $t2 = explode (' ', $_POST['wZip']);
                    $valid = false;
                    foreach($t as $t_res){
                        foreach($t2 as $t_ori){
                            if(trim($t_res) == trim($t_ori)){
                                $valid = true;
                                break 2;
                            }
                        }
                    }
                    
                    if(!$valid){
                        $response->error = 'Y';
                        $response->{'message'} = 'Either the Street Address or the Zip Code are incorrects';
                        $response->{post_zip} = $_POST['wZip'];
                    }
                    
                }
            }
            $_SESSION['accudata_address_checked'][$address_to_query] = $response;
            $ret['address'] = $response;
            $ret['query'] = $address_to_query;
            echo json_encode($ret);
            exit(0);
        }
    }
    
    echo json_encode($ret);
    exit(0);
}

add_action('wp_ajax_accudata_check_address', 'accudata_ajax_check_address');
add_action('wp_ajax_nopriv_accudata_check_address', 'accudata_ajax_check_address');

function accudata_find_country($str){
    include 'vars.php';
    
    foreach($arr_country as $iso => $row_country){
        if($str == $iso){
            return $row_country['pname'];
        }
        
        if($str == $row_country['iso3']){
            return $row_country['pname'];
        }
        
        if($str == $row_country['pname']){
            return $row_country['pname'];
        }
        
        $simil = similar_text($str, $row_country['pname'], $per);
        
        if($per > 80){
            return $row_country['pname'];
        }
        
    }
    
    return false;
}


/*
 * Check the Email Address
 */
function accudata_ajax_check_email(){
    
    $client_key = get_option('accudata_client_key');
    //$email_fields = get_option('accudata_email_fields');
    //$address_fields = get_option('accudata_address_fields');
    $accept_open_domains = get_option('accudata_accept_open_domains');
    $accept_open_domains = ($accept_open_domains == 'yes' ? '1' : '0');
    $block_attempts = get_option('accudata_email_block_attempts');
     
    
    //if the session time stamp for the max number of queries is higher than 30 minutes, delete it
    if(!isset($_SESSION['accudata-session-timeout']) || ($_SESSION['accudata-session-timeout'] + (30 * 60 * 60)) < time()){
        $_SESSION['accudata-session-timeout'] = time();
        $_SESSION['accudata-max-number-of-queries'] = 0;
    }
    
    /*
     * 
     */
    $ret = array();
    if(isset($_POST['wEmail']) && trim($_POST['wEmail']) != ''){
        //filter email
        if(!filter_var($_POST['wEmail'], FILTER_VALIDATE_EMAIL)){
            $ret['email']['error'] = '1';
            $ret['email']['error'] = 'E-mail is not valid';
            echo json_encode($ret);
            exit(0);

        }else{
            
            //check if the email is not already in the cache
            if(isset($_SESSION['accudata_emails_checked'][$_POST['wEmail']])){
                $ret['email'] = $_SESSION['accudata_emails_checked'][$_POST['wEmail']];
                $ret['email']->fromsession = 'yes';
                echo json_encode($ret);
                exit(0);
            }
            $_SESSION['accudata-max-number-of-queries'] = (empty($_SESSION['accudata-max-number-of-queries']) ? 1 : ($_SESSION['accudata-max-number-of-queries'] + 1));
            if($_SESSION['accudata-max-number-of-queries'] > $block_attempts){
                $ret_array = array(
                    'error' => 'N',
                    'message' => 'Allowing to continue, too much queries!'
                );
                $ret['email'] = $ret_array;
                echo json_encode($ret);
                exit(0);
            }
            //check email
            //http://accu-query.accurateformdata.com
            $url = 'http://accu-query.accurateformdata.com/email/check_email.php?api_key='.$client_key.'&open_option='.$accept_open_domains.'&email='.$_POST['wEmail'];
            $response = file_get_contents($url);
            $response = json_decode($response);
            $_SESSION['accudata_emails_checked'][$_POST['wEmail']] = $response;
            $ret['email'] = $response;
            echo json_encode($ret);
            exit(0);
        }
    }
}

add_action('wp_ajax_accudata_check_email', 'accudata_ajax_check_email');
add_action('wp_ajax_nopriv_accudata_check_email', 'accudata_ajax_check_email');




/*function accudata_get_remote_address($address){
    $base = 'http://accu-data-query.pro-sites.org?';

    $params = array(
            'clientKey' => $client_key
            ,'wEmail' => $_POST['wEmail']
            ,'wAddress' => $_POST['wAddress']
            ,'wAcceptOpenDomains' => $accept_open_domains
            
    );

    $url = $base . http_build_query( $params );
    $result = file_get_contents( $url );
    $t = json_decode($result);
    
}*/