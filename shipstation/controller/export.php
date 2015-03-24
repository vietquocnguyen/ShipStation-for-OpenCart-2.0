<?php
class ControllerExport extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('xmlparse');
		$this->load->model('export');

		$data = array();

		if (isset($this->request->get['start_date'])) {
			$start_date_time = explode(' ', $this->request->get['start_date']);
			$startdate = explode('/', $start_date_time[0]);
			$starttime = explode(':', $start_date_time[1]);
			$data['startdate'] = date('Y-m-d H:i:s', mktime($starttime[0], $starttime[1], 0, $startdate[0], $startdate[1], $startdate[2]));
		}

		if (isset($this->request->get['end_date'])) {
			$end_date_time = explode(' ', $this->request->get['end_date']);
			$enddate = explode('/', $end_date_time[0]);
			$endtime = explode(':', $end_date_time[1]);
			$data['enddate'] = date('Y-m-d H:i:s', mktime($endtime[0], $endtime[1], 0, $enddate[0], $enddate[1], $enddate[2]));
		}

		$orders = $this->model_export->getOrders($data);

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<Orders>' . "\n";
		if ($orders) {
			foreach ($orders as $order) {
				$order_info = $this->model_export->getOrder($order['order_id']);

				if ($order_info) {
					foreach ($order_info as $order_data) {
						$xml .= '	<Order>' . "\n";
						$xml .= '		<OrderNumber><![CDATA[' . $order_data['OrderNumber'] . ']]></OrderNumber>' . "\n";
						$xml .= '		<OrderDate>' . $order_data['OrderDate'] . '</OrderDate>' . "\n";
						$xml .= '		<OrderStatus><![CDATA[' . $order_data['OrderStatus'] . ']]></OrderStatus>' . "\n";
						$xml .= '		<LastModified>' . $order_data['LastModified'] . '</LastModified>' . "\n";
						$xml .= '		<ShippingMethod><![CDATA[' . $order_data['ShippingMethod'] . ']]></ShippingMethod>' . "\n";
						$xml .= '		<OrderTotal>' . $order_data['OrderTotal'] . '</OrderTotal>' . "\n";
						$xml .= '		<TaxAmount>' . $order_data['TaxAmount'] . '</TaxAmount>' . "\n";
						$xml .= '		<ShippingAmount>' . $order_data['ShippingAmount'] . '</ShippingAmount>' . "\n";
						$xml .= '		<CustomerNotes><![CDATA[' . $order_data['CustomerNotes'] . ']]></CustomerNotes>' . "\n";
						$xml .= '		<InternalNotes><![CDATA[' . $order_data['InternalNotes'] . ']]></InternalNotes>' . "\n";
						$xml .= '		<Customer>' . "\n";
						foreach ($order_data['Customer'] as $customer) {
							$xml .= '			<CustomerCode>' . $customer['CustomerCode'] . '</CustomerCode>' . "\n";
							$xml .= '			<BillTo>' . "\n";
							foreach ($customer['BillTo'] as $billing) {
								$xml .= '				<Name><![CDATA[' . $billing['Name'] . ']]></Name>' . "\n";
								$xml .= '				<Company><![CDATA[' . $billing['Company'] . ']]></Company>' . "\n";
								$xml .= '				<Phone>' . $billing['Phone'] . '</Phone>' . "\n";
								$xml .= '				<Email>' . $billing['Email'] . '</Email>' . "\n";
							}
							$xml .= '			</BillTo>' . "\n";
							$xml .= '			<ShipTo>' . "\n";
							foreach ($customer['ShipTo'] as $shipping) {
								$xml .= '				<Name><![CDATA[' . $shipping['Name'] . ']]></Name>' . "\n";
								$xml .= '				<Company><![CDATA[' . $shipping['Company'] . ']]></Company>' . "\n";
								$xml .= '				<Address1><![CDATA[' . $shipping['Address1'] . ']]></Address1>' . "\n";
								$xml .= '				<Address2><![CDATA[' . $shipping['Address2'] . ']]></Address2>' . "\n";
								$xml .= '				<City><![CDATA[' . $shipping['City'] . ']]></City>' . "\n";
								$xml .= '				<State><![CDATA[' . $shipping['State'] . ']]></State>' . "\n";
								$xml .= '				<PostalCode><![CDATA[' . $shipping['PostalCode'] . ']]></PostalCode>' . "\n";
								$xml .= '				<Country><![CDATA[' . $shipping['Country'] . ']]></Country>' . "\n";
								$xml .= '				<Phone>' . $shipping['Phone'] . '</Phone>' . "\n";
							}
							$xml .= '			</ShipTo>' . "\n";
						}
						$xml .= '		</Customer>' . "\n";
						$xml .= '		<Items>' . "\n";
						foreach ($order_data['Items'] as $item) {
							$xml .= '			<Item>' . "\n";
							$xml .= '				<SKU><![CDATA[' . $item['SKU'] . ']]></SKU>' . "\n";
							$xml .= '				<Name><![CDATA[' . $item['Name'] . ']]></Name>' . "\n";
							$xml .= '				<ImageUrl><![CDATA[' . $item['ImageUrl'] . ']]></ImageUrl>' . "\n";
							$xml .= '				<Weight>' . $item['Weight'] . '</Weight>' . "\n";
							$xml .= '				<WeightUnits>' . $item['WeightUnits'] . '</WeightUnits>' . "\n";
							$xml .= '				<Quantity>' . $item['Quantity'] . '</Quantity>' . "\n";
							$xml .= '				<UnitPrice>' . $item['UnitPrice'] . '</UnitPrice>' . "\n";
							if ($item['Options']) {
								$xml .= '				<Options>' . "\n";
								foreach ($item['Options'] as $option) {
									$xml .= '					<Option>' . "\n";
									$xml .= '						<Name><![CDATA[' . $option['Name'] . ']]></Name>' . "\n";
									$xml .= '						<Value><![CDATA[' . $option['Value'] . ']]></Value>' . "\n";
									$xml .= '						<Weight>' . $option['Weight'] . '</Weight>' . "\n";
									$xml .= '					</Option>' . "\n";
								}
								$xml .= '				</Options>' . "\n";
							}
							$xml .= '			</Item>' . "\n";
						}
						$xml .= '		</Items>' . "\n";
						$xml .= '	</Order>' . "\n";
					}
				}
			}
		}

		$xml .= '</Orders>';
		$this->log->write($xml);
		echo $xml;

	}
}
?>
