<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamCategoryModel extends CI_Model {

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
	
	public function add($title, $code, $sbase, $dataSub){
		
		$stat = 'invalid';
		
			$data = array(
				'title'	=> $title,
				'code'	=> $code,
				'score_base' => $sbase
			);
		
		$this->db->insert('data_exam_category', $data);
		
		$idExamNew = $this->db->insert_id();
	
		if($dataSub != null){
			// we execute new entry for each array data passed
			foreach($dataSub as $dataSatuan){
				
			$judulSub = $dataSatuan['title'];
			
			$arrayData = array(
				'exam_category_id' 	=> $idExamNew,
				'title' 			=> $judulSub
			);
		
			$this->db->insert('data_exam_sub_category', $arrayData);
			
			}
		
		}
	
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $title, $code, $score_base, $dataSub){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
			'title' 			=> $title,
			'code' 				=> $code,
			'score_base' 		=> $score_base
		);
		
		$this->db->where('id', $id);
		$this->db->update('data_exam_category', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		$idExamCurrent = $id;
		
		if($dataSub != null){
			
			// we execute new entry for each array data passed
			foreach($dataSub as $dataSatuan){
				
			$judulSub 	= $dataSatuan['title'];
			$idNa 		= $dataSatuan['id'];
			
			$arrayData = array(
				'exam_category_id' 	=> $idExamCurrent,
				'title' 			=> $judulSub
			);
			
			if($idNa == 0){
				
				// saving new data
			$this->db->insert('data_exam_sub_category', $arrayData);
			
			}else {
				
				// updating the existing data
			$this->db->where('id', $idNa);
			$this->db->update('data_exam_sub_category', $arrayData);
				
			}
			
			if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
			}
			
			}
			
		}
		
		return $endRes;
		
	}
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		$query = $this->db->get('data_exam_category');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'title' 		=> $row->title,
				'score_base' 	=> $row->score_base,
				'code' 			=> $row->code,
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
		$this->db->delete('data_exam_category');
		
		if($this->db->affected_rows() > 0){
				$endResult = $this->generateRespond('valid');
				
				// continue deleting from another table related
				// here we also delete the entry from
				// data_exam_sub_category table
				$whereComp = array(
					'exam_category_id' => $idIn
				);
		
				$this->db->where($whereComp);
				$this->db->delete('data_exam_sub_category');
				
		}
		
		return $endResult;
	}
	
	public function getDetail($idIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'id' => $idIn
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_exam_category');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'title' 		=> $row->title,
				'code'			=> $row->code,
				'score_base'			=> $row->score_base,
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