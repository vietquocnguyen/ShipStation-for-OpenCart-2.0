<?php
class ControllerDefault extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/shipstation');

		echo $this->language->get('text_direct_access');
	}
}
?>
