<?php
/*
 * Handle the Options Page display and form submitting
 */

if (isset($_POST['wClientKey'])) {
    $client_key = $_POST['wClientKey'];
    update_option('accudata_client_key', $client_key);

    $email_fields = $_POST['wEmailFields'];
    update_option('accudata_email_fields', $email_fields);

    $check_email_validity = $_POST['wCheckEmailValidity'];
    update_option('accudata_check_email_validity', $check_email_validity);

    $check_physical_address_validity = $_POST['wCheckPhysicalAddressValidity'];
    update_option('accudata_check_physical_address_validity', $check_physical_address_validity);

    $address_fields = $_POST['wAddressFields'];
    update_option('accudata_address_fields', $address_fields);

    $state_fields = $_POST['wStateFields'];
    update_option('accudata_state_fields', $state_fields);

    $city_fields = $_POST['wCityFields'];
    update_option('accudata_city_fields', $city_fields);

    $zip_fields = $_POST['wZipFields'];
    update_option('accudata_zip_fields', $zip_fields);

    $country_fields = $_POST['wCountryFields'];
    update_option('accudata_country_fields', $country_fields);

    $accept_open_domains = $_POST['wIncludeOpen'];
    update_option('accudata_accept_open_domains', $accept_open_domains);

    $email_block_attempts = $_POST['wEmailBlockAttempts'];
    if(empty($email_block_attempts)){
        $email_block_attempts = '2';
    }
    update_option('accudata_email_block_attempts', $email_block_attempts);

    $physical_address_block_attempts = $_POST['wPhysicalAddressBlockAttempts'];
    if(empty($physical_address_block_attempts)){
        $physical_address_block_attempts = '2';
    }
    update_option('accudata_physical_address_block_attempts', $physical_address_block_attempts);

    $block_submit_button = $_POST['wBlockSubmit'];
    update_option('accudata_block_submit', $block_submit_button);
    ?>
    <div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></div>
    <?php
} else {
    $client_key = get_option('accudata_client_key');
    $email_fields = get_option('accudata_email_fields');
    $check_email_validity = get_option('accudata_check_email_validity') != 'no' ? 'yes' : 'no';
    $check_physical_address_validity = get_option('accudata_check_physical_address_validity') != 'no' ? 'yes' : 'no';
    $address_fields = get_option('accudata_address_fields');
    $state_fields = get_option('accudata_state_fields');
    $city_fields = get_option('accudata_city_fields');
    $zip_fields = get_option('accudata_zip_fields');
    $country_fields = get_option('accudata_country_fields');
    $accept_open_domains = get_option('accudata_accept_open_domains');
    $email_block_attempts = get_option('accudata_email_block_attempts');
    $physical_address_block_attempts = get_option('accudata_physical_address_block_attempts');
    $block_submit_button = get_option('accudata_block_submit');
}
?>
<style type="text/css">
.form-field input, .form-field textarea{
    width: 100%;
}
</style>
<div class="wrap" id="accu-admin-settings">
    <?php echo "<h2>" . __('Accu Data Options Page', 'accudata_herdom') . "</h2>"; ?>
    
    <form id="accudata_admin_form" class="form-horizontal" name="accudata_admin_form" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <div class="box-wrap">
            <h3>Client Key</h3>
            <p>If you don't have a Client Key, get one free <a target="_blank" href="http://www.accurateformdata.com/register">here</a>.</p>
            <div class="form-group uninline">
                <label><label for="wClientKey"><?php echo __('Client Key', 'accudata_herdom'); ?></label></label>
                <div class="control-group">
                    <input style="max-width: 900px;" type="text" id="wClientKey" name="wClientKey" value="<?php echo $client_key; ?>"/>
                </div>
            </div>
        </div>
        
        <div class="box-wrap">
            <h3>E-mail Checking Settings</h3>
            
            <div class="form-group checkbox-group">
                <label>
                    <span class="text"><?php echo __('Check E-mail validity?', 'accudata_herdom'); ?></span> 
                    <input type="hidden" name="wCheckEmailValidity" value="no"/>
                    <input name="wCheckEmailValidity" <?php if($check_email_validity == 'yes') { echo 'checked="checked"'; } ?> value="yes" type="checkbox" class="toggable-switch" id="check_email_validity"/>
                </label>
            </div>
            
            <div class="toggable-content" style="<?php if($check_email_validity == 'no'){ echo 'display: none;'; } ?>">
                <div class="form-group checkbox-group">
                    <label>
                        <span class="text"><?php echo __('Allow Open Domains?', 'accudata_herdom'); ?></span> 
                        <input type="hidden" name="wIncludeOpen" value="no"/>
                        <input type="checkbox" id="allow_open_domains" <?php if ($accept_open_domains == '' || $accept_open_domains == 'yes') { ?>checked="checked"<?php } ?> name="wIncludeOpen" value="yes"/>
                    </label>
                </div>
                
                <p class="blue-note">
                    <?php echo __('Open Domains are domains like Yahoo.com that accepts all emails, without caring if the destination user really exists and then, after accepting, they bounce the email if the recipient does not exist in its domain.', 'accudata_herdom'); ?>
                    
                </p>
                
                <div class="form-group checkbox-group">
                    <label><span class="text"><?php echo __('The form will be prevented to submit if the E-mail provided can not be found for x attempts', 'accudata_herdom'); ?></span> <!--<input checked="checked" type="checkbox" value="email" class=""/></label>-->
                </div>
                
                <div class="form-group text-group clearfix email-failed-verifications">
                    <label><span class="text"><?php echo __('After how many failed E-mail verifications should the form be allowed to submit?', 'accudata_herdom'); ?></span> </label>
                    <div class="control-group"><input class="input-xs" type="text" type="text" name="wEmailBlockAttempts" value="<?php echo $email_block_attempts; ?>" placeholder="For Example: 5"/></div>
                </div>
                <div class="form-group text-group clearfix">
                    <label>
                        <span class="text"><?php echo __('Possible Names for the E-mail field?', 'accudata_herdom'); ?></span> 
                    </label>
                    <div class="control-group">
                        <input class="input-wide" type="text" id="wEmailFields" name="wEmailFields" value="<?php echo $email_fields; ?>" placeholder="For Example: user_email"/>
                        <span class="note-link"><a href="http://www.accurateformdata.com/wp-how" class="whats-this" target="_blank" rel="Click here to see the docs">What's this?</a></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="box-wrap">
            <h3>Physical Address Checking Settings</h3>
            <div class="form-group checkbox-group">
                <label>
                    <span class="text">Check physical address validity?</span> 
                    <input type="hidden" name="wCheckPhysicalAddressValidity" value="no"/>
                    <input name="wCheckPhysicalAddressValidity" <?php if($check_physical_address_validity == 'yes') { echo 'checked="checked"'; } ?> value="yes" type="checkbox" class="toggable-switch" id="check_physical_address_validity"/>
                </label>
            </div>
            <div class="toggable-content" style="<?php if($check_physical_address_validity == 'no') { echo 'display: none;'; } ?>">
                <p class="blue-note">
                    You understand that Physical Address verification is not 100% accurate for EVERY query. 
                    It has a good degree of accuracy in countries like US, UK, CA, etc., but even in those
                    countries where enough data is not available, the accuracy could be even lower. Please
                    test it to see if this feature serves for your purposes.
                </p>
                
                <div class="form-group checkbox-group clearfix">
                    <label><span class="text"><?php echo __('The form will be prevented to submit if the Physical Address provided can not be found for x attempts (it can be set to 0)', 'accudata_herdom'); ?></span> <!--<input checked="checked" type="checkbox" value="physical_address" class="prevent-submission-switch"/>--></label>
                </div>
                <div class="form-group text-group clearfix physical_address-failed-verifications">
                    <label><span class="text"><?php echo __('After how many failed Address verifications should the form be allowed to submit?', 'accudata_herdom'); ?></span> </label>
                    <div class="control-group"><input class="input-xs" type="text" name="wPhysicalAddressBlockAttempts" value="<?php echo $physical_address_block_attempts; ?>" placeholder="For Example: 5"/></div>
                </div>
                <div class="form-group text-group clearfix">
                    <label>
                        <span class="text"><?php echo __('Possible Names for City HTML field?', 'accudata_herdom'); ?></span> 
                    </label>
                    <div class="control-group">
                        <input class="input-wide" type="text" name="wCityFields" value="<?php echo $city_fields; ?>" placeholder="For Example: city"/>
                        <span class="note-link"><a href="http://www.accurateformdata.com/wp-how" target="_blank" class="whats-this" rel="Click here for a tutorial">What's this?</a></span>
                    </div>
                </div>
                <div class="form-group text-group clearfix">
                    <label>
                        <span class="text"><?php echo __('Possible Names for Zip HTML field?', 'accudata_herdom'); ?></span> 
                    </label>
                    <div class="control-group"><input class="input-wide" type="text" name="wZipFields" value="<?php echo $zip_fields; ?>" placeholder="For Example: zip"/></div>
                </div>
                <div class="form-group text-group clearfix">
                    <label>
                        <span class="text"><?php echo __('Possible Names for Address HTML field?', 'accudata_herdom'); ?></span> 
                    </label>
                    <div class="control-group"><input class="input-wide" type="text" name="wAddressFields" value="<?php echo $address_fields; ?>" placeholder="For Example: address"/></div>
                </div>
                <div class="form-group text-group clearfix">
                    <label>
                        <span class="text"><?php echo __('Possible Names for State HTML field?', 'accudata_herdom'); ?></span> 
                    </label>
                    <div class="control-group"><input class="input-wide" type="text" name="wStateFields" value="<?php echo $state_fields; ?>"/></div>
                </div>
                <div class="form-group text-group clearfix">
                    <label>
                        <span class="text"><?php echo __('Possible Names for Country HTML field?', 'accudata_herdom'); ?></span> 
                    </label>
                    <div class="control-group"><input class="input-wide" type="text" name="wCountryFields" value="<?php echo $country_fields; ?>" placeholder="For Example: country"/></div>
                </div>
            </div>
        </div>
        
        
        <div class="form-action">
            <input type="submit" class="button-primary" name="wSub" value="Save Changes"/>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($){
        $('.whats-this')
            .on('mouseenter', function(e){
                if($('body').find('.fliptip').length > 0) {
                    // make this visible
                    $('body').find('.fliptip').show();
                } else {
                    $('body').append('<div class="fliptip"></div>');
                }
                
                $('.fliptip').text($(this).attr('rel'));
                
                // do the math
                var tooltip_width = $('.fliptip').width();
                $('.fliptip').css({
                    left: e.pageX - (tooltip_width/2),
                    top: e.pageY - 36
                });
            })
            .on('mouseleave', function(){
                $('.fliptip').remove();
            })
            .on('mousemove', function(e){
                var tooltip_width = $('.fliptip').width();
                $('.fliptip').css({
                    left: e.pageX - (tooltip_width/2),
                    top: e.pageY - 36
                });
            })
            ;
        
        $('.prevent-submission-switch').on('change', function(){
            var target = $(this).val();
            if($(this).is(':checked')) {
                // show the toggable content
                $('.' + target + '-failed-verifications').show();
            } else {
                // hide
                $('.' + target + '-failed-verifications').hide();
            }
        });
        
        $('.toggable-switch').on('change', function(){
            console.log($(this).is(':checked'));
            if($(this).is(':checked')) {
                // show the toggable content
                $(this).parents('.box-wrap').find('.toggable-content').stop().slideDown('fast');
            } else {
                // hide
                $(this).parents('.box-wrap').find('.toggable-content').stop().slideUp('fast');
            }
        });
        
        //var obj_form_submit = $("#accudata_admin_form").find("[type=submit]");
        
        //        var radio_1 = $("input:radio[name=wMode]:checked");
        //        $(".help-div-1").hide();
        //        $("#" + radio_1.val() + "_mode").show(300);
        //        
        //        $("input:radio[name=wMode]").click(function(e){
        //            var radio_val = $(this).val();
        //            if(!$("#" + radio_val + "_mode").is(":visible")){
        //                $(".help-div-1").hide();
        //                $("#" + radio_val + "_mode").show(300);
        //            }
        //        });
    });
</script>
