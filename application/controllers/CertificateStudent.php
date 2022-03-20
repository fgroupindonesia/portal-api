<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CertificateStudent extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('CertificateStudentModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken()
	{
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->CertificateStudentModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	private function convertToTinyURL($filename){
		
		// here we shorten the url from the current server location because
		// the user just upload the document here
		// un comments these lines of code if the SERVER is REAL LIVE 
		/*$url_parts = parse_url(current_url());
		$validCurrentDomain = $url_parts['scheme'] . '://' . str_replace('www.', '', $url_parts['host']);
		$realLink = $validCurrentDomain . "/images/certificate/" . $filename;
		$anURL = $realLink;
		*/// un comment until HERE
		
		// otherwise if you're still under PRODUCTION SERVER (locally in WIFI - LAN)
		// use the below code only
		$validCurrentDomain = 'http://192.168.0.8';
		$realLink = $validCurrentDomain . "/images/certificate/" . $filename;
		$anURL = $realLink;
		
		// RTO by 30 seconds
		
		//$anURL = "http://fgroupindonesia.com";
		$alamat = 'http://tinyurl.com/api-create.php?url='.$anURL;
		$output = file_get_contents($alamat, 0, stream_context_create(["http"=>["timeout"=>30]]));
		//echo $output;
		return $output;
	}
	
	private function convertHumanReadable($dataIn){
	$n =	urldecode($dataIn);
	// removing the + character 
	return preg_replace('/[\s\+]/', ' ', $n);
	}
	
	// this is for ADMIN
	public function add()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$username	= $this->input->post('username');
		$exam_category_id 	= $this->input->post('exam_category_id');
		$exam_category_title	= $this->input->post('exam_category_title');
		
		$exam_category_title = $this->convertHumanReadable(urldecode($exam_category_title));
		
		
		$status 	= $this->input->post('status');
		$ex_date 	= $this->input->post('exam_date_created');
	
		$propic		= 'cert-default.png';
		
		$key 		= 'filename';
		
		if(isset($_FILES[$key])) {
		
			$propic = $this->uploadCertificate($propic, $key);
			$url = $this->convertToTinyURL($propic);
	
		}
		
		$endRespond = $this->CertificateStudentModel->add($username, $exam_category_id, $exam_category_title, $status, $propic, $ex_date, $url);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->CertificateStudentModel->delete($idIn);
		
		
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->CertificateStudentModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	private function uploadCertificate($propic, $key){
		
			
		$new_image_name = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$filename = $new_image_name;
	
		$config['upload_path'] = 'images/certificate/'; 
		$config['allowed_types'] = 'pdf|PDF|jpg|jpeg|png|PNG|JPEG|JPG';
		$config['file_name'] = $new_image_name;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		// change the name accordingly
		$propic = $filename;
			
		return $propic;
		
	}
	
	// this is for ADMIN
	public function update()
	{
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$username	= $this->input->post('username');
		$exam_category_id 	= $this->input->post('exam_category_id');
		$exam_category_title	= $this->input->post('exam_category_title');
		$status 	= $this->input->post('status');
		$ex_date 	= $this->input->post('exam_date_created');
	
		$propic		= 'cert-default.png';
		
		$key 		= 'filename';
		
		if(isset($_FILES[$key])) {
		
			$propic = $this->uploadCertificate($propic, $key);
			$url = $this->convertToTinyURL($propic);
		
		}
		
		$endRespond = $this->CertificateStudentModel->edit($id, $username, $exam_category_id, $exam_category_title, $status, $propic, $ex_date, $url);
		
		
		echo json_encode($endRespond);
		
		}
		
	}

	// this is for ADMIN 
	public function all()
	{
		
		$username 	= $this->input->post('username');
		
		$v = $this->validateToken();
		
		if($v){
		
		$data = $this->CertificateStudentModel->getAll($username);
		echo json_encode($data);
		
		}
	}
	
	// certificatestudent/picture/
	public function picture(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('propic');
		//$file = 'logo.png';
		$file 	= $this->input->get('filename');
		
		$targetFile = 'images/certificate/' . $file;
		
		force_download($targetFile,NULL);
		
	}
	
	// certificatestudent/picture/delete
	public function deletePicture(){
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->CertificateStudentModel->deletePicture($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
}
