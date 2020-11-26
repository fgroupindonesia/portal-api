<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('AttendanceModel');
		$this->load->model('TokenEngineModel');
	}

	// this is for ADMIN
	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$status			= $this->input->post('status');
		$username 		= $this->input->post('username');
		$classReg 		= $this->input->post('class_registered');
		
		$filename		= null;
		
		$key 			= 'signature';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/attendance/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		
		$endRespond = $this->AttendanceModel->add($username, $status, $filename, $classReg);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->AttendanceModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->AttendanceModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update(){
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$status			= $this->input->post('status');
		$username 		= $this->input->post('username');
		$classReg 		= $this->input->post('class_registered');
		
		$filename		= null;
		
		$key 			= 'signature';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			// proceed the uploads...
			//$new_image_name = 'propic_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/attendance/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		$endRespond = $this->AttendanceModel->edit($id, $username, $status, $filename, $classReg);
		
		echo json_encode($endRespond);
		
		}
		
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->AttendanceModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}

	// attendance/all
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$username = $this->input->post('username');
		
		$data = $this->AttendanceModel->getAll($username);
		echo json_encode($data);
		
		}
	}

	// attendance/signature
	public function signature(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('signature');
		//$file = 'logo.png';
		$file 	= $this->input->get('signature');
		
		$targetFile = 'images/attendance/' . $file;
		
		force_download($targetFile,NULL);
		
	}	

}
