<?php

/**
 * Created by Basheir Hassan.
 * Version 1.2.1
 */






class ModelExtensionModuleAvailableOnStores extends Model {




	public function getStores() {
		return $this->db->query( "SELECT * from `" . DB_PREFIX . "available_on_stores` ORDER BY `stores_id` ASC;" )->rows;

	}


	public function addStores($name) {
	
	return $this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores (`name`) VALUES ('". $this->db->escape($name) . "')" );
	
	}


	/*
	 * delete Urls And Dashboard
	 */

	public function deleteStores($store_id) {

		$this->db->query( "DELETE from `" . DB_PREFIX . "available_on_stores` WHERE `stores_id` = '". $store_id . "';" );
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `stores_id` ='". $store_id."' "  );
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_dashboard   WHERE  `stores_id` ='". $store_id."' "  );
		
		return  $this->db->countAffected();
		
	}
	
	
	
	

	public function updateStores($name,$id) {


		 $this->db->query( "UPDATE  `" . DB_PREFIX
		                  . "available_on_stores` SET `name` = '"
		                  . $this->db->escape( $name )
		                  . "'  WHERE `stores_id` = '"
		                  . $id
		                  . "';" );
		
		return $this->db->countAffected() ;
	}




	// -------------------------------------------


	
	public function addUrls($url,$product_id ,$stores_id) {
	
	return $this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores_urls (`url`,`product_id`,`stores_id`) VALUES ('".  $url . "','". $product_id  . "','". $stores_id  . "')" );
	

	}



	public function getUrls($product_id) {

		return  $this->db->query( "SELECT * from `" . DB_PREFIX . "available_on_stores_urls` WHERE  `product_id` ='". $product_id."' ;" )->rows;
	
	}


	
	
	public function editUrls($url, $product_id,$stores_id) {
	
	return 	$this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores_urls (`url`,`product_id`,`stores_id`) VALUES ('".  $url . "','". $product_id  . "','". $stores_id  . "')" );

	}





	public function deleteUrls($product_id) {
		
		return $this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `product_id` ='". $product_id."' "  );

	}


	
	
	

	//------------------- DashBorad
	
	
	
	
	public function getDashboardStore($ID) {
		
		return $this->db->query( "Select * from " . DB_PREFIX. "available_on_stores_dashboard   WHERE  `stores_id` ='".(int) $ID."' "  );
		
	}
	
	
	
	
	public function deleteDashboard($product_Id) {
		
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_dashboard   WHERE  `product_id` ='". $product_Id."' "  );
		
		return  $this->db->countAffected();
		
	}
	
	
	
	
	
	public function getDashboardAllData() {
		
		
		
		return $this->db->query("Select * from " . DB_PREFIX. "available_on_stores_dashboard GROUP BY  product_id,stores_id " )->rows;
		
	}
	
	
	
	public function getDashboardByPage($page=1, $limit=10) {
		
		if ($page) {
			$start = ($page - 1) * $limit;
		}
		
		return $this->db->query("Select * from " . DB_PREFIX. "available_on_stores_dashboard   LIMIT $start,$limit" )->rows;
		
	}
	
	
	
	
	
	public function getDashboardByProductIDAndStoresID($product_id,$stores_id) {
		
		return  $this->db->query( "SELECT count(`product_id`) as Total FROM  `" . DB_PREFIX . "available_on_stores_dashboard` WHERE `product_id` ='". (int)$product_id."' and  `stores_id` ='". (int)$stores_id ."';" )->rows[0]['Total'];
		
		
	}
	
	

	
	
	
	
	public function getLastProductId() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product ORDER BY product_id DESC LIMIT 0,1");
		
		return $query->row['product_id'];
	}
	
	
	
	
	
}
