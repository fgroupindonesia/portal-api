<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('DocumentModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->DocumentModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	

	// document/all
	public function all()
	{
		$v = $this->validateToken();
		
		if($v){
		
		$username = $this->input->post('username');
		
		$data = $this->DocumentModel->getAll($username);
		echo json_encode($data);
		
		}
	}
}
