<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TokenEngineModel extends CI_Model {

	function __construct() {
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');
	}
	
	public function isValidTokenExist($tokenIn){
		
		// count the time is not more than expired_date
		
		$checker = array(
			'TIMEDIFF(expired_date, NOW()) >=' =>  1,
			'token' => $tokenIn
		);
		
		$val = false;
		
		$data = array();
		
		$query = $this->db->get_where('data_token', $checker);
		
		// getting the existing token if any
		
		foreach ($query->result() as $row)
		{
			//$val = $row->token;
			$data = array(
				'username' => $row->username,
				'token' => $row->token,
				'expired_date' => $row->expired_date
			);
			
			$val = true;
		}
		
		
		// switch to true
		//return $data;
		return $val;
		
	}
	
	public function checkDuplicates($emailIn){
		
		$duplicate = false;
		
		$checker = array(
			'email' => $emailIn
		);
		
		
		$query = $this->db->get_where('data_user', $checker);
		
		foreach ($query->result() as $row)
		{
			$duplicate = true;
		}
		
		return $duplicate;
	}
	
}