    <?php
    $client_key = get_option('accudata_client_key');
    $email_fields = get_option('accudata_email_fields');
    $address_fields = get_option('accudata_address_fields');
    $state_fields = get_option('accudata_state_fields');
    $zip_fields = get_option('accudata_zip_fields');
    $city_fields = get_option('accudata_city_fields');
    $country_fields = get_option('accudata_country_fields');
    $accept_open_domains = get_option('accudata_accept_open_domains');
    $block_attempts = get_option('accudata_block_attempts');
    
    $email_fields_js_code = "var email_fields = [{fields}];" ;
    $t_arr = array();
    $t_code = '';
    $t = explode(',', $email_fields);
    foreach($t as $field){
        $field = trim($field);
        if(!in_array($field, $t_arr)){
            $t_arr[] = $field;
            $t_code .= (empty($t_code) ? "'$field'" : ", '$field'");
        }
    }
    $email_fields_js_code = str_replace('{fields}', $t_code, $email_fields_js_code);
    
    $address_fields_js_code = "var address_fields = [{fields}];" ;
    $t_arr = array();
    $t_code = '';
    $t = explode(',', $address_fields);
    foreach($t as $field){
        $field = trim($field);
        if(!in_array($field, $t_arr)){
            $t_arr[] = $field;
            $t_code .= (empty($t_code) ? "'$field'" : ", '$field'");
        }
    }
    $address_fields_js_code = str_replace('{fields}', $t_code, $address_fields_js_code);
    
    $zip_fields_js_code = "var zip_fields = [{fields}];" ;
    $t_arr = array();
    $t_code = '';
    $t = explode(',', $zip_fields);
    foreach($t as $field){
        $field = trim($field);
        if(!in_array($field, $t_arr)){
            $t_arr[] = $field;
            $t_code .= (empty($t_code) ? "'$field'" : ", '$field'");
        }
    }
    $zip_fields_js_code = str_replace('{fields}', $t_code, $zip_fields_js_code);
    
    $country_fields_js_code = "var country_fields = [{fields}];" ;
    $t_arr = array();
    $t_code = '';
    $t = explode(',', $country_fields);
    foreach($t as $field){
        $field = trim($field);
        if(!in_array($field, $t_arr)){
            $t_arr[] = $field;
            $t_code .= (empty($t_code) ? "'$field'" : ", '$field'");
        }
    }
    $country_fields_js_code = str_replace('{fields}', $t_code, $country_fields_js_code);
    
    $city_fields_js_code = "var city_fields = [{fields}];" ;
    $t_arr = array();
    $t_code = '';
    $t = explode(',', $city_fields);
    foreach($t as $field){
        $field = trim($field);
        if(!in_array($field, $t_arr)){
            $t_arr[] = $field;
            $t_code .= (empty($t_code) ? "'$field'" : ", '$field'");
        }
    }
    $city_fields_js_code = str_replace('{fields}', $t_code, $city_fields_js_code);
    
    
?>

<script>
    
            
    <?php echo $email_fields_js_code." \n\t".$address_fields_js_code." \n\t".$zip_fields_js_code." \n\t".$country_fields_js_code." \n\t".$city_fields_js_code; ?>
    
    var accudata_plugin_url = '<?php echo plugins_url('/img/', __FILE__); ?>';
    var accudata_admin_ajax = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>