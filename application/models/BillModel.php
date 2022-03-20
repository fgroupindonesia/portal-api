<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BillModel extends CI_Model {

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
	
	public function getAll($usernameIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'username' => $usernameIn
		);
		
		$this->db->where($multiParam);
	
		$this->db->order_by('id', 'DESC');
		
		$query = $this->db->get('data_bill');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'username'		=> $row->username,
				'description'	=> $row->description,
				'amount'		=> $row->amount,
				'status'		=> $row->status,
				'date_created'	=> $row->date_created
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function add($username, $amount, $desc, $status){
		
		$stat = 'invalid';
		
			$data = array(
				'username' 			=> $username,
				'description' 		=> $desc,
				'amount' 			=> $amount,
				'status'			=> $status
			);
		
		
		$this->db->insert('data_bill', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}

	public function edit($id, $username, $amount, $desc, $status){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
			'username' 		=> $username,
			'amount' 		=> $amount,
			'description' 	=> $desc,
			'status' 		=> $status
		);
		
		
		$this->db->where('id', $id);
		$this->db->update('data_bill', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
}