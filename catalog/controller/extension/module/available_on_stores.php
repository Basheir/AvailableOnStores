<?php

/**
 * Created by Basheir Hassan.
 * User: basheir
 * Version 1.1.0
 */



class ControllerExtensionModuleAvailableOnStores extends Controller
{


    public function index()
    {


        $statUS = $this->config->get('available_on_stores_status');

        if ($statUS) {

            $this->addClicked();
        }

    }


    public function addClicked()
    {



        $result = false;

        if (isset($this->request->post['product_id']) AND !empty($this->request->post['product_id']) AND $this->request->post['product_id'] != 0 AND isset($this->request->post['stores_id']) AND !empty($this->request->post['stores_id']) AND $this->request->post['stores_id'] != 0) {

            $product_id = (int)$this->request->post['product_id'];
            $stores_id = (int)$this->request->post['stores_id'];
            $this->db->query("INSERT INTO `" . DB_PREFIX . "available_on_stores_dashboard` (`product_id`,`stores_id`) VALUES ($product_id,$stores_id);");
            $result = $this->db->countAffected();

        }


        echo json_encode(array('result' => $result));


    }

    public function getStoreUrls(){



        $statUS = $this->config->get('available_on_stores_status');
	    $theme = $this->config->get('config_theme') ;
	
	
	    if ($statUS) {
            if (isset($this->request->get['product_id']) AND !empty($this->request->get['product_id']) and $this->request->get['product_id'] != 0) {

                $id = (int)$this->request->get['product_id'];
                $result = $this->db->query("SELECT *

												FROM
												`" . DB_PREFIX . "available_on_stores_urls` `" . DB_PREFIX . "available_on_stores_urls`
												INNER JOIN  `oc_available_on_stores`
												ON `" . DB_PREFIX . "available_on_stores_urls`.`stores_id` = `" . DB_PREFIX . "available_on_stores`.
												`stores_id` WHERE `product_id` = '$id' ")->rows;




                
                if ($this->db->countAffected() > 0) {
	                $json = array('result' => $result,'theme'=>$theme);
                } else {
                    $json = array('result' => false,'theme'=>$theme);

                }
	
	            $this->response->addHeader('Content-Type: application/json');
	            $this->response->setOutput(json_encode($json));
             
             
            }

        }
	    
	    
	    else {
		   echo 'Module Not enabled';
	    }





    }
	
	
	





}
