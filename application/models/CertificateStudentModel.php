<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CertificateStudentModel extends CI_Model {

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
	
	public function add($username, $exam_category_id, $exam_category_title, $status, $filename, $ex_date, $url){
		
		$stat = 'invalid';
		
			$data = array(
				'student_username'	=> $username,
				'exam_category_id'	=> $exam_category_id,
				'exam_category_title' => $exam_category_title,
				'status' => $status,
				'url' => $url,
				'filename' => $filename
			);
		
		if(!isset($ex_date)){
			$ex_date = date('Y-m-d');	
		}
		
		$data['exam_date_created'] = $ex_date;
		
		$this->db->insert('data_certificate_student', $data);
		
		$idExamNew = $this->db->insert_id();
	
	
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $username, $exam_category_id, $exam_category_title, $status, $filename, $ex_date, $url){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
				'student_username'	=> $username,
				'exam_category_id'	=> $exam_category_id,
				'exam_category_title' => $exam_category_title,
				'status' => $status,
				'url' => $url,
				'filename' => $filename
			);
		
		if(!isset($ex_date)){
			$ex_date = date('Y-m-d');	
		}
		
		$data['exam_date_created'] = $ex_date;
		
		$this->db->where('id', $id);
		$this->db->update('data_certificate_student', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		
		return $endRes;
		
	}
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		$query = $this->db->get('data_certificate_student');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 					=> $row->id,
				'student_username' 		=> $row->student_username,
				'exam_category_id' 		=> $row->exam_category_id,
				'exam_category_title' 	=> $row->exam_category_title,
				'status'				=> $row->status,
				'filename'				=> $row->filename,
				'url' 					=> $row->url,
				'exam_date_created'		=> $row->exam_date_created
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
		$this->db->delete('data_certificate_student');
		
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
	
		$query = $this->db->get('data_certificate_student');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id'	=> $row->id,
				'student_username'	=> $row->student_username,
				'exam_category_id'	=> $row->exam_category_id,
				'exam_category_title' => $row->exam_category_title,
				'status' => $row->status,
				'filename' => $row->filename,
				'url' => $row->url,
				'exam_date_created' => $row->exam_date_created,
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function deletePicture($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);		
		$query = $this->db->get('data_certificate_student');
		
		foreach ($query->result() as $row)
		{
			
			$filename =	$row->filename;
		
		}
		// delete the real file on server side
		
		unlink('images/certificate/'.$filename);
		
		$newData = array(
			'filename' => 'cert-default.png'
		);
		
		$this->db->where($whereComp);
		$this->db->update('data_certificate_student', $newData);
		
		if($this->db->affected_rows() > 0){
				$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}	
	
}