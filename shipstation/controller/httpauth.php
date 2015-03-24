<?php
class ControllerHttpauth extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/shipstation');

		$authorised = false;

		if (!$this->config->get('shipstation_status')) {
			echo $this->language->get('text_not_enabled');

			die();
		} else {


			if ( isset ( $this->request->get['SS-UserName'] ) && isset ( $this->request->get['SS-Password'] ) ) {
				$auth_user = $this->request->get['SS-UserName'];
				$auth_password = $this->request->get['SS-Password'];

				if ( ( $auth_user == $this->config->get ( 'shipstation_config_key' ) ) && ( $auth_password == $this->config->get ( 'shipstation_verify_key' ) ) ) {
					$authorised = true;
				}
			}

		

			if (!$authorised) {
				header('WWW-Authenticate: Basic realm="ShipStation"');

				header('HTTP/1.0 401 Unauthorized');

				print "Login failed!\n";

				if ( isset ( $this->request->get['SS-Password'] ) ){
					print "SS-UserName:" . $this->request->get['SS-UserName'] . "\r\n";
				}
				else{
					print "SS-UserName:\r\n";
				}

				if ( isset ( $this->request->get['SS-Password'] ) ){
					print "SS-Password:" . $this->request->get['SS-Password'] . "\r\n";
				}
				else{
					print "SS-Password:\r\n";
				}

				if ( isset ( $this->request->get['action'] ) ){
					print "action:" . $this->request->get['action'] . "\r\n";
				}
				else{
					print "action:\r\n";
				}

				if (isset($_SERVER['HTTP_AUTHORIZATION']) && (strlen($_SERVER['HTTP_AUTHORIZATION']) > 0)) {
					print "HTTP_AUTHORIZATION:" . $_SERVER['HTTP_AUTHORIZATION'] . "\r\n";
				}
				else{
					print "HTTP_AUTHORIZATION:\r\n";
				}

				if (isset($_SERVER['PHP_AUTH_USER']) && (strlen($_SERVER['PHP_AUTH_USER']) > 0)){
					print "PHP_AUTH_USER:" . $_SERVER['PHP_AUTH_USER'] . "\r\n";
				}
				else{
					print "PHP_AUTH_USER:\r\n";
				}

				if (isset($_SERVER['PHP_AUTH_PW']) && (strlen($_SERVER['PHP_AUTH_PW']) > 0)){
					print "PHP_AUTH_PW:" . $_SERVER['PHP_AUTH_PW'] . "\r\n";
				}
				else{
					print "PHP_AUTH_PW:\r\n";
				}

				if (isset($_SERVER['HTTP_SS_AUTH_USER']) && (strlen($_SERVER['HTTP_SS_AUTH_USER']) > 0)){
					print "HTTP_SS_AUTH_USER:" . $_SERVER['HTTP_SS_AUTH_USER'] . "\r\n";
				}
				else{
					print "HTTP_SS_AUTH_USER:\r\n";
				}

				if (isset($_SERVER['HTTP_SS_AUTH_PW']) && (strlen($_SERVER['HTTP_SS_AUTH_PW']) > 0)){
					print "HTTP_SS_AUTH_PW:" . $_SERVER['HTTP_SS_AUTH_PW']. "\r\n";
				}
				else{
					print "HTTP_SS_AUTH_PW:\r\n";
				}

				die();
			}
		}
	}
}
?>
