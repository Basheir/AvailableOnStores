<?php

/**
 * Created by Basheir Hassan.
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

		$this->db->query( "DELETE from `" . DB_PREFIX . "available_on_stores` WHERE `stores_id` = '". $this->db->escape($store_id). "';" );
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `stores_id` ='". $this->db->escape($store_id)."' "  );
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_dashboard   WHERE  `stores_id` ='". $this->db->escape($store_id)."' "  );
		
		return  $this->db->countAffected();
		
	}
	
	
	
	

	public function updateStores($name,$id) {


		 $this->db->query( "UPDATE  `" . DB_PREFIX
		                  . "available_on_stores` SET `name` = '"
		                  . $this->db->escape( $name )
		                  . "'  WHERE `stores_id` = '"
		                  . $this->db->escape($id)
		                  . "';" );
		
		return $this->db->countAffected() ;
	}




	// -------------------------------------------


	
	public function addUrls($url,$product_id ,$stores_id) {
	
	return $this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores_urls (`url`,`product_id`,`stores_id`) VALUES ('". $this->db->escape($url) . "','". $this->db->escape($product_id)  . "','". $this->db->escape($stores_id)  . "')" );
	

	}



	public function getUrls($product_id) {

		return  $this->db->query( "SELECT * from `" . DB_PREFIX . "available_on_stores_urls` WHERE  `product_id` ='". $this->db->escape($product_id)."' ;" )->rows;
	
	}


	
	
	public function editUrls($url, $product_id,$stores_id) {
	
	return 	$this->db->query( "INSERT INTO " . DB_PREFIX. "available_on_stores_urls (`url`,`product_id`,`stores_id`) VALUES ('".  $this->db->escape($url) . "','". $this->db->escape($product_id)  . "','". $this->db->escape($stores_id)  . "')" );

	}





	public function deleteUrls($product_id) {
		
		return $this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_urls   WHERE  `product_id` ='".$this->db->escape($product_id)."' "  );

	}


	
	
	

	//------------------- DashBorad
	
	
	
	
	public function getDashboardStore($ID) {
		
		return $this->db->query( "SELECT * from " . DB_PREFIX. "available_on_stores_dashboard   WHERE  `stores_id` ='".$this->db->escape($ID)."' "  );
		
	}
	
	
	
	
	public function deleteDashboard($product_Id) {
		
		$this->db->query( "DELETE from " . DB_PREFIX. "available_on_stores_dashboard   WHERE  `product_id` ='".$this->db->escape($product_Id)."' "  );
		
		return  $this->db->countAffected();
		
	}
	
	
	
	
	/**
	public function getDashboardAllData() {

	return $this->db->query("Select COUNT(*) from " . DB_PREFIX. "available_on_stores_dashboard GROUP BY  product_id,stores_id " )->rows;

	}
 * /





  /**
   * @return mixed جلب عدد الجداول كاملة
   */
	public function getCountAllRows() {

	  $this->db->query("SELECT `product_id`,`stores_id`,`stores_id`,`date`,COUNT(`product_id`) as count from " . DB_PREFIX. "available_on_stores_dashboard group by `product_id`,`stores_id`" )->rows;
		return $this->db->countAffected();

	}



	
	
	
	public function getDashboardByPage($page=1, $limit=10) {
		
		if ($page) {
			$start = ($page - 1) * $limit;
		}
		
		return $this->db->query("SELECT `product_id`,`stores_id`,`stores_id`,`date`,COUNT(`product_id`) as totalClicked from " . DB_PREFIX. "available_on_stores_dashboard group by `product_id`,`stores_id` order by `totalClicked` desc   LIMIT " .$this->db->escape($start).",".$this->db->escape($limit))->rows;
		
	}


  /**
   * @param $product_id رقم المنتج
   * @param $stores_id رقم الستور
   * جلب مجموع عدد النقرات حسب المنتج
   * @return mixed
   */

	public function getDashboardByProductIDAndStoresID($product_id,$stores_id) {
		
		return  $this->db->query( "SELECT count(`product_id`) as Total FROM  `" . DB_PREFIX . "available_on_stores_dashboard` WHERE `product_id` ='". $this->db->escape($product_id)."' and  `stores_id` ='". $this->db->escape($stores_id) ."';" )->rows[0]['Total'];
		
		
	}
	
	

	
	
	
	
	public function getLastProductId() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product ORDER BY product_id DESC LIMIT 0,1");
		
		return $query->row['product_id'];
	}
	
	
	
	

}
