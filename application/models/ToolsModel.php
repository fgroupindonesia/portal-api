<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ToolsModel extends CI_Model {

	function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
	}
	
	public function generateRespond($statusIn){
		
		$stat = array(
			'status' => $statusIn
		);
		
		return $stat;
	}
	
	public function getDetail($appName){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'app_name' => $appName
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_tools');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'app_name' 		=> $row->app_name,
				'app_ver' 		=> $row->app_ver,
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	
}