<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClassRoom extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('ClassRoomModel');
		$this->load->model('TokenEngineModel');
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ClassRoomModel->generateRespond('invalid');
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
		
		$instructor_id	= $this->input->post('instructor_id');
		$name 			= $this->input->post('name');
		$fexam 			= $this->input->post('for_exam');
		$description 	= $this->input->post('description');
		
		$endRespond = $this->ClassRoomModel->add($instructor_id, $name, $fexam, $description);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ClassRoomModel->delete($idIn);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ClassRoomModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update()
	{
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$instructor_id	= $this->input->post('instructor_id');
		$name 			= $this->input->post('name');
		$fexam 			= $this->input->post('for_exam');
		$description 	= $this->input->post('description');
		
		$endRespond = $this->ClassRoomModel->edit($id, $instructor_id, $name, $fexam, $description);
		
		echo json_encode($endRespond);
		
		}
		
	}

	// classroom/all
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
	
	
		$data = $this->ClassRoomModel->getAll();
		echo json_encode($data);
		
		}
	}
}