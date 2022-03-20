<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamQA extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ExamQAModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken()
	{
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ExamQAModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	public function allBySchedule(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$schedID	= $this->input->post('schedule_id');
		
		$endRespond = $this->ExamQAModel->getAllBySchedule($schedID);

		echo json_encode($endRespond);

	
		}
		
	}
	
	
	// this is for ADMIN
	public function add()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$question	= $this->input->post('question');
		$jenis 		= $this->input->post('jenis');
		$option_a 	= $this->input->post('option_a');
		$option_b 	= $this->input->post('option_b');
		$option_c 	= $this->input->post('option_c');
		$option_d 	= $this->input->post('option_d');
		
		// jenis is 
		// 1 for abcd
		// 2 for essay
		// 3 for ab only (true false)
		// 4 for typing
		// 5 for praktek (submit upload) - word
		// 6 for praktek (submit upload) - excel
		// 7 for praktek (submit upload) - ppoint
		// 8 for praktek (submit upload) - pdf
		// 9 for praktek (submit upload) - all-files
		
		$answer 	= $this->input->post('answer');
		$score_point 	= $this->input->post('score_point');
		$exam_cat_id 	= $this->input->post('exam_category_id');
		$exam_subcat_id 	= $this->input->post('exam_sub_category_id');
		
		//$preview 	= $this->input->post('preview');
		$filename		= 'exam-prev-default.png';
		
		$key 			= 'preview';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			// proceed the uploads...
			//$new_image_name = 'propic_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/exam-preview/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		$endRespond = $this->ExamQAModel->add($question, $jenis, $option_a, $option_b, $option_c, $option_d, $filename, $answer, $score_point, $exam_cat_id, $exam_subcat_id);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ExamQAModel->delete($idIn);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ExamQAModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update()
	{
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		
		$question	= $this->input->post('question');
		$jenis 		= $this->input->post('jenis');
		$option_a 	= $this->input->post('option_a');
		$option_b 	= $this->input->post('option_b');
		$option_c 	= $this->input->post('option_c');
		$option_d 	= $this->input->post('option_d');
		
		$answer 	= $this->input->post('answer');
		$score_point 	= $this->input->post('score_point');
		$exam_cat_id 	= $this->input->post('exam_category_id');
		$exam_subcat_id 	= $this->input->post('exam_sub_category_id');
		
		//$preview 	= $this->input->post('preview');
		$filename		= null;
		
		$key 			= 'preview';
		
		if(isset($_FILES[$key])) {
			// if there file given'
			// proceed the uploads...
			//$new_image_name = 'propic_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/exam-preview/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		}
		
		$endRespond = $this->ExamQAModel->edit($id, $question, $jenis, $option_a, $option_b, $option_c, $option_d, $filename, $answer, $score_point, $exam_cat_id, $exam_subcat_id);
		
		
		echo json_encode($endRespond);
		
		}
		
	}

	// this is for ADMIN 
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$data = $this->ExamQAModel->getAll();
		echo json_encode($data);
		
		}
	}
	
	// examqa/preview
	public function preview(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('propic');
		//$file = 'logo.png';
		$file 	= $this->input->get('preview');
		
		$targetFile = 'images/exam-preview/' . $file;
		
		force_download($targetFile,NULL);
		
	}
	
	// examqa/preview/delete
	public function deletePreview(){
		$v = $this->validateToken();
		
		if($v){
		
		
		$idIn 			= $this->input->post('id');
		
		$endRespond 	= $this->ExamQAModel->deletePreview($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
}
