<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('UserModel');
		$this->load->model('PaymentModel');
		$this->load->model('TokenEngineModel');
		
	}
	
	public function test(){
		$name = $this->input->post('username');
		$A = $this->UserModel->generateNewToken($name);
		echo json_encode($A);
	
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->UserModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	public function uploadPicture($propic, $key){
		
			// if there image given'
			// proceed the uploads...
			//$new_image_name = 'propic_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
			
			$new_image_name = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$propic = $new_image_name;
	
		$config['upload_path'] = 'images/propic/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
		$config['file_name'] = $new_image_name;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		// change the name accordingly
		
			
		return $propic;
	}
	
	// this is for ADMIN
	public function register(){
		
		$username 	= $this->input->post('username');
		$pass 		= $this->input->post('password');
		$email 		= $this->input->post('email');
		$address 	= $this->input->post('address');
		$mobile 	= $this->input->post('mobile');
		
		$propic		= 'default.png';
		
		$key 		= 'propic';
		
		if(isset($_FILES[$key])) {
			$propic  = $this->uploadPicture($propic, $key);
		}
		
		$endRespond = $this->UserModel->add($username, $pass, $email, $address, $mobile, $propic);
		
		echo json_encode($endRespond);
		
	}
	
	public function update(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$id 		= $this->input->post('id');
		$username 	= $this->input->post('username');
		$pass 		= $this->input->post('password');
		$email 		= $this->input->post('email');
		$address 	= $this->input->post('address');
		$mobile 	= $this->input->post('mobile');
		
		$tmv_id 	= $this->input->post('tmv_id');
		$tmv_pass 	= $this->input->post('tmv_pass');
		
		$warn 	= $this->input->post('warning_status');
		
		$propic		= null;
		
		$key 		= 'propic';
		
		if(isset($_FILES[$key])) {
				$propic = $this->uploadPicture($propic, $key);
		}
		
		$endRespond = $this->UserModel->edit($id, $username, $pass, $email, $address, $mobile, $propic, $tmv_id, $tmv_pass, $warn);
		
		echo json_encode($endRespond);
		
		}
		
	}
	
	// this is for ADMIN
	public function all(){
		
	$endRespond 	=	$this->UserModel->getAll();
	echo json_encode($endRespond);
		
	}
	
	// this is for ADMIN
	public function delete(){
		
		$usernameIn 	= $this->input->post('username');
		$endRespond 	=	$this->UserModel->delete($usernameIn);
		echo json_encode($endRespond);
		
	}
	
	// user/picture
	public function picture(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('propic');
		//$file = 'logo.png';
		$file 	= $this->input->get('propic');
		var_dump($file);
		
		if(strlen($file) <= 1){
		// invalid process 
		echo json_encode($this->UserModel->generateRespond('invalid'));
		
		}else {
		// valid process starts here	
		$targetFile = 'images/propic/' . $file;
		
		force_download($targetFile,NULL);
		
		}
	}
	
	// user/picture/delete
	public function deletePicture(){
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->UserModel->deletePicture($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	public function profile(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$username 	= $this->input->post('username');
		
		$dataUsed = $this->UserModel->getProfile($username);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	public function login(){
		
		// we dont use token verification here
		// instead we generate the token here once they have a valid login cridentials
		
		$username 	= $this->input->post('username');
		$pass 		= $this->input->post('password');
		
		$keyGenerated = $this->UserModel->verify($username, $pass);
		
		echo json_encode($keyGenerated);
		
	}
	
	// procedure of Client Login
	
	
}