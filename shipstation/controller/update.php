<?php
class ControllerUpdate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/shipstation');

		if (isset($this->request->get['order_number']) && isset($this->request->get['status']) && isset($this->request->get['comment'])) {
			$this->load->model('update');

			$order_statuses = $this->model_update->getOrderStatuses();

			$order['order_status_id'] = (isset($order_statuses[strtolower($this->request->get['status'])])) ? $order_statuses[strtolower($this->request->get['status'])] : 0;
			$order['notify'] = 0;
			$order['comment'] = $this->request->get['comment'];

			$this->model_update->addOrderHistory((int)$this->request->get['order_number'], $order);

			echo $this->language->get('text_update_success');
		}
	}
}
?>
