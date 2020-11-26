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
	
	public function add($username, $day, $time, $classReg){
		
		$stat = 'invalid';
		
		$data = array(
			'username' 			=> $username,
			'day_schedule' 		=> $day,
			'time_schedule' 	=> $time,
			'class_registered' 	=> $classReg
		);
		
		$this->db->insert('data_schedule', $data);
		$stat = 'valid';
		
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