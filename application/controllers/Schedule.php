<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('ScheduleModel');
		$this->load->model('TokenEngineModel');
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

	// attendance/all
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
