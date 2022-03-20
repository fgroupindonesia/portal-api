<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExamQAModel extends CI_Model {

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
	
	public function add($question, $jenis, $option_a, $option_b, $option_c, $option_d, $preview, $answer, $score_point, $exam_cat_id, $exam_subcat_id){
		
		// jenis is 
		// 1 for abcd
		// 2 for essay
		// 3 for ab only (true false)
		
		$stat = 'invalid';
		
			$data = array(
				'question'	=> $question,
				'jenis'		=> $jenis,
				'option_a'	=> $option_a,
				'option_b'	=> $option_b,
				'option_c'	=> $option_c,
				'option_d'	=> $option_d,
				'preview'	=> $preview,
				'answer'	=> $answer,
				'score_point'	=> $score_point,
				'exam_category_id'	=> $exam_cat_id,
				'exam_sub_category_id'	=> $exam_subcat_id
			);
		
		$this->db->insert('data_exam_qa', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $question, $jenis, $option_a, $option_b, $option_c, $option_d, $preview, $answer, $score_point, $exam_cat_id, $exam_subcat_id){
		
		$endRes = $this->generateRespond('invalid');
	
		
	
		$data = array(
				'question'	=> $question,
				'jenis'		=> $jenis,
				'option_a'	=> $option_a,
				'option_b'	=> $option_b,
				'option_c'	=> $option_c,
				'option_d'	=> $option_d,
				'answer'	=> $answer,
				'score_point'	=> $score_point,
				'exam_category_id'	=> $exam_cat_id,
				'exam_sub_category_id'	=> $exam_subcat_id
			);
		
		// we use preview field as picture file
		// if its not null
		if($preview != null) {
			$data['preview'] = $preview;
		}
		
		$this->db->where('id', $id);
		$this->db->update('data_exam_qa', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
	public function getAllBySchedule($schedID){
		
		$endResult = $this->generateRespond('invalid');
		
		// we need to obtain the exam-category id 
		// from schedule id passed earlier
		$multiParam = array(
			'schedule_id' => $schedID
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_exam_category_schedule');
		
		foreach ($query->result() as $row)
		{
			$examCatID = $row->exam_category_id;
			break;
		}
		
		if(isset($examCatID)){
			// continue obtaining data from data_examqa
			$multiParam = array(
				'exam_category_id' => $examCatID
			);
			
			$this->db->where($multiParam);		
		
			$query = $this->db->get('data_exam_qa');
			
			foreach ($query->result() as $row)
			{
				$endResult['status'] = 'valid';
			
				$data = array(
					'id' 			=> $row->id,
					'question' 		=> $row->question,
					'jenis' 		=> $row->jenis,
					'option_a'	=> $row->option_a,
					'option_b'	=> $row->option_b,
					'option_c'	=> $row->option_c,
					'option_d'	=> $row->option_d,
					'preview'	=> $row->preview,
					'answer'	=> $row->answer,
					'score_point'	=> $row->score_point,
					'exam_sub_category_id'	=> $row->exam_sub_category_id,
					'exam_category_id'	=> $row->exam_category_id
				);
				
				$endResult['multi_data'][] = $data;
			}
			
		}
		
		return $endResult;
		
	}
	
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		
		$query = $this->db->get('data_exam_qa');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'question' 		=> $row->question,
				'jenis' 		=> $row->jenis,
				'option_a'	=> $row->option_a,
				'option_b'	=> $row->option_b,
				'option_c'	=> $row->option_c,
				'option_d'	=> $row->option_d,
				'preview'	=> $row->preview,
				'answer'	=> $row->answer,
				'score_point'	=> $row->score_point,
				'exam_sub_category_id'	=> $row->exam_sub_category_id,
				'exam_category_id'	=> $row->exam_category_id
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
		$this->db->delete('data_exam_qa');
		
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
	
		$query = $this->db->get('data_exam_qa');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'question' 		=> $row->question,
				'jenis'			=> $row->jenis,
				'option_a'			=> $row->option_a,
				'option_b'			=> $row->option_b,
				'option_c'			=> $row->option_c,
				'option_d'			=> $row->option_d,
				'preview'			=> $row->preview,
				'answer'			=> $row->answer,
				'score_point'		=> $row->score_point,
				'exam_sub_category_id'	=> $row->exam_sub_category_id,
				'exam_category_id'	=> $row->exam_category_id
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	
	public function deletePreview($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$newData = array(
			'preview' => 'exam-prev-default.png'
		);
		
		$filename = null;
		
		
		$this->db->where($whereComp);		
		$query = $this->db->get('data_exam_qa');
		
		foreach ($query->result() as $row)
		{
			
			$filename =	$row->preview;
		
		}
		// delete the real file on server side
		
		unlink('images/exam-preview/'.$filename);
		
		$this->db->where($whereComp);
		$this->db->update('data_exam_qa', $newData);
		
		if($this->db->affected_rows() > 0){
				$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}	

}