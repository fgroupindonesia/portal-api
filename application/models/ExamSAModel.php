<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamSAModel extends CI_Model {

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
	
	public function add($username, $exam_qa_id, $answer, $score_earned, $status, $fileupload){
		
		$stat = 'invalid';
		
			$data = array(
				'student_username'	=> $username,
				'exam_qa_id'		=> $exam_qa_id,
				'score_earned'	=> $score_earned,
				'status'	=> $status
			);
		
		if(isset($answer)){
			$data['answer']	= $answer;
		}
		
		
		if(isset($fileupload) || !empty($fileupload)){
			$data['fileupload']	= $fileupload;
		}
		
		$this->db->insert('data_exam_student', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $username, $exam_qa_id, $answer, $score_earned, $status, $fileupload){
		
		$endRes = $this->generateRespond('invalid');
	
		$data = array(
				'student_username'	=> $username,
				'exam_qa_id'		=> $exam_qa_id,
				'answer'	=> $answer,
				'score_earned'	=> $score_earned,
				'status'	=> $status
		);
	
		if(isset($fileupload)){
			$data['fileupload'] = $fileupload;
		}
		
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
				'id' 					=> $row->id,
				'student_username' 		=> $row->student_username,
				'exam_qa_id' 			=> $row->exam_qa_id,
				'answer'				=> $row->answer,
				'score_earned'			=> $row->score_earned,
				'status'				=> $row->status,
				'fileupload'			=> $row->fileupload,
				'date_created'			=> $row->date_created
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
				'id' 					=> $row->id,
				'student_username' 		=> $row->student_username,
				'exam_qa_id' 			=> $row->exam_qa_id,
				'answer'				=> $row->answer,
				'fileupload'				=> $row->fileupload,
				'score_earned'			=> $row->score_earned,
				'date_created'				=> $row->date_created,
				'status'				=> $row->status
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	

}