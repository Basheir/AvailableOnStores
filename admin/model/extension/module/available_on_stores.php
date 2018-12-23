<?php

/**
 * Created by Basheir Hassan.
 * Version 1.0.0
 */






class ModelExtensionModuleAvailableOnStores extends Model {




	public function getStores() {

		return $this->db->query( "SELECT * from `" . DB_PREFIX
		                         . "available_on_stores` ORDER BY `stores_id` ASC;" )->rows;

	}





	public function addStores() {

		$result = false;
		if ( isset( $this->request->post['available_on_stores_stores_name'] ) ) {
			$this->db->query( "INSERT INTO " . DB_PREFIX
			                  . "available_on_stores (`name`) VALUES ('"
			                  . $this->db->escape( $this->request->post['available_on_stores_stores_name'] )
			                  . "')" );
			$result = $this->db->countAffected();
			$lastID = $this->db->getLastId();

		}

		echo json_encode( [
			'result' => $result,
			'lastID' => $lastID,
			'name'   => $this->request->post['available_on_stores_stores_name'],
		] );

	}





	public function deleteStores() {

		$ID = (int)$this->request->post['id'];

		$this->db->query( "Delete from `" . DB_PREFIX . "available_on_stores` WHERE `stores_id` = '". $ID . "';" );
		echo json_encode( [ 'result' => $this->db->countAffected() ] );
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `urls_id` ='". $ID."' "  );


	}


	public function updateStores() {


		$this->db->query( "UPDATE  `" . DB_PREFIX
		                  . "available_on_stores` SET `name` = '"
		                  . $this->db->escape( $this->request->post['name'] )
		                  . "'  WHERE `stores_id` = '"
		                  . $this->db->escape( $this->request->post['id'] )
		                  . "';" );
		echo json_encode( [ 'result' => $this->db->countAffected() ] );

	}




	// -------------------------------------------

	/**
	 * add store url Event
	 * @param $route
	 * @param $data
	 */
	public function addStoresUrls($route, $data) {


		$this->load->model('setting/setting');

		if (!$this->config->get('available_on_stores_status')){
			return;
		}

		if (isset($data[0]['available-on-stores-input'])) {

			$product_id = $this->getLastProductId();

			foreach ($data[0]['available-on-stores-input'] as $key => $product) {
				if(!empty(trim($product))){

					$stores_id = $key;
					$url = $this->db->escape($product);
					$this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores_urls (`url`,`product_id`,`stores_id`) VALUES ('".  $url . "','". $product_id  . "','". $stores_id  . "')" );


				}

			}



		}


	}



	public function getStoresUrls() {


		if (!isset($this->request->get['product_id'])){
			return;
		}

		$product_id = (int)$this->request->get['product_id'];
		$result = $this->db->query( "SELECT * from `" . DB_PREFIX . "available_on_stores_urls` WHERE  `product_id` ='". $product_id."' ;" )->rows;
		//var_dump($result);
		$items = array();

		foreach ($result as $key    =>  $value){

		$items[$value['stores_id']] = $value['url'];

		}

		//var_dump($items);

		return $items;
	}


	public function editStoresUrls($route, $data) {


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
					$this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores_urls (`url`,`product_id`,`stores_id`) VALUES ('".  $url . "','". $product_id  . "','". $stores_id  . "')" );




				}

			}


		}


	}





	public function deleteStoresUrls($route, $data) {


		$product_id = (int)$data[0];

		$this->load->model('setting/setting');

		if (!$this->config->get('available_on_stores_status')){
			return;
		}

		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `product_id` ='". $product_id."' "  );

	}




	/**
	 * @return get last product ID
	 */
	public function getLastProductId() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product ORDER BY product_id DESC LIMIT 0,1");

		return $query->row['product_id'];
	}


	/**
	 *
	 */
	public function getDashboard() {


		$this->load->model('catalog/product');


		$items = array();

		$array_stores = array();
		foreach ($this->getStores() as $stores ) {
			$stores_id = $stores['stores_id'];
			$array_stores[$stores_id]=$stores['name'];
		}



//		$results =  $this->db->query( "SELECT `product_id`,`stores_id`,`date`,count(`product_id`) as Total FROM  `" . DB_PREFIX . "available_on_stores_ dashboard` GROUP BY `product_id`,`stores_id` ;" )->rows;

		$results_dashboard =  $this->db->query( "SELECT * FROM  `" . DB_PREFIX . "available_on_stores_dashboard`  ;" )->rows;

		$data = array();
		foreach ($results_dashboard as $result ) {

			$stores_id = $result['stores_id'];
			$product_id = $result['product_id'];
			$store_name = $array_stores[$stores_id];
			$clicked = $this->db->query( "SELECT count(`product_id`) as Total FROM  `" . DB_PREFIX . "available_on_stores_dashboard` WHERE `product_id` = $product_id and  `stores_id` = $stores_id;" )->rows[0]['Total'];
			$product_name  = $this->model_catalog_product->getProduct($product_id)['name'];

			$data[$product_id][$stores_id] = array(	'store_name'=> $store_name	,	'clicekd'=> $clicked);
			$data[$product_id]['name'] = $product_name;
			$data[$product_id]['url'] = $this->url->link( 'catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product_id, true );


		}

		//print_r($data);
		return $data;
//		print_r($results_stores);
//		print_r($results_dashboard);


	}






}
