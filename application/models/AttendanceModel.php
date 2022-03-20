<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AttendanceModel extends CI_Model {

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
	
	public function add($username, $status, $signature, $classReg){
		
		$stat = 'invalid';
		
		$dateCreated = date('Y-m-d H:i:s');
		
		
			$data = array(
				'username' 			=> $username,
				'status' 			=> $status,
				'class_registered' 	=> $classReg,
				'date_created' 		=> $dateCreated
			);
		
		// we use signature field as picture file
		// if its not null
		if($signature != null) {
			$data['signature'] = $signature;
		}
		
		$this->db->insert('data_attendance', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}
	
	public function delete($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_attendance');
		
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
	
		$query = $this->db->get('data_attendance');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'username' 			=> $row->username,
				'status'			=> $row->status,
				'class_registered' 	=> $row->class_registered,
				'signature' 		=> $row->signature,
				'date_created' 		=> $row->date_created,
				'date_modified'		=> $row->date_modified
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function edit($id, $username, $status, $signature, $classReg){
		
		$endRes = $this->generateRespond('invalid');
		
			$data = array(
			'username' 		=> $username,
			'status' 		=> $status,
			'class_registered' 	=> $classReg
			);
		
		// we use signature field as picture file
		// if its not null
		if($signature != null) {
			$data['signature'] = $signature;
		}
		
		$this->db->where('id', $id);
		$this->db->update('data_attendance', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
			
	public function getAll($usernameIn){
		
		$endResult = $this->generateRespond('invalid');
		
		// the data will be filtered based upon username
		// but for admin he will get everything
		if($usernameIn != 'admin'){
		
		$multiParam = array(
			'username' => $usernameIn
		);
		
		$this->db->where($multiParam);		
		
		}

		$this->db->order_by('id', 'desc');		
		$query = $this->db->get('data_attendance');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'class_registered' 	=> $row->class_registered,
				'status' 			=> $row->status,
				'signature'			=> $row->signature,
				'username'			=> $row->username,
				'date_modified'		=> $row->date_modified,
				'date_created'		=> $row->date_created
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function deleteSignature($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$newData = array(
			'signature' => 'not available'
		);
		
		$this->db->where($whereComp);
		$this->db->update('data_attendance', $newData);
		
		if($this->db->affected_rows() > 0){
				$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}
}