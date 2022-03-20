<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamSA extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ExamSAModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken()
	{
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ExamSAModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	private function unggah($keyPassed){
		
		$filename		= null;
		
		$key 			= $keyPassed;
		
		if(isset($_FILES[$key])) {
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/exam-answer/'; 
		$config['allowed_types'] = 'jpg|png|jpeg|pdf|ppt|pptx|doc|docx|xls|xlsx|txt';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		return $filename;
		
	}
	
	// this is for ADMIN
	public function add()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$username			= $this->input->post('username');
		$exam_qa_id 		= $this->input->post('exam_qa_id');
		$answer 			= $this->input->post('answer');
		$score_earned 		= $this->input->post('score_earned');
		$status 			= $this->input->post('status');
		$fileupload 		= $this->unggah('fileupload');
		
		$endRespond 		= $this->ExamSAModel->add($username, $exam_qa_id, $answer, $score_earned, $status, $fileupload);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ExamSAModel->delete($idIn);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ExamSAModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update()
	{
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		
		$username			= $this->input->post('username');
		$exam_qa_id 		= $this->input->post('exam_qa_id');
		$answer 			= $this->input->post('answer');
		$score_earned 		= $this->input->post('score_earned');
		$status 			= $this->input->post('status');
		
		$filename = $this->uploadFile('fileupload');
		
		$endRespond = $this->ExamSAModel->edit($id, $username, $exam_qa_id, $answer, $score_earned, $status, $filename);
		
		echo json_encode($endRespond);
		
		}
		
	}

	// this is for ADMIN 
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$data = $this->ExamSAModel->getAll();
		echo json_encode($data);
		
		}
	}
	
	
}
