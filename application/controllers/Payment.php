<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('PaymentModel');
		$this->load->model('TokenEngineModel');
		
	}

	// this is for ADMIN
	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$method			= $this->input->post('method');
		$username 		= $this->input->post('username');
		$amount 		= $this->input->post('amount');
		
		$filename		= null;
		
		$key 			= 'screenshot';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/payment/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		
		$endRespond = $this->PaymentModel->add($username, $amount, $method, $filename);
		
		
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->PaymentModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->PaymentModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update(){
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$method			= $this->input->post('method');
		$username 		= $this->input->post('username');
		$amount 		= $this->input->post('amount');
		
		$filename		= null;
		
		$key 			= 'screenshot';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/payment/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		$endRespond = $this->PaymentModel->edit($id, $username, $amount, $method, $filename);
		
		echo json_encode($endRespond);
		
		}
		
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

	// payment/screenshot
	public function screenshot(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('signature');
		//$file = 'logo.png';
		$file 	= $this->input->get('screenshot');
		
		$targetFile = 'images/payment/' . $file;
		
		force_download($targetFile,NULL);
		
	}	

	public function deleteScreenshot(){
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->PaymentModel->deleteScreenshot($idIn);
		echo json_encode($endRespond);
		
		}
	}
}