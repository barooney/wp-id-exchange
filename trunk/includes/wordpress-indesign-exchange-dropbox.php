<?php

class Wordpress_Indesign_Exchange_Dropbox {

	private $appInfo;
	private $webAuth;

	public function __construct() {
		$this->appInfo = new Dropbox\AppInfo( get_option( 'dropbox_key' ), get_option( 'dropbox_secret' ) );
		$csrfTokenStore = new Dropbox\ArrayEntryStore( $_SESSION, 'dropbox-auth-csrf-token' );
		$this->webAuth = new Dropbox\WebAuth( $this->appInfo, 'WordPress InDesign Exchange', admin_url() . 'options-general.php?page=wp-id-exchange/admin/partials/wordpress-indesign-exchange-admin-authorize.php', $csrfTokenStore );
	}

	public function is_authorized() {
		return ( '' !== get_option( 'dropbox_access_token', '' ) );
	}

	public function get_user_id() {
		return get_option( 'dropbox_user_id' );
	}

	public function get_authorization_url() {
		return $this->webAuth->start();
	}

	public function get_access_token() {
		try {
			list( $access_token, $user_id ) = $this->webAuth->finish( $_GET );
		} catch ( Dropbox\WebAuthException_BadRequest $e ) {
			return false;
		}
		update_option( 'dropbox_access_token', $access_token );
		update_option( 'dropbox_user_id', $user_id );
		return array(
			'access_token' => $access_token,
			'user_id' => $user_id,
		);
	}
}