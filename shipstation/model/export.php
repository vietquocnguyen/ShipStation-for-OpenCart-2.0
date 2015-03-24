<?php
class ModelExport extends Model {
	private $error = '';

	public function getOrder($order_id) {
		if($this->statuses == '') {
			$this->statuses = $this->getStatusById();
		}

		$order_query = $this->db->query("SELECT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = o.customer_id) AS customer FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$bill_to = array();

			$bill_to[] = array(
				'Name'       => $order_query->row['payment_firstname'] . ' ' . $order_query->row['payment_lastname'],
				'Company'    => $order_query->row['payment_company'],
				'Phone'      => $order_query->row['telephone'],
				'Email'      => $order_query->row['email']
			);

			$ship_to = array();

			if ($order_query->row['shipping_method']) {
				$ship_to[] = array(
					'Name'       => $order_query->row['shipping_firstname'] . ' ' . $order_query->row['shipping_lastname'],
					'Company'    => $order_query->row['shipping_company'],
					'Address1'   => $order_query->row['shipping_address_1'],
					'Address2'   => $order_query->row['shipping_address_2'],
					'City'       => $order_query->row['shipping_city'],
					'State'      => $order_query->row['shipping_zone'],
					'PostalCode' => $order_query->row['shipping_postcode'],
					'Country'    => $shipping_iso_code_2,
					'Phone'      => $order_query->row['telephone']
				);
			} else {
				$ship_to[] = array(
					'Name'       => $order_query->row['payment_firstname'] . ' ' . $order_query->row['payment_lastname'],
					'Company'    => $order_query->row['payment_company'],
					'Address1'   => $order_query->row['payment_address_1'],
					'Address2'   => $order_query->row['payment_address_2'],
					'City'       => $order_query->row['payment_city'],
					'State'      => $order_query->row['payment_zone'],
					'PostalCode' => $order_query->row['payment_postcode'],
					'Country'    => $payment_iso_code_2,
					'Phone'      => $order_query->row['telephone']
				);
			}

			$customer = array();

			$customer[] = array(
				'CustomerCode' => $order_query->row['email'],
				'BillTo'       => $bill_to,
				'ShipTo'       => $ship_to
			);

			$order_totals = $this->getOrderTotals($order_query->row['order_id']);

			$totals = array();

			foreach ($order_totals as $order_total) {
				$totals[$order_total['code']] = $order_total;
			}

			$order_products = $this->getOrderProducts($order_id);

			$products = array();

			$this->load->model('tool/image');

			foreach ($order_products as $product) {
				$weight_class = $this->getWeightClass($product['weight_class_id']);

				$order_options = $this->getOrderOptions($product['order_id'], $product['order_product_id']);

				$options = array();

				foreach ($order_options as $option) {
					$options[] = array(
						'Name'      => $option['name'],
						'Value'     => $option['value'],
						'Weight'    => $option['weight']
					);
				}

				$products[] = array(
					'SKU'         => ($product['sku'] == '') ? (($product['upc'] == '') ? $product['model'] : $product['upc']) : $product['sku'],
					'Name'        => $product['name'],
					'ImageUrl'    => $this->model_tool_image->resize($product['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
					'Weight'      => $product['weight'],
					'WeightUnits' => $weight_class['title'],
					'Quantity'    => $product['quantity'],
					'UnitPrice'   => $product['price'],
					'Options'     => $options
				);
			}

			$order_data = array();

			$order_data[] = array(
				'OrderNumber'        => $order_id,
				'OrderDate'          => date('j/n/Y g:i A', strtotime($order_query->row['date_added'])),
				'OrderStatus'        => $this->statuses[$order_query->row['order_status_id']],
				'LastModified'       => date('j/n/Y g:i A', strtotime($order_query->row['date_modified'])),
				'ShippingMethod'     => ($order_query->row['shipping_method'] == '') ? 'unknown' : $order_query->row['shipping_method'],
				'OrderTotal'         => (isset($totals['total']['value'])) ? $totals['total']['value'] : '0.00' ,
				'TaxAmount'          => (isset($totals['tax'])) ? preg_replace('/[^0-9.]*/', '', $totals['tax']['title']) : '0.00',
				'ShippingAmount'     => (isset($totals['shipping'])) ? preg_replace('/[^0-9.]*/', '', $totals['shipping']['title']) : '0.00',
				'CustomerNotes'      => $order_query->row['comment'],
				'InternalNotes'      => '',
				'Customer'           => $customer,
				'Items'              => $products
			);

			return $order_data;
		} else {
			return false;
		}
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT *, op.quantity AS quantity, op.price AS price FROM " . DB_PREFIX . "order_product op INNER JOIN " . DB_PREFIX . "product p ON op.product_id = p.product_id WHERE op.order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrders($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > 0 AND ";

		if ((isset($data['startdate'])) || (isset($data['enddate']))) {

			if (isset($data['startdate'])) {
				$sql .= " `date_modified` >=  '" . $data['startdate'] . "'";
			}

			if ((isset($data['startdate'])) && (isset($data['enddate']))) {
				$sql .= " AND ";
			}

			if (isset($data['enddate'])) {
				$sql .= " `date_modified` <= '" . $data['enddate'] . "'";
			}
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getStatusById() {
		$order_status_data = $this->cache->get('order_status.' . (int)$this->config->get('config_language_id'));

		if (!$order_status_data) {
			$query = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

			$order_status_data = $query->rows;

			$this->cache->set('order_status.' . (int)$this->config->get('config_language_id'), $order_status_data);
		}

		foreach ($order_status_data as $result) {
			$order_status[$result['order_status_id']] = $result['name'];
		}

		return $order_status;
	}

	public function getWeightClass($weight_class_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wc.weight_class_id = '" . (int)$weight_class_id . "' AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option oo LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (oo.product_option_value_id = pov.product_option_value_id) WHERE oo.order_id = '" . (int)$order_id . "' AND oo.order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}
}
?>
