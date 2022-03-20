<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClassRoomModel extends CI_Model {

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
	
	public function add($instructor_id, $name, $fexam, $description){
		
		$stat = 'invalid';
		
			$data = array(
				'instructor_id'	=> $instructor_id,
				'name'			=> $name,
				'for_exam'		=> $fexam,
				'description' 	=> $description
			);
		
		$this->db->insert('data_class_room', $data);
		
		$idExamNew = $this->db->insert_id();
	
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $instructor_id, $name, $fexam, $description){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
			'instructor_id' 	=> $instructor_id,
			'name' 				=> $name,
			'for_exam'			=> $fexam,
			'description' 		=> $description
		);
		
		$this->db->where('id', $id);
		$this->db->update('data_class_room', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		
		
		return $endRes;
		
	}
	
	
	public function getInstructorName($anID){
		
		$nameObtained = 'unknown';
		
		// this is actually requesting the user with a access_level of 'instructor'
		
		// access_level = 1 = ADMIN
		// access_level = 2 = STUDENT
		// access_level = 3 = INSTRUCTOR
		
		$multiParam = array(
			'access_level' => 3,
			'id' => $anID
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_user');
		
		foreach ($query->result() as $row)
		{
			$nameObtained = $row->username;
		}
		
		return $nameObtained;
	}
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		$query = $this->db->get('data_class_room');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'for_exam'			=> $row->for_exam,
				'instructor_id' 	=> $row->instructor_id,
				'name' 				=> $row->name,
				'description'		=> $row->description,
				'date_created'		=> $row->date_created
			);
			
			// this is additional data
			// used for UI Swing on Client-Side
			// if matched with access_level
			
			// this is actually requesting the user with a access_level of 'instructor'
		
			// access_level = 1 = ADMIN
			// access_level = 2 = STUDENT
			// access_level = 3 = INSTRUCTOR
			
			$data['instructor_name'] = $this->getInstructorName($row->instructor_id);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function delete($idIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_class_room');
		
		if($this->db->affected_rows() > 0){
				$endResult = $this->generateRespond('valid');
				
		}
		
		return $endResult;
	}
	
	public function getDetail($idIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'id' => $idIn
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_class_room');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'name' 			=> $row->name,
				'for_exam'		=> $fexam,
				'description'	=> $row->description,
				'instructor_id'		=> $row->instructor_id,
				'instructor_name'		=> $this->getInstructorName($row->instructor_id),
				'date_created' 	=> $row->date_created
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	
}