<?php

/*

* @version 1.0.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?><?php
if (defined('MARKETKINGPRO_DIR')){
    if (intval(get_option('marketking_enable_vacation_setting', 1)) === 1){
        if(marketking()->vendor_has_panel('vacation')){
            ?>
            <div class="nk-content marketking_vacation_page">
            <div class="container-fluid">
                <?php
                if (isset($_GET['update'])){
                    $add = sanitize_text_field($_GET['update']);;
                    if ($add === 'success'){
                        ?>                                    
                        <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('Your settings have been saved successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>.</div>
                        <?php
                    }
                }
                ?>
                <div class="nk-content-inner">
                    <div class="nk-content-body">
                        <div class="nk-block">
                            <div class="card">
                                <div class="card-aside-wrap">
                                    <div class="card-inner card-inner-lg">
                                        <div class="nk-block-head nk-block-head-lg">
                                            <div class="nk-block-between">
                                                <div class="nk-block-head-content">
                                                    <h4 class="nk-block-title"><em class="icon ni ni-sun-fill"></em>&nbsp;&nbsp;<?php esc_html_e('Vacation Mode','marketking');?></h4>
                                                </div>
                                                <div class="nk-block-head-content align-self-start d-lg-none">
                                                    <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        $user_id = marketking()->get_data('user_id');
                                        $currentuser = new WP_User($user_id);
                                        
                                        $vacation_enabled = get_user_meta($user_id, 'marketking_vacation_enabled', true);
                                        $vacation_message = get_user_meta($user_id, 'marketking_vacation_message', true);
                                        if (empty($vacation_message)){
                                            $vacation_message = '';
                                        }
                                        $closingtime = get_user_meta($user_id, 'marketking_vacation_closingtime', true);
                                        if (empty($closingtime)){
                                            $closingtime = 'now';
                                        }
                                        $closingstart = get_user_meta($user_id, 'marketking_vacation_closingstart', true);
                                        $closingend = get_user_meta($user_id, 'marketking_vacation_closingend', true);

                                        ?>
                                        <div class="nk-block-content">
                                            <div class="gy-3">
                                                <div class="g-item">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" <?php checked('yes',$vacation_enabled, true); ?> id="vacationenabled">
                                                        <label class="custom-control-label" for="vacationenabled"><?php esc_html_e('Enable Vacation Mode','marketking');
                                                          ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- .nk-block-content -->
                                        <br>
                                        <div class="form-group"> 
                                            <label class="form-label"><?php esc_html_e('Closing Time','marketking');?></label>
                                            <div class="form-control-wrap" id="closingtimewrap">
                                                <select class="form-select" id="closingtime">
                                                    <option value="now" <?php selected('now', $closingtime, true);?>><?php esc_html_e('Close Now','marketking');?></option> 
                                                    <option value="dates" <?php selected('dates', $closingtime, true);?>><?php esc_html_e('Scheduled Close','marketking');?></option> 
                                                </select>
                                            </div>
                                        </div>
                                        <div id="marketking_scheduled_close_dates">
                                            <div class="form-group marketking_close_date_time"><div class="form-control-wrap"><div class="form-icon form-icon-right"><em class="icon ni ni-calendar-alt"></em></div><input type="date" class="form-control form-control-outlined date-picker" id="marketking_scheduled_close_start" value="<?php echo esc_attr($closingstart);?>"><label class="form-label-outlined" for="marketking_scheduled_close_start"><?php esc_html_e('Vacation Start Date','marketking');?></label></div></div>
                                            <div class="form-group marketking_close_date_time"><div class="form-control-wrap"><div class="form-icon form-icon-right"><em class="icon ni ni-calendar-alt"></em></div><input type="date" class="form-control form-control-outlined date-picker" id="marketking_scheduled_close_end" value="<?php echo esc_attr($closingend);?>"><label class="form-label-outlined" for="marketking_scheduled_close_end"><?php esc_html_e('Vacation End Date','marketking');?></label></div></div>
                                        </div>
                                        <div class="form-group marketking_vacation_message"><label class="form-label" for="vacationmessage"><?php esc_html_e('Set Vacation Message','marketking');?></label><div class="form-control-wrap"><textarea class="form-control form-control-sm valid" id="vacationmessage" name="vacationmessage" placeholder="<?php esc_attr_e('Enter your message','marketking');?>" required="" aria-invalid="false"><?php echo esc_html($vacation_message);?></textarea></div></div>

                                        <button class="btn btn-primary" type="submit" id="marketking_save_vacation_settings" value="<?php echo esc_attr($user_id);?>"><?php esc_html_e('Save Settings','marketking');?></button>

                                        <br><br><br>
                                        <div class="marketking_vacation_help_alert"><div class="alert alert-light alert-icon"><em class="icon ni ni-help"></em><?php esc_html_e('While in','marketking');?> <strong><?php esc_html_e('Vacation Mode','marketking');?></strong><?php esc_html_e(', your shop\'s products cannot be purchased by customers.','marketking');?></div></div>
                                    </div>
                                    <?php include(MARKETKINGCORE_DIR.'/public/dashboard/templates/profile-sidebar.php'); ?>
                                </div><!-- .card-inner -->
                            </div><!-- .card-aside-wrap -->
                        </div><!-- .nk-block -->
                    </div>
                </div>
            </div>
            </div>
            <?php
        }
    }
}