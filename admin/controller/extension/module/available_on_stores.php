<?php

/**
 * Created by Basheir Hassan.
 * Version 1.0.0
 */








class ControllerExtensionModuleAvailableOnStores extends Controller {
	private $error = array();

	public function index() {

		$this->load->language('extension/module/available_on_stores');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('available_on_stores', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}




		if (isset($this->error['available_on_stores_boot_token'])) {
			$data['error_no_key'] = $this->error['available_on_stores_boot_token'];
		} else {
			$data['error_no_key'] = '';
		}


		if(isset($this->error['available_on_stores_chat_ids'])){
			$data['error_no_chat_ids'] = $this->error['available_on_stores_chat_ids'];
		}
		else {
			$data['error_no_chat_ids'] = '';

		}





		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/available_on_stores', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/available_on_stores', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);



		//        $this->load->model('setting/setting');
		//        $setting = $this->model_setting_setting->getSetting('available_on_stores');



		$data['add_stores']    = html_entity_decode($this->url->link('extension/module/available_on_stores/addStores', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		$data['get_stores']    = html_entity_decode($this->url->link('extension/module/available_on_stores/getStores', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		$data['delete_stores'] = html_entity_decode($this->url->link('extension/module/available_on_stores/deleteStores', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		$data['update_stores'] = html_entity_decode($this->url->link('extension/module/available_on_stores/updateStores', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		$data['url_dashboard'] = html_entity_decode($this->url->link('extension/module/available_on_stores/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'));




//		$this->load->model('extension/module/available_on_stores');
//		$data['rows'] = $this->model_extension_module_available_on_stores->getStoresUrl();
		$data['rows'] = $this->getStores();







		//الكي الخاص باالتطبيق
		if(isset($this->request->post['available_on_stores_status'])) {
			$data['available_on_stores_status'] = $this->request->post['available_on_stores_status'];
		} elseif ($this->config->get('available_on_stores_status')){
			$data['available_on_stores_status'] = $this->config->get('available_on_stores_status');
		} else{
			$data['available_on_stores_status'] = '';
		}








		$data['header'] =      $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] =      $this->load->controller('common/footer');


		$this->response->setOutput($this->load->view('extension/module/available_on_stores', $data));
	}



	public function install(){
		$this->load->model( 'setting/event' );
		$this->model_setting_event->addEvent( 'available_on_stores_post_add', 'admin/model/catalog/product/addProduct/after', 'extension/module/available_on_stores/addStoresUrlsEvent' );
		$this->model_setting_event->addEvent( 'available_on_stores_post_edit', 'admin/model/catalog/product/editProduct/after', 'extension/module/available_on_stores/editStoresUrlsEvent' );
		$this->model_setting_event->addEvent( 'available_on_stores_post_delete', 'admin/model/catalog/product/deleteProduct/after', 'extension/module/available_on_stores/deleteProductEvent' );




		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "available_on_stores` (
            `stores_id` int(2) NOT NULL,
            `name` varchar(250) NOT NULL,
            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;"
		);


		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "available_on_stores_dashboard` (
           	`dashboard_id` int(11) NOT NULL,
            `product_id` int(11) NOT NULL,
            `stores_id` int(2) NOT NULL,
            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;"
		);


		$this->db->query(
			"CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "available_on_stores_urls` (
            `urls_id` int(11) NOT NULL,
            `url` varchar(9999) NOT NULL,
            `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `stores_id` int(2) NOT NULL,
            `product_id` int(9) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
        
        "
		);
		
		
		
		
		
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "available_on_stores`           ADD PRIMARY KEY (`stores_id`);");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "available_on_stores`           MODIFY `stores_id` int(2) NOT NULL AUTO_INCREMENT;");
		
		
		
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "available_on_stores_dashboard`  ADD PRIMARY KEY (`dashboard_id`);");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "available_on_stores_dashboard`  MODIFY `dashboard_id` int(11) NOT NULL AUTO_INCREMENT;");
		
		
		
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "available_on_stores_urls`       ADD PRIMARY KEY (`urls_id`);");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "available_on_stores_urls`       MODIFY `urls_id` int(11) NOT NULL AUTO_INCREMENT;");
		
	}



	public function uninstall(){
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('available_on_stores');
		$this->model_setting_event->deleteEventByCode('available_on_stores_post_add' );
		$this->model_setting_event->deleteEventByCode('available_on_stores_post_edit' );
		$this->model_setting_event->deleteEventByCode('available_on_stores_post_delete' );
	}



	protected function validate() {

		if (!$this->user->hasPermission('modify', 'extension/module/available_on_stores')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}





	public function addStores() {

		$this->load->model('extension/module/available_on_stores');
		$this->model_extension_module_available_on_stores->addStores();

	}

	public function getStores() {

		$this->load->model('extension/module/available_on_stores');
		return $this->model_extension_module_available_on_stores->getStores();
	}

	public function deleteStores() {

		$this->load->model('extension/module/available_on_stores');
		return $this->model_extension_module_available_on_stores->deleteStores();

	}

	public function updateStores() {

		$this->load->model('extension/module/available_on_stores');
		return $this->model_extension_module_available_on_stores->updateStores();

	}





	function addStoresUrlsEvent($route, $data){

		$this->load->model('extension/module/available_on_stores');
		$this->model_extension_module_available_on_stores->addStoresUrls($route, $data);

	}



	function editStoresUrlsEvent($route, $data){

		$this->load->model('extension/module/available_on_stores');
		$this->model_extension_module_available_on_stores->editStoresUrls($route, $data);

	}


	function deleteProductEvent($route, $data){

		$this->load->model('extension/module/available_on_stores');
		$this->model_extension_module_available_on_stores->deleteStoresUrls($route, $data);

	}

	function dashboard(){
		$this->load->model('extension/module/available_on_stores');
		$data['rows'] = $this->model_extension_module_available_on_stores->getDashboard();

		$this->load->language('extension/module/available_on_stores');
		$this->document->setTitle($this->language->get('heading_title'));
		$data['header'] =      $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] =      $this->load->controller('common/footer');



		$this->response->setOutput($this->load->view('extension/module/available_on_stores_dashboard', $data));

	}





}
