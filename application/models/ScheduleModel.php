<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ScheduleModel extends CI_Model {

	function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
		
	}
	
	public function generateRespond($statusIn){
		
		$stat = array(
			'status' => $statusIn
		);
		
		return $stat;
	}
	
	public function getEmail($username){
		
		$dataEnd = null;
		
		$multiParam = array(
				'username' => $username
		);
			
		$this->db->where($multiParam);		
		
		$query = $this->db->get('data_user');
		
		foreach ($query->result() as $row)
		{
				$dataEnd = $row->email;
		}
		
		return $dataEnd;
		
	}
	
	public function getAllByDayWithEmail($dayIn){
		
		$endResult = $this->generateRespond('invalid');

		// this will limit the schedule of specific day only
			
		$multiParam = array(
				'day_schedule' => $dayIn
		);
			
		$this->db->where($multiParam);		
		
		$query = $this->db->get('data_schedule');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'class_registered' 		=> $row->class_registered,
				'time_schedule' 		=> $row->time_schedule,
				'day_schedule'			=> $row->day_schedule,
				'username'				=> $row->username
			);
			
			$data['email'] = $this->getEmail($data['username']);
			//echo var_dump($data['email']);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function getAllByDay($dayIn){
		
		$endResult = $this->generateRespond('invalid');

		// this will limit the schedule of specific day only
			
		$multiParam = array(
				'day_schedule' => $dayIn
		);
			
		$this->db->where($multiParam);		
		
		$query = $this->db->get('data_schedule');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'class_registered' 		=> $row->class_registered,
				'time_schedule' 		=> $row->time_schedule,
				'day_schedule'		=> $row->day_schedule,
				'username'		=> $row->username
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function isExam($className){
		
		// this will check on 
		// class_room table whether the schedule class
		// is actually a real EXAM instead of NORMAL CLASS
		
		$stat = false;
		
		$checked = array(
				'name' => $className
		);
			
		$this->db->where($checked);		
		
		$query = $this->db->get('data_class_room');
		
		foreach ($query->result() as $row)
		{
			
			$data = array(
				'name'			=> $row->name,
				'for_exam'		=> $row->for_exam
			);
			
			if($data['for_exam'] == 1){
				$stat = true;
			}
			
			break;
		}
		
		return $stat;
		
	}
	
	public function getAll($usernameIn){
		
		$endResult = $this->generateRespond('invalid');

			// this will limit the schedule of specific person only
			// otherwise admin will have all access
		if($usernameIn != 'admin'){
		
			$multiParam = array(
				'username' => $usernameIn
			);
			
			$this->db->where($multiParam);		
		
		}
		
		$query = $this->db->get('data_schedule');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$examStat = $this->isExam($row->class_registered);
			
			$data = array(
				'id' 		=> $row->id,
				'exam'		=> $examStat,
				'class_registered' 		=> $row->class_registered,
				'time_schedule' 		=> $row->time_schedule,
				'day_schedule'		=> $row->day_schedule,
				'username'		=> $row->username
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function add($username, $day, $time, $classReg, $examID){
		
		$stat = 'invalid';
		
		
		$data = array(
			'username' 			=> $username,
			'day_schedule' 		=> $day,
			'time_schedule' 	=> $time,
			'class_registered' 	=> $classReg
		);
		
		$this->db->insert('data_schedule', $data);
		$schedID = $this->db->insert_id();
		$stat = 'valid';
		
		// if exist for the exam cat id
		// thus we save the 2nd table database
		
		if(isset($examID)){
		
			$newData = array(
			'schedule_id' 		=> $schedID,
			'exam_category_id' 	=> $examID
			);
			
			$this->db->insert('data_exam_category_schedule', $newData);
		
		}
		
		return $this->generateRespond($stat);
	}
	
	public function delete($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_schedule');
		
		if($this->db->affected_rows() > 0){
				$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}
	
	public function getDetail($idIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'id' => $idIn
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_schedule');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'username' 			=> $row->username,
				'day_schedule'		=> $row->day_schedule,
				'time_schedule' 	=> $row->time_schedule,
				'class_registered' 	=> $row->class_registered
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function edit($id, $username, $day, $time, $classReg){
		
		$endRes = $this->generateRespond('invalid');
		
			$data = array(
			'username' 			=> $username,
			'day_schedule' 		=> $day,
			'time_schedule' 	=> $time,
			'class_registered' 	=> $classReg
			);
		
		$this->db->where('id', $id);
		$this->db->update('data_schedule', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
		
}