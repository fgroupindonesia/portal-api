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
		
		$multiParam = array(
			'username' => $usernameIn
		);
		
		$this->db->where($multiParam);		
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
	
	
	
}