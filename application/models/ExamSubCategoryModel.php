<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamSubCategoryModel extends CI_Model {

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
	
	public function add($title, $exam_category_id){
		
		$stat = 'invalid';
		
			$data = array(
				'title'	=> $title,
				'exam_category_id'	=> $exam_category_id
			);
		
		$this->db->insert('data_exam_sub_category', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $title, $exam_category_id){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
			'title' 	=> $title,
			'exam_category_id' 		=> $exam_category_id
		);
		
		$this->db->where('id', $id);
		$this->db->update('data_exam_sub_category', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
	public function getAll($idExam){
		
		$endResult = $this->generateRespond('invalid');
		
		if($idExam != null) {
			// if the id parent is exist
			// we should get the specific range only
		$multiParam = array(
			'exam_category_id' => $idExam
		);
		
		$this->db->where($multiParam);		
	
		}
			// if the id parent is not passed
			// thus we got all items
			$query = $this->db->get('data_exam_sub_category');
		
		
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'title' 		=> $row->title,
				'exam_category_id' 	=> $row->exam_category_id
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
		$this->db->delete('data_exam_sub_category');
		
		if($this->db->affected_rows() >= 0){
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
	
		$query = $this->db->get('data_exam_sub_category');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'title' 		=> $row->title,
				'exam_category_id'	=> $row->exam_category_id
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
}