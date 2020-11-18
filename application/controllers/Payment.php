<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('PaymentModel');
		$this->load->model('TokenEngineModel');
	}

	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->PaymentModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}

	// payment/all
	public function all()
	{
		$v = $this->validateToken();
		
		if($v){
		
		$username = $this->input->post('username');
		
		$data = $this->PaymentModel->getAll($username);
		echo json_encode($data);
		
		} 
	}
	
	// payment/last
	public function last(){
		
		$v = $this->validateToken();
		
		if($v){
		$username = $this->input->post('username');
		
		$data = $this->PaymentModel->getLast($username);
		echo json_encode($data);
		
		} 
	}	
}
