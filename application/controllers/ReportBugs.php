<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportBugs extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('ReportBugsModel');
		$this->load->model('TokenEngineModel');
	}

	// this is for ADMIN
	public function add(){
	
		$v = $this->validateToken();
		
		if($v){
		
		
		$app_name		= $this->input->post('app_name');
		$username 		= $this->input->post('username');
		$ip 			= $this->input->ip_address();
		$title 			= $this->input->post('title');
		$desc 			= $this->input->post('description');
		
		$filename		= null;
		
		$key 			= 'screenshot';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/bugs/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		
		$endRespond = $this->ReportBugsModel->add($app_name, $username, $ip, $title, $desc, $filename);
		
		
		
		echo json_encode($endRespond);
		
		}
		
		
	}
	
	// this is for ADMIN
	public function delete(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ReportBugsModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ReportBugsModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update(){
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$app_name		= $this->input->post('app_name');
		$username 		= $this->input->post('username');
		$ip 			= $this->input->ip_address();
		$title 			= $this->input->post('title');
		$desc 			= $this->input->post('description');
		
		$filename		= null;
		
		$key 			= 'screenshot';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/bugs/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		$endRespond = $this->ReportBugsModel->edit($id, $app_name, $username, $ip, $title, $desc, $filename);
		
		echo json_encode($endRespond);
		
		}
		
	}
	
	private function validateToken(){
		
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ReportBugsModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return ($valid);
	}

	// reportbugs/all
	public function all(){
		$v = $this->validateToken();
		
		if($v){
		
		$data = $this->ReportBugsModel->getAll();
		echo json_encode($data);
		
		}			
	}
	
	// reportbugs/screenshot
	public function screenshot(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('signature');
		//$file = 'logo.png';
		$file 	= $this->input->get('screenshot');
		
		$targetFile = 'images/bugs/' . $file;
		
		force_download($targetFile,NULL);
		
	}	

}