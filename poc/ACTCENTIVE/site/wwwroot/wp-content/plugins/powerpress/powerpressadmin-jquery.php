<?php
	// jQuery specific functions and code go here..

function powerpress_add_blubrry_redirect($program_keyword)
{
	$Settings = powerpress_get_settings('powerpress_general');
	$RedirectURL = 'https://media.blubrry.com/'.$program_keyword;
	$NewSettings = array();
	
	// redirect1
	// redirect2
	// redirect3
	for( $x = 1; $x <= 3; $x++ )
	{
		$field = sprintf('redirect%d', $x);
		if( !empty($Settings[$field]) && stripos($Settings[$field], 'podtrac.com') === false )
			$NewSettings[$field] = '';
	}
	$NewSettings['redirect1'] = $RedirectURL.'/';
	
	if( count($NewSettings) > 0 )
		powerpress_save_settings($NewSettings);
}

function powerpress_strip_redirect_urls($url)
{
	$Settings = powerpress_get_settings('powerpress_general');
	for( $x = 1; $x <= 3; $x++ )
	{
		$field = sprintf('redirect%d', $x);
		if( !empty($Settings[$field]) )
		{
			$redirect_no_http = str_replace('http://', '', $Settings[$field]);
			if( substr($redirect_no_http, -1, 1) != '/' )
				$redirect_no_http .= '/';
			$url = str_replace($redirect_no_http, '', $url);
		}
	}
	
	return $url;
}

function powerpress_admin_jquery_init()
{
	$Settings = false; // Important, never remove this
	$Settings = get_option('powerpress_general', array());
	$creds = get_option('powerpress_creds');
	$Error = false;

	$Programs = false;
	$Step = 1;


    require_once(POWERPRESS_ABSPATH .'/powerpressadmin-auth.class.php');
    $auth = new PowerPressAuth();

	$action = (isset($_GET['action'])?$_GET['action']: (isset($_POST['action'])?$_POST['action']:false) );
	if( !$action )
		return;
	
	$DeleteFile = false;
	switch($action)
	{
	    case 'powerpress-jquery-migrate-queue': {

			check_admin_referer('powerpress-jquery-migrate-queue');
            powerpress_admin_jquery_header('powerpress/powerpressadmin_migrate.php');
            require_once( POWERPRESS_ABSPATH .'/powerpressadmin-migrate.php');
            powerpress_admin_migrate();
            powerpress_admin_jquery_footer(false);
	    }
		case 'powerpress-jquery-media-disable': {
			
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header('Uploader');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to disable this option.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-media-disable');
			
			$DisableSetting = array();
			$DisableSetting['no_media_url_folder'] = 1;
			powerpress_save_settings($DisableSetting);
			
			powerpress_admin_jquery_header( __('Select Media', 'powerpress') );
?>
<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
<p><?php echo __('Blubrry Media Hosting icon will no longer be displayed when editing posts and pages.', 'powerpress'); ?></p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
			powerpress_admin_jquery_footer();
			exit;
			
		}; // No break here, let this fall thru..

		case 'powerpress-jquery-hosting': {
		
				powerpress_admin_jquery_header( __('Blubrry Podcast Media Hosting', 'powerpress') );
				
				// Congratulations you aleady have hosting!
?>
<div style="line-height: 32px; height: 32px;">&nbsp;</div>
<iframe src="//www.blubrry.com/pp/" frameborder="0" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" style="overflow:hidden; overflow-y: hidden;" width="100%" height="480" scrolling="no" seamless="seamless"></iframe>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
				
		}; break;

		case 'powerpress-jquery-media-delete': {

			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header('Uploader');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}

			check_admin_referer('powerpress-jquery-media-delete');
			$DeleteFile = $_GET['delete'];

		}; // No break here, let this fall thru..

		case 'powerpress-jquery-media': {
			
			$QuotaData = false;

			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header( __('Select Media', 'powerpress') );
?>
<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
<p><?php echo __('You do not have sufficient permission to manage options.', 'powerpress'); ?></p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}

			if( (empty($Settings['blubrry_auth']) && !$creds) || empty($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] === 'false' )
			{
				powerpress_admin_jquery_header( __('Select Media', 'powerpress') );
?>
<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
<p><?php echo __('Wait a sec! This feature is only available to Blubrry Media Podcast Hosting customers.', 'powerpress'); ?></p>
<iframe src="//www.blubrry.com/pp/" frameborder="0" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" style="overflow:hidden; overflow-y: hidden;" width="100%" height="480" scrolling="no" seamless="seamless"></iframe>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}
            if(empty($Settings['network_mode'])) {
                $Settings['network_mode'] = '0';
            }
			$Msg = false;
            $blubrryProgramKeyword =  $Settings['blubrry_program_keyword'];
            $defaultPodcast = get_user_meta(get_current_user_id(), 'pp_default_podcast', true);

            if(!empty($defaultPodcast) && $Settings['network_mode'] == '1') {
                $blubrryProgramKeyword = $defaultPodcast;
            }
            if(!empty($_GET['blubrryProgramKeyword'])) {
                $blubrryProgramKeyword = $_GET['blubrryProgramKeyword'];
                // if they have no default program (an option left over from older versions of powerpress), set one
                if( empty($defaultPodcast) || $defaultPodcast == '!nodefault' ) {
                    update_user_meta(get_current_user_id(), 'pp_default_podcast', $blubrryProgramKeyword);
                }
            }
            if( !empty($_GET['blubrryProgramKeyword']) && !empty($_GET['remSel']) &&  $_GET['remSel'] == 'true' ) {
                update_user_meta(get_current_user_id(), 'pp_default_podcast', $blubrryProgramKeyword);
            }
			if( $DeleteFile )
			{
				$json_data = false;
				$results = array();
				$api_url_array = powerpress_get_api_array();
				if ($creds) {
                    $accessToken = powerpress_getAccessToken();
                    $req_url = sprintf('/2/media/%s/%s?format=json', $Settings['blubrry_program_keyword'], $DeleteFile);
                    $req_url = sprintf('/2/media/%s/%s?format=json', $blubrryProgramKeyword, $DeleteFile);
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                    $results = $auth->api($accessToken, $req_url, false, 'DELETE');
                } else {
                    foreach ($api_url_array as $index => $api_url) {
                        $req_url = sprintf('%s/media/%s/%s?format=json', rtrim($api_url, '/'), $Settings['blubrry_program_keyword'], $DeleteFile);
                        $req_url = sprintf('%s/media/%s/%s?format=json', rtrim($api_url, '/'), $blubrryProgramKeyword, $DeleteFile);
                        $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 10, 'DELETE');
                        if (!$json_data && $api_url == 'https://api.blubrry.com/') { // Lets force cURL and see if that helps...
                            $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 10, 'DELETE', true); // Only give this 2 seconds to return results
                        }
                        if ($json_data != false)
                            break;
                    }
                    $results = powerpress_json_decode($json_data);
                }

				if( isset($results['text']) )
					$Msg = $results['text'];
				else if( isset($results['error']) )
					$Msg = $results['error'];
				else
					$Msg = __('An unknown error occurred deleting media file.', 'powerpress');
			}

			$json_data = false;
			$json_data_programs = false;
			$api_url_array = powerpress_get_api_array();
			if (is_plugin_active('powerpress-hosting/powerpress-hosting.php')) {
			    $website_detection_string = "&wp_blubrry_hosted=true";
			} else {
			    $website_detection_string = "&wp_admin_url=" . urlencode(admin_url());
			}
            if ($creds) {
                $accessToken = powerpress_getAccessToken();
                $req_url = sprintf('/2/media/%s/index.json?quota=true%s&published=true&detail=true&cache=' . md5( rand(0, 999) . time() ), $blubrryProgramKeyword, $website_detection_string);
                $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                $req_url_programs = sprintf('/2/service/index.json?cache=' . md5( rand(0, 999) . time() ));
                $req_url_programs .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                $results = $auth->api($accessToken, $req_url);
                $results_programs = $auth->api($accessToken, $req_url_programs);
            } else {
                foreach ($api_url_array as $index => $api_url) {
                    $req_url = sprintf('%s/media/%s/index.json?quota=true%s&published=true&detail=true&cache=' . md5( rand(0, 999) . time() ), rtrim($api_url, '/'), $blubrryProgramKeyword, $website_detection_string);
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                    $req_url_programs = sprintf('%s/service/index.json?cache=' . md5( rand(0, 999) . time() ), rtrim($api_url, '/'));
                    $req_url_programs .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                    $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth']);
                    $json_data_programs = powerpress_remote_fopen($req_url_programs, $Settings['blubrry_auth']);
                    if (!$json_data && $api_url == 'https://api.blubrry.com/') { // Lets force cURL and see if that helps...
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
                    } else if (!$json_data_programs && $api_url == 'https://api.blubrry.com/') {
                        $json_data_programs = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
                    }
                    if ($json_data != false)
                        break;
                }
                $results =  powerpress_json_decode($json_data);
                $results_programs = powerpress_json_decode($json_data_programs);
            }

            if( isset($results_programs['error']) )
            {
                $Error = $results_programs['error'];
                if( strstr($Error, __('currently not available', 'powerpress') ) )
                {
                    $Error = __('Unable to find podcasts for this account.', 'powerpress');
                    $Error .= '<br /><span style="font-weight: normal; font-size: 12px;">';


                    $Error .= 'Verify that the email address you enter here matches the email address you used when you listed your podcast on blubrry.com.</span>';
                }
                else if( preg_match('/No programs found.*media hosting/i', $results_programs['error']) )
                {
                    $Error .= '<br/><span style="font-weight: normal; font-size: 12px;">';
                    $Error .= 'Service may take a few minutes to activate.</span>';
                }
            }
            else if( !is_array($results_programs) )
            {
                $Error = $json_data_programs;
            }
            else {
                // Get all the programs for this user...
                foreach ($results_programs as $null => $row) {
                    $Programs[$row['program_keyword']] = $row['program_title'];
                }
            }
			powerpress_admin_jquery_header( __('Select Media', 'powerpress'), true );
            if( $Error ) {
                powerpress_page_message_add_notice($Error, 'inline', false);
				powerpress_page_message_print();
            } else {
			$FeedSlug = sanitize_title($_GET['podcast-feed']);
?>
<style>
.error {
	padding:  6px 10px;
	border 1px solid #ffc599;
	background-color: #ff9800;
	color: #fff;
	font-weight: bold;
}
</style>
<script language="JavaScript" type="text/javascript"><!--

window.addEventListener('message', function(event) {
    <?php
    if (defined('POWERPRESS_BLUBRRY_API_URL')) {
        $origin_array = explode('.', POWERPRESS_BLUBRRY_API_URL);
        $origin_array[0] = 'publish';
        $desired_origin = implode('.', $origin_array);
        $desired_origin = rtrim($desired_origin, '/');
    } else {
        $desired_origin = 'publish.blubrry.com';
    }
    ?>
    let event_origin_host = event.origin.replace('https://', '');
    event_origin_host = event_origin_host.replace('http://', '');
      if(event_origin_host === '<?php echo $desired_origin; ?>')
      {
          if (event.data.message.includes("FILE: ")) {
              let file = event.data.message.replace("FILE: ", "");
              if (event.data.auphonicUuid === undefined || event.data.auphonicUuid == '') {
                  // no mastered media, show completed message, close uploader iframe, select media, return to edit page
                  setTimeout(() => {
                      tb_remove();
                      SelectMedia(file);
                  }, 1000);
              } else {
                  // leave the mastered media uploader message for 5seconds before closing and reloading media list
                  setTimeout(() => {
                      tb_remove();
                      this.location.reload();
                  }, 5000);

              }
          } else if (event.data.message.includes("CLOSE")) {
              tb_remove();
          }
      }
    }, false);

function SelectMedia(File)
{
    <?php
        // this function is only called if a blubrry_program_keyword is set
		$fullMediaURL = 'https://media.blubrry.com/'.$Settings['blubrry_program_keyword'].'/content.blubrry.com/'.$Settings['blubrry_program_keyword'].'/';
     ?>
	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').value='<?php echo $fullMediaURL; ?>' + File;
	self.parent.document.getElementById('powerpress_url_display_<?php echo $FeedSlug; ?>').value=File;
	self.parent.document.getElementById('powerpress_hosting_<?php echo $FeedSlug; ?>').value='1';
	self.parent.document.getElementById('powerpress_url_display_<?php echo $FeedSlug; ?>').readOnly=true;
	self.parent.document.getElementById('powerpress_hosting_note_<?php echo $FeedSlug; ?>').style.display='block';
	self.parent.document.getElementById('powerpress_program_keyword_<?php echo $FeedSlug; ?>').value= document.querySelector('#blubrry_program_keyword').value;
	// verify automatically to get around an odd bug in the info_media endpoint which only happens on publish
	let verify_button = self.parent.document.getElementById('continue-to-episode-settings-<?php echo $FeedSlug; ?>');
	if (verify_button.onclick) {
       verify_button.onclick();
    } else if (verify_button.click) {
       verify_button.click();
    }
	if( self.parent.powerpress_update_for_video )
		self.parent.powerpress_update_for_video(File, '<?php echo $FeedSlug; ?>');
	self.parent.tb_remove();
}
function SelectURL(url)
{
	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').value=url;
	self.parent.document.getElementById('powerpress_hosting_<?php echo $FeedSlug; ?>').value='0';
	self.parent.document.getElementById('powerpress_url_display_<?php echo $FeedSlug; ?>').readOnly=false;
	self.parent.document.getElementById('powerpress_url_display_<?php echo $FeedSlug; ?>').value=url;
	self.parent.document.getElementById('powerpress_hosting_note_<?php echo $FeedSlug; ?>').style.display='none';
	self.parent.document.getElementById('powerpress_program_keyword_<?php echo $FeedSlug; ?>').value= document.querySelector('#blubrry_program_keyword').value;
	if( self.parent.powerpress_update_for_video )
		self.parent.powerpress_update_for_video(url, '<?php echo $FeedSlug; ?>');
	self.parent.tb_remove();
}
function DeleteMedia(File)
{
	return confirm('<?php echo __('Delete', 'powerpress'); ?>: '+File+'\n\n<?php echo __('Are you sure you want to delete this media file?', 'powerpress'); ?>');
}

window.onload = function() {
    const program = document.querySelector('#blubrry_program_keyword');
    const remember = document.querySelector('#remember_selection');
    function reloadFrame() {
        window.location = "<?php echo admin_url(); ?>?action=powerpress-jquery-media&blubrryProgramKeyword="+ program.value +"&podcast-feed=<?php echo $FeedSlug; ?>&KeepThis=true&TB_iframe=true&modal=false&remSel=" + remember.checked;
    }
    if (program) {
        program.addEventListener('change', function () {
            reloadFrame();
        });
    }
    if (remember) {
        remember.addEventListener('change', function () {
            reloadFrame();
        });
    }
}
//-->
</script>
		<div id="media-header">
            <?php
            if (count($Programs) > 1 && $Settings['network_mode'] == '1') {
                ?>
                <h2><?php echo __('Select Program', 'powerpress'); ?></h2>
                <span>
                <select id="blubrry_program_keyword" name="Settings[blubrry_program_keyword]">
                    <option value="!selectPodcast"><?php echo __('Select Program', 'powerpress'); ?></option>
                    <?php
                    ksort($Programs);
                    foreach ($Programs as $value => $desc)
                        echo "\t<option value=\"$value\"" . ($blubrryProgramKeyword == $value ? ' selected' : '') . ">$desc</option>\n";
                    ?>
                </select>
                <span <?php echo $blubrryProgramKeyword == '!selectPodcast' ? 'style="visibility: hidden;"' : null ?>>
                    <input type="checkbox" id="remember_selection"
                           name="remember_selection">
                    <label for="remember_selection">Remember Selection</label>
                </span>
            </span>
                <?php
            } else {
			?>
			<input type="hidden" name="Settings[blubrry_program_keyword]" id="blubrry_program_keyword" value="<?php echo htmlspecialchars($blubrryProgramKeyword); ?>" />
			<?php
			}
            ?>
			<h2><?php echo __('Select Media', 'powerpress') ?> <?php echo ( $blubrryProgramKeyword != '!selectPodcast' &&  $blubrryProgramKeyword != '!nodefault') ? '- '. $Programs[$blubrryProgramKeyword] : null ?></h2>
			<?php
				if(  !empty($results['quota']['expires'] ) )
				{
					$message = '';
					if( !empty($results['quota']['expires']['expired']) )
					{
						$message = '<p>'. sprintf( __('Media hosting service expired on %s.', 'powerpress'), esc_attr($results['quota']['expires']['readable_date'])) . '</p>';
					}
					else
					{
						$message = '<p>'. sprintf( __('Media hosting service will expire on %s.', 'powerpress'), esc_attr($results['quota']['expires']['readable_date'])) . '</p>';
					}
					
					$message .= '<p style="text-align: center;"><strong><a href="'. $results['quota']['expires']['renew_link'] .'" target="_blank" style="text-decoration: underline;">'. __('Renew Media Hosting Service', 'powerpress') . '</a></strong></p>';
					powerpress_page_message_add_notice( $message, 'inline', false );
					powerpress_page_message_print();
				}
				else if($blubrryProgramKeyword == '!selectPodcast') {
                    $message = '<p style="text-align: center;"><strong>Please Select A Program</strong></p>';
                    powerpress_page_message_add_notice( $message, 'inline', false );
                    powerpress_page_message_print();
                }
				else if( empty($results) )
				{
					// Handle the error here.
					$message = '<h3>'.__('Error', 'powerpress') . '</h3>';
					global $g_powerpress_remote_error, $g_powerpress_remote_errorno;
					if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )
						$message .= '<p>'. __('Incorrect sign-in email address or password.', 'powerpress').'</p><p>'.__('Verify your account entered under Services and Statistics settings then try again.', 'powerpress') .'</p>';
					else if( !empty($g_powerpress_remote_error) )
						$message .= '<p>'.$g_powerpress_remote_error.'</p>';
					else
						$message .= '<p>'.__('Unable to connect to service.','powerpress').'</p>';
			
					// Print an erro here
					powerpress_page_message_add_notice( $message, 'inline', false );
					powerpress_page_message_print();
				}
				
				if( $Msg )
				echo '<p>'. $Msg . '</p>';
                if($blubrryProgramKeyword != '!selectPodcast') {
            ?>
            <div class="media-upload-link"><a
                        title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>"
                        href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-upload", 'powerpress-jquery-upload'); ?>&blubrryProgramKeyword=<?php echo $blubrryProgramKeyword ?>&podcast-feed=<?php echo $FeedSlug; ?>&keepThis=true&TB_iframe=true&height=460&width=530&modal=false"
                        class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a></div>
            <p><?php echo __('Select from media files uploaded to blubrry.com', 'powerpress'); ?>:</p>
        </div>
            <div id="media-items-container">
                <div id="media-items">
                    <?php

                    if (isset($results['error'])) {
                        echo '<p class="error">' . $results['error'] . '</p>';
                        echo '<p><a href="https://publish.blubrry.com/services/" target="_blank">' . __('Manage your blubrry.com Account', 'powerpress') . '</a></p>';
                    } else if (is_array($results)) {
                        $PublishedList = false;
                        foreach ($results as $index => $data) {
                            if ($index === 'quota') {
                                $QuotaData = $data;
                                continue;
                            }
                            if ($index === 'detail') {
                                continue;
                            }

                            if ($PublishedList == false && !empty($data['published'])) {
                                ?>
                                <div id="media-published-title">
                                    <?php echo __('Last 20 Published media files', 'powerpress'); ?>:
                                </div>
                                <?php
                                $PublishedList = true;
                            }

                            ?>
                            <div class="media-item <?php echo(empty($data['published']) ? 'media-unpublished' : 'media-published'); ?>">
                                <strong class="media-name"><?php echo !empty($data['name']) ? htmlspecialchars($data['name']) : ''; ?></strong>
                                <cite><?php echo !empty($data['length']) ? powerpress_byte_size($data['length']) : ''; ?></cite>

                                <?php if (!empty($data['masterMedia'])): ?>
                                    <cite> - <?php echo $data['masterMedia']['status_msg']; ?></cite>
                                <?php endif; ?>

                                <?php if (!empty($data['published'])) { ?>
                                    <div class="media-published-date">
                                        &middot; <?php echo __('Published on', 'powerpress'); ?> <?php echo date(get_option('date_format'), $data['last_modified']); ?></div>
                                <?php } ?>

                                <div class="media-item-links">
                                    <!-- Published Media -->
                                    <?php if (!empty($data['published']) && !empty($data['url'])) { ?>
                                        <a href="#"
                                           onclick="SelectURL('<?php echo esc_js($data['url']); ?>'); return false;"><?php echo __('Select', 'powerpress'); ?></a>
                                    <?php } else { ?>
                                    <!-- Unpublished Media -->
                                        <?php if (function_exists('curl_init')) { ?>
                                            <a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-media-delete", 'powerpress-jquery-media-delete'); ?>&amp;podcast-feed=<?php echo $FeedSlug; ?>&amp;blubrryProgramKeyword=<?php echo urlencode($blubrryProgramKeyword); ?>&amp;delete=<?php echo !empty($data['name']) ? urlencode($data['name']) : ''; ?>"
                                               onclick="return DeleteMedia('<?php echo !empty($data['name']) ? htmlspecialchars($data['name']) : ''; ?>');"><?php echo __('Delete', 'powerpress'); ?></a> |
                                        <?php }

                                        $hideSelect = (!empty($data['masterMedia']) && $data['masterMedia']['status'] != 24);
                                        $name = !empty($data['name']) ? htmlspecialchars($data['name']) : '';
                                        $title = "Select file $name";
                                        if (!$hideSelect) {
                                            $onClick = "SelectMedia('$name'); return false;";
                                        } else {
                                            $onClick = '';
                                            $title = "Media not ready for publishing.";
                                        } ?>

                                        <a
                                        <?php echo $hideSelect  ? 'disabled' : 'href="#"'; ?>
                                        title="<?php echo htmlspecialchars($title); ?>"
                                           onclick="<?php echo $onClick; ?>"><?php echo __('Select', 'powerpress'); ?>
                                        </a>
                                    <?php } ?>

                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div><!-- end media-items -->
            </div><!-- end media-items-container -->
            <div id="media-footer">
                <div class="media-upload-link"><a
                            title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>"
                            href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-upload", 'powerpress-jquery-upload'); ?>&blubrryProgramKeyword=<?php echo $blubrryProgramKeyword ?>&podcast-feed=<?php echo $FeedSlug; ?>&keepThis=true&TB_iframe=true&height=350&width=530&modal=false"
                            class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a></div>
                <?php
                }
		if( $QuotaData ) {
					
			$NextDate = strtotime( $QuotaData['published']['next_date']);
		?>
			<?php
			if( $QuotaData['unpublished']['available'] != $QuotaData['unpublished']['total'] )
			{
				//echo '<p>';
				//echo sprintf( __('You have uploaded %s (%s available) of your %s upload limit.', 'powerpress'),
				//	'<em>'. powerpress_byte_size($QuotaData['unpublished']['used']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['unpublished']['available']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['unpublished']['total']) .'</em>' );
				//echo '</p>';
			}
			
			if( $QuotaData['published']['status'] == 'OK' )
			{
			?>
			<p><?php
			
			if( $QuotaData['published']['available'] > 0 ) // != $QuotaData['published']['total'] )
			{
				echo sprintf( __('Publishing space available: %s of (%s %%) of %s/month quota.', 'powerpress'),
					'<em>'. powerpress_byte_size($QuotaData['published']['available']) .'</em>',
					//'<em>'. powerpress_byte_size($QuotaData['published']['total']-$QuotaData['published']['available']) .'</em>',
					'<em>'. round( ($QuotaData['published']['available']/$QuotaData['published']['total'])*100 ) .'</em>',
					'<em>'. str_replace('.0', '', powerpress_byte_size($QuotaData['published']['total'])) .'</em>' );
				//echo sprintf( __('You have %s available (%s published in the last 30 days) of your %s publish limit.', 'powerpress'),
				//	'<em>'. powerpress_byte_size($QuotaData['published']['available']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['published']['total']-$QuotaData['published']['available']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['published']['total']) .'</em>' );
			}
			else if( $QuotaData['published']['available'] == 0 ) // Hosting account frozen
			{
			
			}
			else
			{
				echo sprintf( __('You have %s publish space available.', 'powerpress'),
					'<em>'. powerpress_byte_size($QuotaData['published']['total']) .'</em>' );
			}
			?>
			</p>
			<p><?php
			if( $QuotaData['published']['available'] != $QuotaData['published']['total'] )
			{
			echo sprintf( __('Your quota will reset on %s.', 'powerpress'),
				date('m/d/Y', $NextDate)  );
			}
			?>
			</p>
			<?php
				}
				else if( $QuotaData['published']['status'] == 'UNLIMITED' )
				{
					echo '<p>';
					echo __('Publishing Space Available: Unlimited (Professional Hosting)', 'powerpress');
					echo '<p>';
				}
				else
				{
					echo '<p>';
					echo __('Publishing Space Available: Account has expired', 'powerpress');
					echo '<p>';
				}
			?>
		<?php }
		}?>
		<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
	</div>
	
<?php	
			powerpress_admin_jquery_footer(true);
			exit;
		}; break;
        case 'powerpress-jquery-account-verify': {

            if( !current_user_can(POWERPRESS_CAPABILITY_MANAGE_OPTIONS) )
            {
                powerpress_admin_jquery_header('Blubrry Services', 'powerpress');
                powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );
                powerpress_page_message_print();
                powerpress_admin_jquery_footer();
                exit;
            }
            check_admin_referer('powerpress-jquery-account-verify');

            if (isset($_GET['logout']) && $_GET['logout']) {
				if ($creds) {
				    $accessToken = powerpress_getAccessToken();
                    $auth->revokeClient($accessToken, $creds['client_id'], $creds['client_secret']);
                    delete_option('powerpress_creds');
                }
				?>
                <script>
                    jQuery(document).ready(function() {
                        window.parent.tb_remove();
                        parent.location.reload(1);
                    });
                </script>
                 <?php
                exit;
            }

            powerpress_admin_jquery_account_header( __('Blubrry Services', 'powerpress'), false, true );

            if (isset($_GET['email']) && $_GET['email']) {
                $result = $auth->reSendVerifyEmail();
                powerpress_page_message_add_notice(__('Email re-sent.', 'powerpress'));
                powerpress_page_message_print();
            }

            if (isset($_GET['api']) && $_GET['api']) {
                $result = $auth->checkAccountVerified();
                if (isset($result['account_enabled']) && isset($result['account_confirmed'])) {
                    if (!$result['account_enabled'] || !$result['account_confirmed']) {
                        powerpress_page_message_add_error(__('Account not verified.', 'powerpress'));
                        powerpress_page_message_print();
                    } else {
                        //If the account wasn't verified until now, we create a program
                        if (!$creds['account_verified']) {
                            $FeedSettings = get_option('powerpress_feed_podcast', array());
                            $PostVars = [
                                'title' => $FeedSettings['title'],
                                'explicit' => $FeedSettings['itunes_explicit'],
                                'apple category' => $FeedSettings['apple_cat_1'],
                                'feed_url' => get_home_url() . '/feed/podcast'
                            ];
                            $accessToken = powerpress_getAccessToken();
                            $req_url = '/2/show/add-powerpress.json';
                            $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '?' . POWERPRESS_BLUBRRY_API_QSA : '');
                            //try giving this request 30 seconds instead of 15. it times out but if we don't catch that error, everything behaves as should
                            $auth->api($accessToken, $req_url, $PostVars, false, 30);
                        }
                        $creds['account_verified'] = true;
                        powerpress_save_settings($creds, 'powerpress_creds');

                        // If we're in the onboarding section, we want to close the popup
                        if (isset($_GET['no_signout_link']) && $_GET['no_signout_link'] == 'true') {
                            ?>
                            <script>
                                jQuery(document).ready(function() {
                                    window.parent.tb_remove();
                                    parent.location.reload(1);
                                })
                            </script>
                    <?php
                        } else {
                            //If we're somewhere else in settings, we want the popup to redirect to account-edit
                            $link_action_url = admin_url('admin.php?action=powerpress-jquery-account-edit');
                            $link_action = 'powerpress-jquery-account-edit';
                            //header("Location: " . wp_nonce_url($link_action_url, $link_action));

                            echo '<script>window.location.href = "' . wp_nonce_url($link_action_url, $link_action) . '";</script>';

                        }

                    exit;
                    }
                } else {
                    powerpress_page_message_add_error(__('Error verifying account: ', 'powerpress') . ' ' . $auth->GetLastError());
                    powerpress_page_message_print();
                }
            }

            $no_signout_link = "";
            if (isset($_GET['no_signout_link']) && $_GET['no_signout_link'] == 'true') {
                $no_signout_link = "&amp;no_signout_link=true";
            }
            $action_url = admin_url('admin.php?action=powerpress-jquery-account-verify');
            $action = 'powerpress-jquery-account-verify';
            $email_url = wp_nonce_url($action_url, $action) . '&amp;email=true' . $no_signout_link;
            $verify_url = wp_nonce_url($action_url, $action) . '&amp;api=true' . $no_signout_link;
            $logout_url = wp_nonce_url($action_url, $action) . '&amp;logout=true' . $no_signout_link;
            //header("Location: " . wp_nonce_url($link_action_url, $link_action));
            //powerpress_admin_jquery_account_header(__('Verify Blubrry Services', 'powerpress'), false, true);
            ?>
            <script>
                function clickAndDisable(evt) {
                    // disable subsequent clicks
                    //evt.preventDefault();
                    let links = document.getElementsByClassName("verify-api-call");
                    for (let link of links){
                        link.onclick = function(event) {
                            event.preventDefault();
                        };
                    }
                }
            </script>
            <div class="pp-account-verify-container">
                <h3><?php echo __('Blubrry Account Not Yet Verified', 'powerpress'); ?></h3>
                <p><?php echo __('Please check your email for a link to verify your account.', 'powerpress'); ?></p>
                <a href="<?php echo $verify_url; ?>" class="verify-api-call" onclick="clickAndDisable(event)"><?php echo __('I already verified','powerpress'); ?></a>
                <a href="<?php echo $email_url; ?>" class="verify-api-call" onclick="clickAndDisable(event)"><?php echo __('Re-send email','powerpress'); ?></a>
                <?php if (!isset($_GET['no_signout_link']) || $_GET['no_signout_link'] == 'false') { ?>
                <a class="account-verify-signout" href="<?php echo $logout_url; ?>"><?php echo __('Un-link Blubrry account','powerpress'); ?></a>
                <?php } ?>
            </div>
            <?php
            exit;
            break;
        }
		case 'powerpress-jquery-account-save': {

			if( !current_user_can(POWERPRESS_CAPABILITY_MANAGE_OPTIONS) )
			{
				powerpress_admin_jquery_header('Blubrry Services', 'powerpress');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}

            check_admin_referer('powerpress-jquery-account-save');

			$SaveSettings = isset($_POST['Settings']) ? $_POST['Settings'] : array();
			$SaveSettings = powerpress_stripslashes($SaveSettings);

			$Save = true;
			$Close = true;
		
			
			if( !empty($_POST['Remove']) || !empty($_GET['remove']) )
			{
				$SaveSettings['blubrry_username'] = '';
				$SaveSettings['blubrry_auth'] = '';
				$SaveSettings['blubrry_program_keyword'] = '';
				$SaveSettings['blubrry_hosting'] = false;
				if ($creds) {
				    $accessToken = powerpress_getAccessToken();
                    $auth->revokeClient($accessToken, $creds['client_id'], $creds['client_secret']);
                    delete_option('powerpress_creds');
                }
				$Close = true;
				$Save = true;
			} else {
                $Programs = array();
                $ProgramHosting = array();
                $json_data = false;
                $results = array();
                if ($creds) {
                    $accessToken = powerpress_getAccessToken();
                    $req_url = '/2/service/index.json?cache=' . md5( rand(0, 999) . time() );
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                    $results = $auth->api($accessToken, $req_url);
                } else {
                    $api_url_array = powerpress_get_api_array();
                    foreach( $api_url_array as $index => $api_url )
                    {
                        $req_url = sprintf('%s/service/index.json?cache=' . md5( rand(0, 999) . time() ), rtrim($api_url, '/') );
                        $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth']);
                        if( !$json_data && $api_url == 'https://api.blubrry.com/' ) { // Lets force cURL and see if that helps...
                            $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
                        }
                        if( $json_data != false )
                            break;
                    }
                    if( $json_data ) {
					    $results =  powerpress_json_decode($json_data);
					}
                }
                if ($results) {
                    if( isset($results['error']) )
                    {
                        $Error = $results['error'];
                        if( strstr($Error, __('currently not available', 'powerpress') ) )
                        {
                            $Error = __('Unable to find podcasts for this account.', 'powerpress');
                            $Error .= '<br /><span style="font-weight: normal; font-size: 12px;">';
                            $Error .= 'Verify that the email address you enter here matches the email address you used when you listed your podcast on blubrry.com.</span>';
                        }
                        else if( preg_match('/No programs found.*media hosting/i', $results['error']) )
                        {
                            $Error .= '<br/><span style="font-weight: normal; font-size: 12px;">';
                            $Error .= 'Service may take a few minutes to activate.</span>';
                        }
                    }
                    else if( !is_array($results) )
                    {
                        $Error = $json_data;
                    }
                    else
                    {
                        // Get all the programs for this user...
                        foreach( $results as $null => $row )
                        {
                            $Programs[ $row['program_keyword'] ] = $row['program_title'];
                            if( $row['hosting'] === true || $row['hosting'] == 'true' )
                                $ProgramHosting[ $row['program_keyword'] ] = true;
                            else
                                $ProgramHosting[ $row['program_keyword'] ] = false;
                        }

                        if( count($Programs) > 0 )
                        {
                            if( !empty($SaveSettings['blubrry_program_keyword']) )
                            {
                                if ($SaveSettings['blubrry_program_keyword'] != 'no_default') {
                                    powerpress_add_blubrry_redirect($SaveSettings['blubrry_program_keyword']);
                                    $SaveSettings['blubrry_hosting'] = $ProgramHosting[ $SaveSettings['blubrry_program_keyword'] ];
                                    if( !is_bool($SaveSettings['blubrry_hosting']) )
                                    {
                                        if( $SaveSettings['blubrry_hosting'] === 'false' || empty($SaveSettings['blubrry_hosting']) )
                                            $SaveSettings['blubrry_hosting'] = false;
                                    }
                                }

                                $Save = true;
                                $Close = true;
                            }
                            else if( isset($SaveSettings['blubrry_program_keyword']) ) // Present but empty
                            {
                                $Error = __('You must select a program to continue.', 'powerpress');
                            }
                            else if( count($Programs) == 1 )
                            {
                                foreach( $Programs as $keyword => $title ) {
                                    break;
                                }

                                $SaveSettings['blubrry_program_keyword'] = $keyword;
                                $SaveSettings['blubrry_hosting'] = $ProgramHosting[ $keyword ];
                                if( !is_bool($SaveSettings['blubrry_hosting']) )
                                {
                                    if( $SaveSettings['blubrry_hosting'] === 'false' || empty($SaveSettings['blubrry_hosting']) )
                                        $SaveSettings['blubrry_hosting'] = false;
                                }
                                powerpress_add_blubrry_redirect($keyword);
                                $Close = true;
                                $Save = true;
                            }
                            else
                            {
                                $Error = __('Error: No podcast program selected.', 'powerpress');
                            }
                        }
                        else
                        {
                            $Error = __('No podcasts for this account are listed on blubrry.com.', 'powerpress');
                        }
                    }
                }
                else
                {
                    global $g_powerpress_remote_error, $g_powerpress_remote_errorno;
                    //$Error = '<h3>'. __('Error', 'powerpress') .'</h3>';
                    if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )
                        $Error .= '<p>'. __('Incorrect sign-in email address or password.', 'powerpress') .'</p><p>'. __('Verify your account settings then try again.', 'powerpress') .'</p>';
                    else if( !empty($g_powerpress_remote_error) )
                        $Error .= '<p>'.$g_powerpress_remote_error .'</p>';
                    else
                        $Error .= '<p>'.__('Authentication failed.', 'powerpress') .'</p>';
                }

                if( $Error )
                {
                    $Error .= '<p style="text-align: center;"><a href="https://blubrry.com/support/powerpress-documentation/services-stats/" target="_blank">'. __('Click Here For Help','powerpress') .'</a></p>';
                }
            }

			
			if( $Save ) {
                $SaveSettings['network_mode'] = (isset($SaveSettings['network_mode'])) ? 1 : 0;
                powerpress_save_settings($SaveSettings);
            }
			
			// Clear cached statistics
			delete_option('powerpress_stats');
			
			if( $Error )
				powerpress_page_message_add_notice( $Error, 'inline', false );
				
			if( $Close )
			{
			    $next_top_url = admin_url("admin.php?page=powerpressadmin_onboarding.php&step=createEpisode");
			    ?>
			    <script type="text/javascript">
			        let closeClick;
			        if (window.top.location.href.includes('powerpressadmin_onboarding')) {
			            closeClick = function() {
			                window.top.location.href = "<?php echo $next_top_url; ?>";
			                self.parent.tb_remove();
			                return false;
			            };
			        } else {
			            closeClick = function() {
			                self.parent.tb_remove();
			                return false;
			            };
			        }
                </script>
			     <?php
			    $admin_url =  admin_url("admin.php?page=powerpressadmin_basic");
				powerpress_admin_jquery_account_header( __('Blubrry Services', 'powerpress') );
				powerpress_page_message_print();
?>
<p style="display: none; text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding:0;"><a href="#" onclick="self.parent.tb_remove(); return false;" title="<?php echo __('Close', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" alt="<?php echo __('Close', 'powerpress'); ?>" /></a></p>
<h2><?php echo __('Blubrry Services', 'powerpress'); ?></h2>
<p style="text-align: center;"><strong><?php echo __('Settings Saved Successfully!', 'powerpress'); ?></strong></p>
<p style="text-align: center;">
	<a href="<?php echo $admin_url; ?>" onclick="closeClick(); return false;" target="_top"><?php echo __('Close', 'powerpress'); ?></a>
</p>
<script type="text/javascript"><!--

jQuery(document).ready(function($) {
	// Upload loading, check the parent window for #blubrry_stats_settings div
	if( jQuery('#blubrry_stats_settings',parent.document).length )
	{
		jQuery('#blubrry_stats_settings',parent.document).html('');
	}
});

// --></script>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}
			
            break;
		}
        case 'powerpress-jquery-account-edit':
        {
            if( !current_user_can(POWERPRESS_CAPABILITY_MANAGE_OPTIONS) )
            {
                powerpress_admin_jquery_header( __('Blubrry Services', 'powerpress') );
                powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );
                powerpress_page_message_print();
                powerpress_admin_jquery_footer();
                exit;
            }

            if( !ini_get( 'allow_url_fopen' ) && !function_exists( 'curl_init' ) )
            {
                powerpress_admin_jquery_header( __('Blubrry Services', 'powerpress') );
                powerpress_page_message_add_notice( __('Your server must either have the php.ini setting \'allow_url_fopen\' enabled or have the PHP cURL library installed in order to continue.', 'powerpress') );
                powerpress_page_message_print();
                powerpress_admin_jquery_footer();
                exit;
            }

            check_admin_referer('powerpress-jquery-account-edit');

            if( !$Settings )
                $Settings = get_option('powerpress_general', array());

            if( empty($Settings['blubrry_username']) )
                $Settings['blubrry_username'] = '';
            if( empty($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] === 'false' )
                $Settings['blubrry_hosting'] = false;
            if( empty($Settings['blubrry_program_keyword']) )
                $Settings['blubrry_program_keyword'] = '';
            if(empty($Settings['network_mode'])) {
                $Settings['network_mode'] = '0';
            }

            $Programs = array();
            $ProgramHosting = array();
            $json_data = false;
            $results_programs = array();
            if ($creds) {
                $accessToken = powerpress_getAccessToken();
                $req_url = '/2/service/index.json?cache=' . md5( rand(0, 999) . time() );
                $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                $results_programs = $auth->api($accessToken, $req_url);
            } else {
                $api_url_array = powerpress_get_api_array();
                foreach( $api_url_array as $index => $api_url )
                {
                    $req_url = sprintf('%s/service/index.json?cache=' . md5( rand(0, 999) . time() ), rtrim($api_url, '/') );
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                    $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth']);
                    if( !$json_data && $api_url == 'https://api.blubrry.com/' ) { // Lets force cURL and see if that helps...
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
                    }
                    if( $json_data != false )
                        break;
                }
                if( $json_data ) {
					$results_programs =  powerpress_json_decode($json_data);
				}
            }
            powerpress_admin_jquery_account_header( __('Blubrry Services', 'powerpress') );
            powerpress_page_message_print();
            $Programs = array();
            // Get all the programs for this user...
            if ($results_programs) {
                if( isset($results_programs['error']) || !is_array($results_programs) ) {
                    if (isset($results_programs['error'])) {
                        powerpress_page_message_add_notice(__($results_programs['error'], 'powerpress'));
                        powerpress_page_message_print();
                    } else {
                        powerpress_page_message_add_notice(__("Error logging in", 'powerpress'));
                        powerpress_page_message_print();
                    }
                } else {
                    foreach ($results_programs as $null => $row) {
                        $Programs[$row['program_keyword']] = $row['program_title'];
                    }
                }
            }
             ?>

        <form action="<?php echo admin_url(); ?>" enctype="multipart/form-data" method="post">
<?php wp_nonce_field('powerpress-jquery-account-save'); ?>
            <input type="hidden" name="action" value="powerpress-jquery-account-save" />
            <div id="accountinfo">
            <?php if (count($Programs) > 0) { ?>
                <p>
                    <label for="blubrry_program_keyword"><?php echo __('Select Default Program', 'powerpress'); ?></label>
                    <select id="blubrry_program_keyword" name="Settings[blubrry_program_keyword]">
                        <?php
                        foreach( $Programs as $value => $desc )
                            echo "\t<option value=\"$value\"". ($Settings['blubrry_program_keyword']==$value?' selected':''). ">$desc</option>\n";
                        ?>
                    </select>
                </p>
                <p>
                    <label for="blubrry_network_mode"><?php echo __('Multi-program mode (publish to multiple Blubrry Hosting Accounts)', 'powerpress') ?></label>
                    <input type="checkbox" id="blubrry_network_mode" value="1" name="Settings[network_mode]" <?php echo $Settings['network_mode'] == '1' ? 'checked' : ''; ?> />
                </p>
            <?php } ?>
                <p>
                    <input type="submit" name="Save" value="<?php echo __('Save', 'powerpress'); ?>" />
                    <input type="button" name="Cancel" class="pp-plain-link" value="<?php echo __('Cancel', 'powerpress'); ?>" onclick="self.parent.tb_remove();" />
                    <input type="submit" name="Remove" class="pp-plain-link" value="<?php echo __('Unlink Account', 'powerpress'); ?>" style="float: right;" onclick="return confirm('<?php echo __('Remove Blubrry Services Integration, are you sure?', 'powerpress'); ?>');" />
                </p>
            </div>
            </form>
            <script type="text/javascript">


            <?php if (count($Programs) > 0) { ?>
                jQuery(document).ready(function($) {
                    jQuery('#blubrry_network_mode').change( function(event) {
                        if(this.checked) {
                            jQuery("#blubrry_program_keyword").prepend('<option value="no_default"><?php echo __("No Default", "powerpress"); ?></option>');
                        }
                        else {
                            jQuery('#blubrry_program_keyword option[value="no_default"').remove();
                        }

                    } );
                } );

            <?php } ?>
            </script><?php
            powerpress_admin_jquery_footer();
            exit;
            break;
        }
		case 'powerpress-jquery-subscribe-preview': {
			
			// Preview the current styling for the subscribe button
            nocache_headers();
			echo "<html>";
            echo "<body>";
            $shape = (!empty($_GET['shape']) && $_GET['shape'] == 'squared') ? '-sq' : '';
            $css = plugins_url('css/subscribe.css', __FILE__);
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$css\">";
            echo '<div style="width:80%;margin:0 0;" class="pp-sub-widget pp-sub-widget-modern">';
            echo '<div class="pp-sub-btns">';
            echo '<a href="#" style="width:250px;" class="pp-sub-btn' . $shape . ' pp-sub-itunes" title="'. esc_attr( __('Subscribe on Apple Podcasts', 'powerpress') ) .'" style="width: 90% !important;"><span class="pp-sub-ic"></span> '. esc_attr( __('Apple Podcasts', 'powerpress') ) .'</a></div>';
            echo '</div>';
            echo '</div>';
            echo "</body>";
			echo "</html>";
            exit;
		}; break;
		case 'powerpress-jquery-upload': {
			
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header( __('Uploader', 'powerpress') );
				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.','powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-upload');
			
			$RedirectURL = false;
			$Error = false;
			
			if( !$Settings )
				$Settings = get_option('powerpress_general', array());
			
			if( empty($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] === 'false' )
				$Settings['blubrry_hosting'] = false;
			if( empty($Settings['blubrry_program_keyword']) )
				$Settings['blubrry_program_keyword'] = '';
			if( empty($Settings['blubrry_auth']) )
				$Settings['blubrry_auth'] = '';	
				
			if( empty($Settings['blubrry_hosting']) )
			{
				$Error = __('This feature is available to Blubrry Hosting users only.','powerpress');
			}
            $blubrryProgramKeyword =  $Settings['blubrry_program_keyword'];
            if( !empty($_GET['blubrryProgramKeyword']) ) {
                $blubrryProgramKeyword = $_GET['blubrryProgramKeyword'];
            }
			if( $Error == false )
			{
				$json_data = false;
				$api_url_array = powerpress_get_api_array();
                if ($creds) {
                    $accessToken = powerpress_getAccessToken();
                    $req_url = sprintf('/2/media/%s/upload_session.json', $blubrryProgramKeyword);
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '?' . POWERPRESS_BLUBRRY_API_QSA : '');
                    $results = $auth->api($accessToken, $req_url);
                } else {
                    foreach ($api_url_array as $index => $api_url) {
                        $req_url = sprintf('%s/media/%s/upload_session.json', rtrim($api_url, '/'), $blubrryProgramKeyword);
                        $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '?' . POWERPRESS_BLUBRRY_API_QSA : '');
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth']);
                        if (!$json_data && $api_url == 'https://api.blubrry.com/') { // Lets force cURL and see if that helps...
                            $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
                        }
                        if ($json_data != false)
                            break;
                    }
                    $results = powerpress_json_decode($json_data);
                }
				// We need to obtain an upload session for this user...
				if( isset($results['error']) && strlen($results['error']) > 1 )
				{
					$Error = $results['error'];
					if( strstr($Error, 'currently not available') )
						$Error = __('Unable to find podcasts for this account.','powerpress');
				}
				else if( $results === $json_data )
				{
					$Error = $json_data;
				}
				else if( !is_array($results) || $results == false )
				{
					$Error = $json_data;
				}
				else
				{
					if( isset($results['url']) && !empty($results['url']) )
						$RedirectURL = $results['url'];
				}
			}
			
			if( $Error == false && $RedirectURL )
			{
				$RedirectURL .= '&ReturnURL=';
				$RedirectURL .= urlencode( admin_url("admin.php?action=powerpress-jquery-upload-complete") );
				$RedirectURL .= '&message=true';
                $RedirectURL .= '&ver='.POWERPRESS_VERSION;
				header("Location: $RedirectURL");
				exit;
			}
			else if( $Error == false )
			{
				global $g_powerpress_remote_error, $g_powerpress_remote_errorno;
				if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )
					$Error = '<p>'. __('Incorrect sign-in email address or password.', 'powerpress').'</p><p>'.__('Verify your account entered under Services and Statistics settings then try again.', 'powerpress') .'</p>';
				else if( !empty($g_powerpress_remote_error) )
					$Error = '<p>'.$g_powerpress_remote_error .'</p>';
				else
					$Error = '<p>'.__('Unable to obtain upload session.','powerpress') .'</p>';
			}
			
			powerpress_admin_jquery_header( __('Uploader','powerpress') );
			echo '<h2>'. __('Uploader','powerpress') .'</h2>';
			echo '<p>';
			echo $Error;
			echo '</p>';
			?>
			<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
			<?php
			powerpress_admin_jquery_footer();
			exit;
		}; break;
		case 'powerpress-jquery-upload-complete': {
		
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header('Uploader');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			// sanitize_title esc_attr esc_html powerpress_esc_html
			$File = (isset($_GET['File'])? htmlspecialchars($_GET['File']):false);
			$Message = (isset($_GET['Message'])? htmlspecialchars($_GET['Message']):'');
			
			powerpress_admin_jquery_header( __('Upload Complete', 'powerpress') );
			echo '<h2>'. __('Uploader', 'powerpress') .'</h2>';
			echo '<p>';
			if( $File )
			{
				echo __('File', 'powerpress')  .': ';
				echo $File;
				echo ' - ';
			}
			echo $Message;
			echo '</p>';
			?>
			<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
			<?php
			
			if( empty($Message) )
			{
?>
<script language="JavaScript" type="text/javascript"><!--
<?php if( $File != '' ) { ?>
self.parent.SelectMedia('<?php echo $File ; ?>'); <?php } ?>
self.parent.tb_remove();
//-->
</script>
<?php
			}
			powerpress_admin_jquery_footer();
			exit;
		}; break;
		case 'powerpress-jquery-pts': {
			if( function_exists('powerpress_ajax_pts') )
				powerpress_ajax_pts($Settings);
			else
				echo "Error";
			exit;
		}; break;
		case 'powerpress-jquery-pts-post': {
			if( function_exists('powerpress_ajax_pts_post') )
				powerpress_ajax_pts_post($Settings);
			else
				echo "Error";
		}; break;
        case 'powerpress-ep-box-options': {
        if (defined('WP_DEBUG')) {
            if (WP_DEBUG) {?>
        <link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/episode-box.css" type="text/css" media="screen" />
            <?php } else { ?>
        <link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/episode-box.min.css" type="text/css" media="screen" />
            <?php   }
        } else { ?>
        <link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/episode-box.min.css" type="text/css" media="screen" />
        <?php }
            wp_enqueue_script('powerpress-admin', powerpress_get_root_url() . 'js/admin.js', array(), POWERPRESS_VERSION );

            powerpress_admin_jquery_header( __('PowerPress Entry Box Settings','powerpress') );
            require_once(dirname(__FILE__). '/views/ep-box-settings.php');
            powerpressadmin_edit_entry_options($Settings);
            powerpress_admin_jquery_footer();
        }; break;
        case 'powerpress-ep-box-options-save': {
            if( !current_user_can(POWERPRESS_CAPABILITY_MANAGE_OPTIONS) )
            {
                powerpress_admin_jquery_header('PowerPress Entry Box Settings', 'powerpress');
                powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );
                powerpress_page_message_print();
                powerpress_admin_jquery_footer();
                exit;
            }

            check_admin_referer('powerpress-edit');
            $Settings = $_POST['General'];
            powerpress_save_settings($Settings);
            powerpress_admin_jquery_header('PowerPress Entry Box Settings', 'powerpress');
            powerpress_page_message_add_notice( __('Settings will be applied on page refresh. If you\'ve already entered information into this post, simply finish the post and the settings will apply when you start your next one.', 'powerpress') );
            powerpress_page_message_print();
            powerpress_admin_jquery_footer();
            exit;
        }; break;
        case 'powerpress-player-block': {
            echo do_shortcode('[powerpress sample=1 channel="podcast"]');
            exit;
        }
        case 'powerpress-podcast-index-shows': {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'powerpress-edit-feed' ) ) {
                exit;
            }
            $json_data = false;
            $results = array();
            $api_url_array = powerpress_get_api_array();
            if ($creds) {
                $accessToken = powerpress_getAccessToken();
                $req_url = '/2/podcast-index/get-shows?format=json';
                $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                $results = $auth->api($accessToken, $req_url, array('piQuery' => $_POST['piQuery']), 'POST');
            } else {
                foreach ($api_url_array as $index => $api_url) {
                    $req_url = sprintf('%s/podcast-index/get-shows/?format=json', rtrim($api_url, '/'));
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                    $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array('piQuery' => $_POST['piQuery']), 10, 'POST');
                    if (!$json_data && $api_url == 'https://api.blubrry.com/') { // Lets force cURL and see if that helps...
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array('piQuery' => $_POST['piQuery']), 10, 'POST', true); // Only give this 2 seconds to return results
                    }
                    if ($json_data != false)
                        break;
                }
                $results = powerpress_json_decode($json_data);
            }

            echo $results;
            exit;
        }
        case 'powerpress-podcast-index-episodes': {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'powerpress-edit-feed' ) ) {
                exit;
            }
            $json_data = false;
            $results = array();
            $api_url_array = powerpress_get_api_array();
            if ($creds) {
                $accessToken = powerpress_getAccessToken();
                $req_url = '/2/podcast-index/get-episodes?format=json';
                $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
                $results = $auth->api($accessToken, $req_url, array('podcastGuid' => $_POST['podcastGuid']), 'POST');
            } else {
                foreach ($api_url_array as $index => $api_url) {
                    $req_url = sprintf('%s/podcast-index/get-episodes/?format=json', rtrim($api_url, '/'));
                    $req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA') ? '&' . POWERPRESS_BLUBRRY_API_QSA : '');
                    $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array('podcastGuid' => $_POST['podcastGuid']), 10, 'POST');
                    if (!$json_data && $api_url == 'https://api.blubrry.com/') { // Lets force cURL and see if that helps...
                        $json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array('podcastGuid' => $_POST['podcastGuid']), 10, 'POST', true); // Only give this 2 seconds to return results
                    }
                    if ($json_data != false)
                        break;
                }
                $results = powerpress_json_decode($json_data);
            }

            echo $results;
            exit;
        }
	}
	
}

function powerpress_admin_jquery_account_header($title, $jquery = false, $no_exit = false) {

	if( function_exists('get_current_screen') ) {
		$current_screen = get_current_screen();
		if( !empty($current_screen) && is_object($current_screen) && $current_screen->is_block_editor() ) {
			return;
		}
	}

	nocache_headers();
	$other = false;
	if( $jquery )
		add_thickbox(); // we use the thckbox for some settings
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; <?php echo __('WordPress', 'powerpress'); ?></title>
<?php

// In case these functions haven't been included yet...
if( !defined('WP_ADMIN') )
	require_once(ABSPATH . 'wp-admin/includes/admin.php');

wp_admin_css( 'css/global' );
wp_admin_css();
if( $jquery )
	wp_enqueue_script('utils');

do_action('admin_print_scripts');
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    do_action('admin_head');
}

echo '<!-- done adding extra stuff -->';

if (defined('WP_DEBUG')) {
    if (WP_DEBUG) {?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.css" type="text/css" media="screen" />
    <?php } else { ?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.min.css" type="text/css" media="screen" />
    <?php   }
} else { ?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.min.css" type="text/css" media="screen" />
<?php }
if( $other ) echo $other; ?>
</head>
<body>
    <div id="TB_title">
        <div id="TB_ajaxWindowTitle"><?php echo $title; ?></div>
<?php if (!$no_exit) { ?>
        <div id="TB_closeAjaxWindow">
            <button type="button" id="TB_closeWindowButton" onclick="window.parent.tb_remove()">
                <span class="screen-reader-text"><?php echo __('close', 'powerpress'); ?></span>
                <span class="tb-close-icon"></span>
            </button>
        </div>
    <?php } ?>
    </div>
<div id="container">
<p style="display: none; text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding: 0;"><a href="#" onclick="self.parent.tb_remove();" title="<?php echo __('Cancel', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" /></a></p>
<?php
}

function powerpress_admin_jquery_header($title, $jquery = false)
{
	if( function_exists('get_current_screen') ) {
		$current_screen = get_current_screen();
		if( !empty($current_screen) && is_object($current_screen) && $current_screen->is_block_editor() ) { 
			return;
		}
	}
	
	nocache_headers();
	$other = false;
	if( $jquery )
		add_thickbox(); // we use the thckbox for some settings
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; <?php echo __('WordPress', 'powerpress'); ?></title>
<?php

// In case these functions haven't been included yet...
if( !defined('WP_ADMIN') )
	require_once(ABSPATH . 'wp-admin/includes/admin.php');

wp_admin_css( 'css/global' );
wp_admin_css();
if( $jquery )
	wp_enqueue_script('utils');


do_action('admin_print_scripts');
if (!is_plugin_active('woocommerce/woocommerce.php')) {
    do_action('admin_head');
}


echo '<!-- done adding extra stuff -->';

if (defined('WP_DEBUG')) {
    if (WP_DEBUG) {?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.css" type="text/css" media="screen" />
    <?php } else { ?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.min.css" type="text/css" media="screen" />
    <?php   }
} else { ?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.min.css" type="text/css" media="screen" />
<?php }
if( $other ) echo $other; ?>
</head>
<body>
<div id="container">
<p style="display: none; text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding: 0;"><a href="#" onclick="self.parent.tb_remove();" title="<?php echo __('Cancel', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" /></a></p>
<?php
}


function powerpress_admin_jquery_footer($jquery = false)
{
	if( $jquery )
		do_action('admin_print_footer_scripts');
	
?>
</div><!-- end container -->
</body>
</html>
<?php
	exit();
}

?>