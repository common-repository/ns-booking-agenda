<?php
class OutlookIntegration{
	private $clientID;
	private $secretID;
	private $redirectUrl;
	private $tokenUrl;
	
	public function __construct($redirectUrl){
		$this->clientID = get_option('ns_agenda_outlook_client_id');
		$this->secretID = get_option('ns_agenda_outlook_client_secret');
		$this->tokenUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
		$this->redirectUrl = $redirectUrl;
	}
	
	public function authorization($code) {
		$fields = [
			'client_id '      => $this->clientID,
			'client_secret' => $this->secretID,
			'redirect_uri' => $this->redirectUrl,
			'grant_type' => 'authorization_code',
			'code'         => $code
		];

		$response = wp_remote_post( $this->tokenUrl, array(
			'body'    => $fields,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded',
				),
			) );
			
		$body = json_decode(wp_remote_retrieve_body($response));

		if($body->access_token) {
			return $body->access_token;
		}
		
		return 'error';
	}
}

?>