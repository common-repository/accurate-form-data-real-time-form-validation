    var accudata_checks = {};
    accudata_checks.emails = '';
    accudata_checks.address = '';
    
    var accudata_zip_present = false;
    var accudata_country_present = false;
    
    var tooltipser_tooltips = [];
    var tooltipsterObjects = [];
    var seen_time = 10000;
    
    
    function check_submit_enable(the_submit){
        if(accudata_checks.emails == 'invalid' || accudata_checks.address == 'invalid'){
            the_submit.prop("disabled", true);
        }else{
            the_submit.prop("disabled", false);
        }
    }
    
    jQuery(document).ready(function($){
                
        var found_email_fields;
        var found_address_fields;
        var found_zip_fields;
        var found_country_fields;
        var found_city_fields;
        //first get all the elements that match options field names
        var obj_forms_fields = {};
        var obj_current_values = {};
        
        
        // initialize the tooltipser plugin
        $.fn.tooltipster('setDefaults', {
          position: 'right',
          trigger: 'custom',
          animation: 'grow'
        });
        // $('input').tooltipster();
        
        for(var i=0; i<email_fields.length; i++){
            found_email_fields = $("input[name=" + email_fields[i] + "]");
            
            found_email_fields.each(function(){
                
                var the_field = $(this);
                the_field.tooltipster();
                
                $(this).blur(function(){
                    //check the email
                    var the_form = $(this).closest('form');
                    var the_submit = $(this).closest('form').find(':submit');
                    
                    if($(this).val() !== ''){
                        
                        // make the first tooltip - for loading/processing the validity...
                        the_field.tooltipster('destroy');
                        
                        tooltipsterObjects[i] = {};
                        tooltipsterObjects[i][email_fields[i]] = {};
                        tooltipser_tooltips[i] = {};
                        tooltipser_tooltips[i][email_fields[i]] = {};
                        
                        tooltipsterObjects[i][email_fields[i]] = the_field.tooltipster({
                            content: 'Checking E-mail validity...',
                            multiple: true,
                            theme: 'tooltipster-default',
                            timer: seen_time
                        });
                        tooltipser_tooltips[i][email_fields[i]] = tooltipsterObjects[i][email_fields[i]][0];
                        tooltipser_tooltips[i][email_fields[i]].show();
                        
                        
                        
                        //as a first measure disable submit until checks are performed
                        the_submit.prop("disabled", true);
                        var data = {
                            'action': 'accudata_check_email',
                            'wEmail': $(this).val()
                        };
                        $.post(accudata_admin_ajax, data, function(response) {
                            //console.log(response);
                            //return true;
                            var r = $.parseJSON(response);
                            var alert_msg = '';

                            if(r.email.error === '1' || r.email.error == 'Y'){
                                //set error message
                                accudata_checks.emails = 'invalid';
                                //disable submit
                                check_submit_enable(the_submit);
                                
                                the_field.tooltipster('destroy');

                                tooltipsterObjects[i][email_fields[i]] = the_field.tooltipster({
                                    content: r.email.message,
                                    multiple: true,
                                    theme: 'tooltipster-error',
                                    timer: (seen_time * 1000)
                                });
                                tooltipser_tooltips[i][email_fields[i]] = tooltipsterObjects[i][email_fields[i]][0];
                                tooltipser_tooltips[i][email_fields[i]].show();

                            }else{
                                //enable submit (if disabled)
                                accudata_checks.emails = '';
                                check_submit_enable(the_submit);
                                
                                the_field.tooltipster('destroy');
                                // hide other tooltips

                                tooltipsterObjects[i][email_fields[i]] = the_field.tooltipster({
                                    content: (typeof(r.email.message) != 'undefined' ? r.email.message : 'E-mail is Valid!'),
                                    multiple: true,
                                    theme: 'tooltipster-success',
                                    timer: seen_time
                                });
                                tooltipser_tooltips[i][email_fields[i]] = tooltipsterObjects[i][email_fields[i]][0];
                                tooltipser_tooltips[i][email_fields[i]].show();
                            }
                        });
                        
                     }else{
                         accudata_checks.emails = '';
                         try{
                            inputOpentipDark.hide();
                            inputOpentipAlert.hide();
                         }catch(e){}
                         
                         check_submit_enable(the_submit);
                     }
                });
            });
            
        }
        
        for(var i=0; i<address_fields.length; i++){
            
            found_address_fields = $("input[name=" + address_fields[i] + "]");
            found_address_fields.each(function(){
                
                var the_form = $(this).closest('form');
                
                if(the_form.prop('id') == 'undefined' || the_form.prop('id') === ''){
                    the_form.prop('id', ('form_' + $(this).prop('name') + Math.floor(Math.random()*10000)));
                }
                if(typeof(obj_forms_fields[the_form.prop('id')]) == 'undefined'){
                    obj_forms_fields[the_form.prop('id')] = {};
                }
                obj_forms_fields[the_form.prop('id')].address = $(this).prop('name');
                
                
                var the_field = $(this);
                the_field.tooltipster();
                
                $(this).blur(function(){
                    
                    var the_submit = $(this).closest('form').find(':submit');
                    var the_form = $(this).closest('form');
                    var the_form_id = the_form.prop('id');
                    var zip_val = '';
                    var country_val = '';

                    //if zip and country are present, those need to be not empty
                    if(typeof(obj_forms_fields[the_form.prop('id')].zip) != 'undefined' && 
                        typeof(obj_forms_fields[the_form.prop('id')].country) != 'undefined'){
                            
                            zip_val = $("#" + the_form_id + " input[name=" + obj_forms_fields[the_form_id].zip + "]").val();
                            country_val = $("#" + the_form_id + " [name=" + obj_forms_fields[the_form_id].country + "]").val();
                            if(zip_val === '' || country_val === ''){
                                return true;
                            }
                    }
                    
                    if($(this).val() !== ''){
                    
                        //make sure we dont have exactly the same values
                        if(typeof(obj_current_values.wAddress) != 'undefined' && obj_current_values.wAddress == $(this).val() && typeof(obj_current_values.wZip) != 'undefined' && obj_current_values.wZip == zip_val && typeof(obj_current_values.wCountry) != 'undefined' && obj_current_values.wCountry == country_val){
                            return true;
                        }
                    
                        // make the first tooltip - for loading/processing the validity...
                        the_field.tooltipster('destroy');
                        
                        tooltipsterObjects[i] = {};
                        tooltipsterObjects[i][address_fields[i]] = {};
                        tooltipser_tooltips[i] = {};
                        tooltipser_tooltips[i][address_fields[i]] = {};
                        
                        tooltipsterObjects[i][address_fields[i]] = the_field.tooltipster({
                            content: 'Checking Address validity...',
                            multiple: true,
                            theme: 'tooltipster-default',
                            timer: seen_time
                        });
                        tooltipser_tooltips[i][address_fields[i]] = tooltipsterObjects[i][address_fields[i]][0];
                        tooltipser_tooltips[i][address_fields[i]].show();
                        
                        //as a first measure disable submit until checks are performed
                        the_submit.prop("disabled", true);
                    
                        var data = {
                            'action': 'accudata_check_address',
                            'wAddress': $(this).val()
                        };
                        obj_current_values.wAddress = $(this).val();
                        //if zip and country are present, include them
                        
                        if(typeof(obj_forms_fields[the_form_id].zip) != 'undefined'){
                            data.wZip = $("#" + the_form_id + " input[name=" + obj_forms_fields[the_form_id].zip + "]").val();
                            obj_current_values.wZip = data.wZip;
                        }
                        
                        if(typeof(obj_forms_fields[the_form_id].country) != 'undefined'){
                            data.wCountry = $("#" + the_form_id + " [name=" + obj_forms_fields[the_form_id].country + "]").val();
                            obj_current_values.wCountry = data.wCountry;
                        }
                        
                        if(typeof(obj_forms_fields[the_form_id].city) != 'undefined'){
                            data.wCity = $("#" + the_form_id + " input[name=" + obj_forms_fields[the_form_id].city + "]").val();
                            obj_current_values.wCity = data.wCity;
                        }
                        
                        //console.log(data);
                                                
                        $.post(accudata_admin_ajax, data, function(response) {
                            //console.log(response);
                            //return true;
                            var r = $.parseJSON(response);
                            var alert_msg = '';

                            if(r.address.error === '1' || r.address.error == 'Y'){
                                //set error message
                                accudata_checks.address = 'invalid';
                                //disable submit
                                check_submit_enable(the_submit);
                                
                                the_field.tooltipster('destroy');

                                tooltipsterObjects[i][email_fields[i]] = the_field.tooltipster({
                                    content: r.address.message,
                                    multiple: true,
                                    theme: 'tooltipster-error',
                                    timer: (seen_time * 1000)
                                });
                                tooltipser_tooltips[i][email_fields[i]] = tooltipsterObjects[i][email_fields[i]][0];
                                tooltipser_tooltips[i][email_fields[i]].show();

                            }else{
                                //enable submit (if disabled)
                                //correct the city if everything matches but it is different from response
                                //var response_city = r['address']['city'];
                                //var the_form_city = $("#" + the_form_id + " [name=" + obj_forms_fields[the_form_id].city + "]");
                                //alert(the_form_city.val());
                                //if(response_city != '' && the_form_city.val()){
                                //    the_form_city.val(response_city);
                                //}
                                accudata_checks.address = '';
                                check_submit_enable(the_submit);
                                
                                the_field.tooltipster('destroy');
                                // hide other tooltips

                                tooltipsterObjects[i][email_fields[i]] = the_field.tooltipster({
                                    content: (typeof(r.address.message) != 'undefined' ? r.address.message : 'Address is Valid!'),
                                    multiple: true,
                                    theme: 'tooltipster-success',
                                    timer: seen_time
                                });
                                tooltipser_tooltips[i][email_fields[i]] = tooltipsterObjects[i][email_fields[i]][0];
                                tooltipser_tooltips[i][email_fields[i]].show();
                            }
                     });
                     
                   }else{
                       accudata_checks.address = '';
                        try{
                           address_inputOpentipDark.hide();
                           address_inputOpentipAlert.hide();
                        }catch(e){}

                        check_submit_enable(the_submit);
                   }
                   
                });
            });
            
        }
        
        //zip
        for(var i=0; i<zip_fields.length; i++){
            found_zip_fields = $("input:text[name=" + zip_fields[i] + "]");
            found_zip_fields.each(function(){
                var the_form = $(this).closest('form');
                if(the_form.prop('id') != 'undefined'){
                    obj_forms_fields[the_form.prop('id')].zip = $(this).prop('name');
                }
                //for the field, make it active on blur
                $(this).blur(function(){
                    var the_form = $(this).closest('form');
                    var the_form_id = the_form.prop('id');
                    $("#" + the_form_id + " input[name=" + obj_forms_fields[the_form_id].address + "]").blur();
                });
            });
        }
        
        //country
        for(var i=0; i<country_fields.length; i++){
            found_country_fields = $("[name=" + country_fields[i] + "]");
            found_country_fields.each(function(){
                var the_form = $(this).closest('form');
                if(the_form.prop('id') != 'undefined'){
                    obj_forms_fields[the_form.prop('id')].country = $(this).prop('name');
                }
                
                $(this).blur(function(){
                    var the_form = $(this).closest('form');
                    var the_form_id = the_form.prop('id');
                    $("#" + the_form_id + " input[name=" + obj_forms_fields[the_form_id].address + "]").blur();
                });
                
                $(this).change(function(){
                    var the_form = $(this).closest('form');
                    var the_form_id = the_form.prop('id');
                    $("#" + the_form_id + " input[name=" + obj_forms_fields[the_form_id].address + "]").blur();
                });
            });
        }
        
        //city
        for(var i=0; i<city_fields.length; i++){
            found_city_fields = $("input:text[name=" + city_fields[i] + "]");
            found_city_fields.each(function(){
                var the_form = $(this).closest('form');
                if(the_form.prop('id') != 'undefined'){
                    obj_forms_fields[the_form.prop('id')].city = $(this).prop('name');
                }
            });
        }
        
        // console.log(obj_forms_fields);
    });