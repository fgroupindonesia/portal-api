<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bill extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('BillModel');
		$this->load->model('PaymentModel');
		$this->load->model('TokenEngineModel');
	}

	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->BillModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}

	// called by ADMIN
	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$amount			= $this->input->post('amount');
		$desc 			= $this->input->post('description');
		$username 		= $this->input->post('username');
		$status 		= $this->input->post('status');
		
		$endRespond = $this->BillModel->add($username, $amount, $desc, $status);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// called by ADMIN
	public function update(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$id				= $this->input->post('id');
		$amount			= $this->input->post('amount');
		$desc 			= $this->input->post('description');
		$username 		= $this->input->post('username');
		$status 		= $this->input->post('status');
		
		// here the admin has no access changing the image of payment / transfer
		
		$endRespond = $this->BillModel->edit($id, $username, $amount, $desc, $status);
		
		echo json_encode($endRespond);
		
		}
		
	}
	
	// called by CLIENT
	public function paid(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$id			= $this->input->post('id');
		$amount		= $this->input->post('amount');
		$desc 		= $this->input->post('description');
		$username 	= $this->input->post('username');
		// the status is automatically become pending
		$status 	= 'pending';
		
		// edit the bill data
		$endRespond = $this->BillModel->edit($id, $username, $amount, $desc, $status);
		
		if($endRespond['status'] == 'valid'){
			// proceed creating new entry for Payment
			$method			= 'Transfer Bank';
			
			$filename		= null;
		
			$key 			= 'screenshot';
			
			if(isset($_FILES[$key])) {
			// if there image given'
			// proceed the uploads...
			//$new_image_name = 'propic_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
			
			$new_image_name = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$filename = $new_image_name;
	
		$config['upload_path'] = 'images/payment/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
		$config['file_name'] = $new_image_name;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		// change the name accordingly
		//$propic = $filename;
			
		}
			
			// adding new payment data
			$endRespond = $this->PaymentModel->add($username, $amount, $method, $filename);
			
			
		}
		
		echo json_encode($endRespond);
		
		}
		
	}

	// bill/last
	// will obtain the specific user bills only in desc order
	public function all()
	{
		$v = $this->validateToken();
		
		if($v){
		$username 	= $this->input->post('username');
		
		$data = $this->BillModel->getAll($username);
		echo json_encode($data);
		
		}
	}
}
