<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamStudentModel extends CI_Model {

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
	
	public function add($student_username, $exam_qa_id, $answer, $status, $date_created){
		
		$stat = 'invalid';
		
			$data = array(
				'student_username'	=> $student_username,
				'exam_qa_id'		=> $exam_qa_id,
				'answer'		=> $answer,
				'status'		=> $status,
				'date_created'	=> $date_created
			);
		
		$this->db->insert('data_exam_student', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $student_username, $exam_qa_id, $answer, $status, $date_created){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
				'student_username'	=> $student_username,
				'exam_qa_id'		=> $exam_qa_id,
				'answer'			=> $answer,
				'status'			=> $status,
				'date_created'		=> $date_created
		);
		
		$this->db->where('id', $id);
		$this->db->update('data_exam_student', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		$query = $this->db->get('data_exam_student');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'student_username' 	=> $row->student_username,
				'exam_qa_id' 		=> $row->exam_qa_id,
				'status'		=> $row->status,
				'answer'		=> $row->answer,
				'date_created'	=> $row->date_created
			);
			
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
		$this->db->delete('data_exam_student');
		
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
	
		$query = $this->db->get('data_exam_student');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'student_username' 	=> $row->student_username,
				'exam_qa_id'	=> $row->exam_qa_id,
				'answer'		=> $row->answer,
				'status'		=> $row->status,
				'date_created'	=> $row->date_created,
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
}