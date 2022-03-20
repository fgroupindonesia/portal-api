<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentModel extends CI_Model {

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
	
	public function getLast($usernameIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'username' => $usernameIn
		);
		
		$this->db->where($multiParam);		
		$this->db->order_by('id','DESC');
		$this->db->limit(1);
		$query = $this->db->get('data_payment');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 		=> $row->id,
				'username' 	=> $row->username,
				'date_created' 	=> $row->date_created,
				'amount'		=> $row->amount,
				'method'		=> $row->method,
				'screenshot'	=> $row->screenshot
			);
			
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function getAll($usernameIn){
		
		$endResult = $this->generateRespond('invalid');
		
		if($usernameIn != 'admin'){
			$multiParam = array(
				'username' => $usernameIn
			);
			
			$this->db->where($multiParam);		
		
		}
		$query = $this->db->get('data_payment');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'username'		=> $row->username,
				'date_created'	=> $row->date_created,
				'amount' 		=> $row->amount,
				'method'		=> $row->method,
				'screenshot'	=> $row->screenshot
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function add($username, $amount, $method, $filename){
		
		
		$dateCreated = date('Y-m-d H:i:s');
		
		
			$data = array(
				'username' 			=> $username,
				'amount' 			=> $amount,
				'method' 			=> $method
			);
		
		// we use screenshot field as picture file
		// if its not null
		if($filename != null) {
			$data['screenshot'] = $filename;
		}
		
		$this->db->insert('data_payment', $data);
		$endRes = $this->generateRespond('valid');
		
		return $endRes;
	}
		
	public function delete($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_payment');
		
		if($this->db->affected_rows() > 0){
				$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}

	public function deleteScreenshot($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$newData = array(
			'screenshot' => 'not available'
		);
		
		$this->db->where($whereComp);
		$this->db->update('data_payment', $newData);
		
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
	
		$query = $this->db->get('data_payment');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 				=> $row->id,
				'username' 			=> $row->username,
				'amount'			=> $row->amount,
				'method' 			=> $row->method,
				'screenshot' 		=> $row->screenshot,
				'date_created' 		=> $row->date_created
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}

	public function edit($id, $username, $amount, $method, $filename){
		
		$endRes = $this->generateRespond('invalid');
		
			$data = array(
			'username' 		=> $username,
			'amount' 		=> $amount,
			'method' 		=> $method
			);
		
		// we use screenshot field as picture file
		// if its not null
		if($filename != null) {
			$data['screenshot'] = $filename;
		}
		
		$this->db->where('id', $id);
		$this->db->update('data_payment', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
}