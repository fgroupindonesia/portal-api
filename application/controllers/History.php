<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('HistoryModel');
		$this->load->model('TokenEngineModel');
	}

	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->HistoryModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}

	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$desc			= $this->input->post('description');
		$username 		= $this->input->post('username');
		
		$endRespond = $this->HistoryModel->add($username, $desc);
		
		echo json_encode($endRespond);
		
		}
	}
	
	public function all(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$username 		= $this->input->post('username');
		
		// getting all data without limits
		$endRespond = $this->HistoryModel->getAll($username, null);
		
		echo json_encode($endRespond);
		
		}
	}

	// history/last
	public function last()
	{
		$v = $this->validateToken();
		
		if($v){
		$username 	= $this->input->post('username');
		$limit 		= $this->input->post('limit');
		
		$data = $this->HistoryModel->getAll($username, $limit);
		echo json_encode($data);
		
		}
	}
}
