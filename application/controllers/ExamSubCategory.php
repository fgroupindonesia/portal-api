<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamSubCategory extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ExamSubCategoryModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken()
	{
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ExamSubCategoryModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	// this is for ADMIN
	public function add()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$title				= $this->input->post('title');
		$exam_category_id 	= $this->input->post('exam_category_id');
		
		$endRespond = $this->ExamSubCategoryModel->add($title, $exam_category_id);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ExamSubCategoryModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ExamSubCategoryModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update()
	{
		$v = $this->validateToken();
		
		if($v){
			
		$id 					= $this->input->post('id');
		$title					= $this->input->post('title');
		$exam_category_id 		= $this->input->post('exam_category_id');
		
		$endRespond = $this->ExamSubCategoryModel->edit($id, $title, $exam_category_id);
		
		echo json_encode($endRespond);
		
		}
		
	}

	// this is for ADMIN 
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('exam_category_id');
		
		
		$data = $this->ExamSubCategoryModel->getAll($idIn);
		echo json_encode($data);
		
		}
	}
	
}
