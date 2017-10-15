<?php
/**
 * Plugin Name:  HS ContactForm7 Proxy
 * Plugin URI: https://github.com/tezrosolutions/hs-contactform7-integration
 * Description: This plugin integrates ContactForm7 with HubSpot Form API
 * Version: 1.0.0
 * Author: Muhammad Umair
 * Author URI: https://github.com/tezrosolutions/
 */
define('HS_PORTAL', 'HS Portal ID');
define('SUBSCRIPTION_FORM_HS_GUID', 'HS Form ID');
define('SUBSCRIPTION_FORM_ID', 'CF7 form ID');

define('QUESTION_FORM_HS_GUID', 'HS Form ID');
define('QUESTION_FORM_ID', 'CF7 form ID');


function _get_hs_context() {
	if(isset($_COOKIE['hubspotutk'])) {
   		$hubspotutk = $_COOKIE['hubspotutk'];
	} else {
    	$hubspotutk = "";
	}


	$ip_addr = $_SERVER['REMOTE_ADDR'];
	$hs_context = array(
    	'hutk' => $hubspotutk,
    	'ipAddress' => $ip_addr,
    	'pageUrl' => 'http://tezrosolutions.com',
    	'pageName' => 'Test Page'
	);

	return $hs_context_json = json_encode($hs_context);

}

function _post_data_to_hs($data, $endpoint) {
	$ch = @curl_init();
	@curl_setopt($ch, CURLOPT_POST, true);
	@curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	@curl_setopt($ch, CURLOPT_URL, $endpoint);
	@curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    	'Content-Type: application/x-www-form-urlencoded'
    ));
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch); 
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	@curl_close($ch);
}


function ts_synchronize_hs ($wpcf7_data) {
	$submission = WPCF7_Submission::get_instance();
	

	if ( $submission ) {
		$input = $submission->get_posted_data();
		if($input['_wpcf7'] == SUBSCRIPTION_FORM_ID) {

		
		
			$firstname = ($input['firstname'])?$input['firstname']:"";
			$lastname = ($input['lastname'])?$input['lastname']:"";
			$email = ($input['emailaddress'])?$input['emailaddress']:"";

			$hs_context_json = _get_hs_context();

		

			$str_post = "firstname=" . urlencode($firstname)
        	. "&lastname=" . urlencode($lastname)
        	. "&email=" . urlencode($email)
        	. "&hs_context=" . urlencode($hs_context_json); ; 

			$endpoint = 'https://forms.hubspot.com/uploads/form/v2/' . HS_PORTAL . '/' . SUBSCRIPTION_FORM_HS_GUID;
			_post_data_to_hs($str_post, $endpoint);


		
		} else if(QUESTION_FORM_ID == $input['_wpcf7']  ) {
			$firstname = ($input['firstname'])?$input['firstname']:"";
			$lastname = ($input['lastname'])?$input['lastname']:"";
			$email = ($input['email'])?$input['email']:"";
			$company = ($input['company'])?$input['company']:"";
			$message = ($input['message'])?$input['message']:"";
			$mobilephone = ($input['mobilephone'])?$input['mobilephone']:"";
			$subscribe_to_our_mailing_list_to_get_the_updates_to_your_email_inbox_ = ($input['subscribe_to_our_mailing_list_to_get_the_updates_to_your_email_inbox_'])?$input['subscribe_to_our_mailing_list_to_get_the_updates_to_your_email_inbox_']:"";


			$hs_context_json = _get_hs_context();

			$str_post = "firstname=" . urlencode($firstname)
        	. "&lastname=" . urlencode($lastname)
        	. "&email=" . urlencode($email)
        	. "&company=" . urlencode($company)
        	. "&message=" . urlencode($message)
        	. "&mobilephone=" . urlencode($mobilephone)
        	. "&subscribe_to_our_mailing_list_to_get_the_updates_to_your_email_inbox_=" . urlencode($subscribe_to_our_mailing_list_to_get_the_updates_to_your_email_inbox_)
        	. "&hs_context=" . urlencode($hs_context_json);

			$endpoint = 'https://forms.hubspot.com/uploads/form/v2/' . HS_PORTAL . '/' . QUESTION_FORM_HS_GUID;

			_post_data_to_hs($str_post, $endpoint);

		} 

		
	}

    $wpcf7_data->skip_mail = true;
}

add_action("wpcf7_mail_sent", "ts_synchronize_hs");	