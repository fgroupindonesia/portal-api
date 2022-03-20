<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('ScheduleModel');
		$this->load->model('TokenEngineModel');
	}
	
	// this is for ADMIN
	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$day		= $this->input->post('day_schedule');
		$username 	= $this->input->post('username');
		$time		= $this->input->post('time_schedule');
		$classReg 	= $this->input->post('class_registered');
		
		// this is optional
		$exam_category_id = $this->input->post('exam_category_id');
		
		
		
		
		$endRespond = $this->ScheduleModel->add($username, $day, $time, $classReg, $exam_category_id);
		
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function delete(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->ScheduleModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->ScheduleModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update(){
		$v = $this->validateToken();
		
		if($v){
			
		$id 		= $this->input->post('id');
		$day		= $this->input->post('day_schedule');
		$username 	= $this->input->post('username');
		$time		= $this->input->post('time_schedule');
		$classReg 	= $this->input->post('class_registered');
		
		$endRespond = $this->ScheduleModel->edit($id, $username, $day, $time, $classReg);
		
		echo json_encode($endRespond);
		
		}
		
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ScheduleModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}

	// this is for ADMIN
	// schedule/allday
	public function allday()
	{
			
		$v = $this->validateToken();
		
		if($v){
		
		$dayna = $this->input->post('day_schedule');
		
		$data = $this->ScheduleModel->getAllByDay($dayna);
		echo json_encode($data);
		
		}
	}

	// schedule/all
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$username = $this->input->post('username');
		
		$data = $this->ScheduleModel->getAll($username);
		echo json_encode($data);
		
		}
	}
}
