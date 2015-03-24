<?php
class ControllerModuleShipStation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/shipstation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/shipstation.css');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->uninstall(false);

			$this->install(false);

			$this->model_setting_setting->editSetting('shipstation', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['heading_general'] = $this->language->get('heading_general');
		$data['heading_export'] = $this->language->get('heading_export');
		$data['heading_update'] = $this->language->get('heading_update');
		$data['heading_error'] = $this->language->get('heading_error');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_not_enabled'] = $this->language->get('text_not_enabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_config_key'] = $this->language->get('entry_config_key');
		$data['entry_config_ver_key'] = $this->language->get('entry_config_ver_key');
		$data['entry_start_date'] = $this->language->get('entry_start_date');
		$data['entry_end_date'] = $this->language->get('entry_end_date');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_export'] = $this->language->get('tab_export');
		$data['tab_update'] = $this->language->get('tab_update');
		$data['tab_error'] = $this->language->get('tab_error');

		$data['button_keygen'] = $this->language->get('button_keygen');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_export'] = $this->language->get('button_export');
		$data['button_clear'] = $this->language->get('button_clear');

		$data['token'] = $this->session->data['token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])){
			$data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['date_error'])) {
			$data['error_warning'] = $this->session->data['date_error'];

			unset($this->session->data['date_error']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['config_key'])){
			$data['error_config_key'] = $this->error['config_key'];
		} else {
			$data['error_config_key'] = '';
		}

		if (isset($this->error['verify_key'])){
			$data['error_verify_key'] = $this->error['verify_key'];
		} else {
			$data['error_verify_key'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/shipstation', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['keygen'] = $this->url->link('module/shipstation/keygen', 'token=' . $this->session->data['token'], 'SSL');
		$data['action'] = $this->url->link('module/shipstation', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['export'] = HTTPS_SERVER . '../shipstation/index.php?action=export';
		$data['clear'] = $this->url->link('module/shipstation/clear', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['shipstation_status'])) {
			$data['shipstation_status'] = $this->request->post['shipstation_status'];
		} else {
			$data['shipstation_status'] = $this->config->get('shipstation_status');
		}

		if (isset($this->request->post['shipstation_config_key'])) {
			$data['shipstation_config_key'] = $this->request->post['shipstation_config_key'];
		} elseif ($this->config->get('shipstation_config_key')) {
			$data['shipstation_config_key'] = $this->config->get('shipstation_config_key');
		} else {
			$data['shipstation_config_key'] = '';
		}

		if (isset($this->request->post['shipstation_verify_key'])) {
			$data['shipstation_verify_key'] = $this->request->post['shipstation_verify_key'];
		} elseif ($this->config->get('shipstation_verify_key')) {
			$data['shipstation_verify_key'] = $this->config->get('shipstation_verify_key');
		} else {
			$data['shipstation_verify_key'] = '';
		}

		$file = DIR_LOGS . 'shipstation/' . $this->config->get('config_error_filename');

		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		} else {
			$data['log'] = '';
		}



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/shipstation.tpl', $data));
		// $this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/shipstation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['shipstation_config_key']) {
			$this->error['config_key'] = $this->language->get('error_config_key');
		}

		if (!$this->request->post['shipstation_verify_key']) {
			$this->error['verify_key'] = $this->language->get('error_verify_key');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function install($install = true) {
		if (!$this->user->hasPermission('modify', 'module/shipstation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!file_exists(DIR_LOGS . 'shipstation')) {
			mkdir(DIR_LOGS . 'shipstation');
		}
$install = true;
		if ($install) {
			echo 'yay';
			$base_dir = str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/';

			$output  = '<?php' . "\n";
			$output .= '// Generated during install (' . date('F j, Y, g:i a') . ')' . "\n\n";

			$output .= '// HTTP' . "\n";
			$output .= 'define(\'HTTP_SERVER\', \'' . HTTP_SERVER . '\');' . "\n";
			$output .= 'define(\'HTTP_IMAGE\', \'http://' . $_SERVER['HTTP_HOST'] . '/image/\');' . "\n\n";

			$output .= '// HTTPS' . "\n";
			$output .= 'define(\'HTTPS_SERVER\', \'' . HTTPS_SERVER . '\');' . "\n";
			$output .= 'define(\'HTTPS_IMAGE\', \'http://' . $_SERVER['HTTP_HOST'] . '/image/\');' . "\n\n";

			$output .= '// DIR' . "\n";
			$output .= 'define(\'BASE_DIR\', \'' . $base_dir . '\');' . "\n\n";
			$output .= 'define(\'DIR_APPLICATION\', \'' . $base_dir . 'shipstation/' . '\');' . "\n";
			$output .= 'define(\'DIR_SYSTEM\', \'' . DIR_SYSTEM . '\');' . "\n";
			$output .= 'define(\'DIR_DATABASE\', \'' . DIR_DATABASE . '\');' . "\n";
			$output .= 'define(\'DIR_LANGUAGE\', \'' . DIR_LANGUAGE . '\');' . "\n";
			$output .= 'define(\'DIR_CONFIG\', \'' . DIR_CONFIG . '\');' . "\n";
			$output .= 'define(\'DIR_IMAGE\', \'' . DIR_IMAGE . '\');' . "\n";
			$output .= 'define(\'DIR_CACHE\', \'' . DIR_CACHE . '\');' . "\n";
			$output .= 'define(\'DIR_LOGS\', \'' . DIR_LOGS . 'shipstation/' . '\');' . "\n\n";

			$output .= '// DB' . "\n";
			$output .= 'define(\'DB_DRIVER\', \'' . DB_DRIVER . '\');' . "\n";
			$output .= 'define(\'DB_HOSTNAME\', \'' . DB_HOSTNAME . '\');' . "\n";
			$output .= 'define(\'DB_USERNAME\', \'' . DB_USERNAME . '\');' . "\n";
			$output .= 'define(\'DB_PASSWORD\', \'' . DB_PASSWORD . '\');' . "\n";
			$output .= 'define(\'DB_DATABASE\', \'' . DB_DATABASE . '\');' . "\n";
			$output .= 'define(\'DB_PREFIX\', \'' . DB_PREFIX . '\');' . "\n";
			$output .= '?>';

			$file = fopen('../shipstation/config.php', 'w');

			fwrite($file, $output);

			fclose($file);
		}
	}

	public function uninstall($uninstall = true) {
		if (!$this->user->hasPermission('modify', 'module/shipstation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$this->model_setting_setting->deleteSetting('shipstation');

		if ($uninstall) {
			$output = '';

			$file = fopen('../shipstation/config.php', 'w');

			fwrite($file, $output);

			fclose($file);
		}
	}

	public function keygen() {
		$this->load->model('setting/setting');

		$config_key = sha1('shipstation' . time() . HTTP_CATALOG);
		$verify_key = md5($config_key . DIR_APPLICATION);

		$data = array(
			'shipstation_status'     => $this->config->get('shipstation_status'),
			'shipstation_config_key' => $config_key,
			'shipstation_verify_key' => $verify_key
		);

		$this->model_setting_setting->editSetting('shipstation', $data);
		
		$this->response->redirect($this->url->link('extension/shipstation', 'token=' . $this->session->data['token'], 'SSL'));

	}

	public function clear() {
		$this->load->language('module/shipstation');

		$file = DIR_LOGS . 'shipstation/' . $this->config->get('config_error_filename');

		$handle = fopen($file, 'w+'); 

		fclose($handle); 			

		$this->session->data['success'] = $this->language->get('text_cleared');

		$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));	
	}
}
?>
