<?php
class ControllerVerifyStatus extends Controller {
	private $error = array();

	public function index() {
		if (isset($this->request->get['status'])) {
			$this->load->model('status');

			if ($this->model_status->checkOrderStatus($this->request->get['status'])) {
				echo 'true';
			} else {
				echo 'false';
			}
		} else {
			echo 'false';
		}
	}

	public function all() {
		$this->load->model('status');
		$this->load->model('xmlparse');

		$order_statuses = $this->model_status->getOrderStatuses();

		$xml;

		foreach($order_statuses as $order_status) {
			$xml['Status_' . $order_status['order_status_id']] = $order_status['name'];
		}

		echo $this->model_xmlparse->parse($xml, 'Statuses');
	}

	public function help() {
		?>
		https://www.mystore.com/shipstation/index.php?action=verifystatus&status=statusname
		<?php
	}
}
?>
