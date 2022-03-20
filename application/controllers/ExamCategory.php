<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamCategory extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('ExamCategoryModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken()
	{
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ExamCategoryModel->generateRespond('invalid');
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
		
		$title	= $this->input->post('title');
		$code 	= $this->input->post('code');
		$sbase 	= $this->input->post('score_base');
		
		// this is for sub_category data
		// it's coming from json-array values
		$dataMasuk 	= $this->input->post('json');
		
		// initiated with a null
		$dataJSON = null;
		
		if(isset($dataMasuk)){
		// data changed when receiving values
		$dataJSON = json_decode($dataMasuk, true);
		}
		
		$endRespond = $this->ExamCategoryModel->add($title, $code, $sbase, $dataJSON);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ExamCategoryModel->delete($idIn);
		
		
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ExamCategoryModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update()
	{
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$title			= $this->input->post('title');
		$code 			= $this->input->post('code');
		$score_base 	= $this->input->post('score_base');
		
		// this is for sub_category data
		// it's coming from json-array values
		$dataMasuk 	= $this->input->post('json');
		
		// initiated with a null
		$dataJSON = null;
		
		if(isset($dataMasuk)){
		// data changed when receiving values
		$dataJSON = json_decode($dataMasuk, true);
		}
		
		$endRespond = $this->ExamCategoryModel->edit($id, $title, $code, $score_base, $dataJSON);
		
		echo json_encode($endRespond);
		
		}
		
	}

	// this is for ADMIN 
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$data = $this->ExamCategoryModel->getAll();
		echo json_encode($data);
		
		}
	}
	
}
