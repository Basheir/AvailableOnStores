<?php

/**
 * Created by Basheir Hassan.
 * User: basheir
 * Version 1.2.2
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
		$data['get_count_Deleted'] = html_entity_decode($this->url->link('extension/module/available_on_stores/getCountDelete', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		
		
		
		//		var_dump($r);
		//admin/model/extension/module/available_on_stores.php
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
		$this->model_setting_event->addEvent( 'available_on_stores_post_add', 'admin/model/catalog/product/addProduct/after', 'extension/module/available_on_stores/addUrlsEvent' );
		$this->model_setting_event->addEvent( 'available_on_stores_post_edit', 'admin/model/catalog/product/editProduct/after', 'extension/module/available_on_stores/editUrlsEvent' );
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
		
		$result = false;
		if ( isset( $this->request->post['available_on_stores_stores_name'] ) ) {
			
			$this->load->model('extension/module/available_on_stores');
			$query = $this->model_extension_module_available_on_stores->addStores($this->request->post['available_on_stores_stores_name']);
			
			$result = $this->db->countAffected();
			$lastID = $this->db->getLastId();
			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode([
			'result' => $result,
			'lastID' => $lastID,
			'name'   => $this->request->post['available_on_stores_stores_name'],
		]));
		

	}
	
	
	
	
	
	

	public function getStores() {

		$this->load->model('extension/module/available_on_stores');
		return $this->model_extension_module_available_on_stores->getStores();
	}
	
	
	

	public function deleteStores() {
		
		$store_id = (int)$this->request->post['id'];
		$this->load->model('extension/module/available_on_stores');
		$result = $this->model_extension_module_available_on_stores->deleteStores($store_id);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode([ 'result' => $result ]));
	
		
		

	}

	public function updateStores() {
		
		
		$this->load->model('extension/module/available_on_stores');
		$result = $this->model_extension_module_available_on_stores->updateStores($this->request->post['name'],(int)$this->request->post['id']);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode(['result' => $result ]));

	}


  /**
   * مجموع الجداول المحذوفة
   */
		public function getCountDelete() {
			
				$ID = (int)$this->request->post['id'];
				$this->load->model('extension/module/available_on_stores');
				
				
				
				$this->model_extension_module_available_on_stores->getUrls($ID);
				$res_url = $this->db->countAffected();
			
			
			
				$this->model_extension_module_available_on_stores->getDashboardStore($ID);
				$res_dashboard = $this->db->countAffected();
			
			
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode(array('url_count'=>$res_url,'dashboard_count'=>$res_dashboard)));
		
		}



		
		
		
	function addUrlsEvent($route, $data){
		
		$this->load->model('setting/setting');
		$this->load->model('extension/module/available_on_stores');
		
		
		if (!$this->config->get('available_on_stores_status')){
			return;
		}
		
		if (isset($data[0]['available-on-stores-input'])) {
			
			$product_id = $this->model_extension_module_available_on_stores->getLastProductId();
			
			foreach ($data[0]['available-on-stores-input'] as $key => $product) {
				if(!empty(trim($product))){
					
					$stores_id = $key;
					$url = $this->db->escape($product);
					$this->model_extension_module_available_on_stores->addUrls($url,$product_id ,$stores_id);
					
				}
				
			}
			
			
		}
		
		
	}


	function editUrlsEvent($route, $data){
		
		
		
		$this->load->model('extension/module/available_on_stores');
		$product_id = $data[0];
		
		$this->load->model('setting/setting');
		
		if (!$this->config->get('available_on_stores_status')){
			return;
		}
		
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `product_id` ='". $product_id."' "  );
		
		
		if (isset($data[1]['available-on-stores-input'])) {
			
			
			foreach ($data[1]['available-on-stores-input'] as $key => $product) {
				if(!empty(trim($product))){
					
					$stores_id = $key;
					$url = $this->db->escape($product);
					$this->model_extension_module_available_on_stores->editUrls($url, $product_id,$stores_id);
					
					
				}
				
			}
			
			
		}
		
		

	}


	function deleteProductEvent($route, $data){
		
		$this->load->model('extension/module/available_on_stores');
		$product_id = (int)$data[0];
		
		$this->load->model('setting/setting');
		
		if (!$this->config->get('available_on_stores_status')){
			return;
		}
		
		$this->model_extension_module_available_on_stores->deleteUrls($product_id);
		$this->model_extension_module_available_on_stores->deleteDashboard($product_id);
		
	}


  /**
   * عرض النقرات في صفجة الداشبورد
   */

	function dashboard(){
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			
		} else {
			$page = 1;
		}
		
		$limit = 10;
		
		
		
		$this->load->model('catalog/product');
		$this->load->model('extension/module/available_on_stores');

		


		//  جلب الستورات مثلا سوق كوم او ايبياي او امازون مسجلة بقاعدة البيانات
		$stores = $this->model_extension_module_available_on_stores->getStores();



		$array_stores = array();
		foreach ($stores as $store ) {
			$stores_id = $store['stores_id'];
			$array_stores[$stores_id]=$store['name'];
		}
		





		$results_dashboard =  $this->model_extension_module_available_on_stores->getDashboardByPage($page, $limit);




	
		foreach ($results_dashboard as $result ) {
			
			$stores_id = $result['stores_id'];
			$product_id = $result['product_id'];
			$store_name = $array_stores[$stores_id];
			$clicked = $this->model_extension_module_available_on_stores->getDashboardByProductIDAndStoresID($product_id,$stores_id);
			$product_name  = $this->model_catalog_product->getProduct($product_id)['name'];
			
			$data['rows'][$product_id][$stores_id] = array(	'store_name'=> $store_name	,'clicekd'=> $clicked);
			$data['rows'][$product_id]['name'] = $product_name;
			$data['rows'][$product_id]['url'] = $this->url->link( 'catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product_id, true );
			$data['rows'][$product_id]['product_page'] = '../index.php?route=product/product&product_id='.$product_id;
			$data['rows'][$product_id]['getChart'] = htmlspecialchars_decode($this->url->link('extension/module/available_on_stores/getChart', 'user_token=' . $this->session->data['user_token'].'&product_id=' . $product_id, 'SSL'));

			
		}
		
		
		
		
		
		
		// جلب عدد الجداول
		$count_rows =  $this->model_extension_module_available_on_stores->getCountAllRows();




		$data['pagination'] = '';
		$pagination = new Pagination();
		$pagination->total = $count_rows;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('extension/module/available_on_stores/dashboard', 'product_id=' . @$this->request->get['product_id'] .'&user_token=' . $this->session->data['user_token']. '&page={page}');
		$data['pagination'] = $pagination->render();

		
		
		$this->load->language('extension/module/available_on_stores');
		$this->document->setTitle($this->language->get('heading_title'));
		$data['header'] =      $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] =      $this->load->controller('common/footer');
			

		$this->response->setOutput($this->load->view('extension/module/available_on_stores_dashboard', $data));

	}
	



}


