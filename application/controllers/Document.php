<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('DocumentModel');
		$this->load->model('TokenEngineModel');
	}
	
	
	private function convertHumanReadable($dataIn){
	$n =	urldecode($dataIn);
	// removing the + character 
	return preg_replace('/[\s\+]/', ' ', $n);
	}
	
	private function convertToTinyURL($filename){
		
		// here we shorten the url from the current server location because
		// the user just upload the document here
		// un comments these lines of code if the SERVER is REAL LIVE 
		/*$url_parts = parse_url(current_url());
		$validCurrentDomain = $url_parts['scheme'] . '://' . str_replace('www.', '', $url_parts['host']);
		$realLink = $validCurrentDomain . "/documents/" . $filename;
		$anURL = $realLink;
		*/// un comment until HERE
		
		// otherwise if you're still under PRODUCTION SERVER (locally in WIFI - LAN)
		// use the below code only
		$validCurrentDomain = 'http://192.168.0.8';
		$realLink = $validCurrentDomain . "/documents/" . $filename;
		$anURL = $realLink;
		
		// RTO by 30 seconds
		
		//$anURL = "http://fgroupindonesia.com";
		$alamat = 'http://tinyurl.com/api-create.php?url='.$anURL;
		$output = file_get_contents($alamat, 0, stream_context_create(["http"=>["timeout"=>30]]));
		//echo $output;
		return $output;
	}
	
	private function uploadDocument($filename, $key){
				// if there file given'
			// proceed the uploads...
			//$new_image_name = 'propic_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'documents/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		return $filename;
	}
	
	// this is for ADMIN
	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$title 			= $this->convertHumanReadable($this->input->post('title'));
		$description	= $this->convertHumanReadable($this->input->post('description'));
		$username 		= $this->input->post('username');
		$url 			= $this->input->post('url');
		
		$filename		= null;
		
		$key 			= 'document';
		
		if(isset($_FILES[$key])) {
	
		$filename = $this->uploadDocument($filename, $key);
		$url = $this->convertToTinyURL($filename);
	
		}
		
		$endRespond = $this->DocumentModel->add($title, $description, $username, $url, $filename);
		
		echo json_encode($endRespond);
		
		}
	}
	
	
	// this is for ADMIN
	public function delete(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->DocumentModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->DocumentModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	
	// this is for ADMIN
	public function update(){
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$title 			= $this->convertHumanReadable($this->input->post('title'));
		$description	= $this->convertHumanReadable($this->input->post('description'));
		$username 		= $this->input->post('username');
		$url 			= $this->input->post('url');
		$filename		= null;
		
		$key 			= 'document';
		
		if(isset($_FILES[$key])) {
		
			$filename = $this->uploadDocument($filename, $key);
			$url = $this->convertToTinyURL($filename);
	
		
		}
		
		$endRespond = $this->DocumentModel->edit($id, $title, $description, $username, $url, $filename);
		
		echo json_encode($endRespond);
		
		
		}
		
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
