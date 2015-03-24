<?php
class ModelXmlParse extends Model {
	public function parse($data, $node = 'shipstation', $xml = null, $pretty = false) {
		if (isset($this->request->get['json'])) {
			return json_encode($data);
		}

		if (isset($this->request->get['xmloff'])) {
			return print_r($data);
		}

		if (isset($this->request->get['pretty'])) {
			$pretty = true;
		}

		if (ini_get('zend.ze1_compatibility_mode') == 1) {
			ini_set('zend.ze1_compatibility_mode', 0);
		}

		if ($xml == null) {
			$xml = simplexml_load_string('<?xml version=\'1.0\' encoding=\'utf-8\'?><' . $node . ' />');
		}

		foreach ($data as $key => $value) {
			if (is_numeric($key)) {
				$key = "unknownNode_" . (string)$key;
			}

			if (!strstr($key, 'Address')) {
				$key = preg_replace('/[^a-z]/i', '', $key);
			}

			if (is_array($value)) {
				$child = $xml->addChild($key);
				$this->parse($value, $node, $child);
			} else {
				$value = htmlentities($value);
				$xml->addChild($key, $value);
			}
		}

		if ($pretty) {
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());

			return $dom->saveXML();
		} else {
			$xml->formatOutput = true;

			return $xml->asXML();
		}
	}
}
?>
