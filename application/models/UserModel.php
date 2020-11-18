<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends CI_Model {

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
	
	public function add($usernameIn, $passIn, $emailIn, $addressIn, $mobileIn, $propicIn){
		
		$stat = 'invalid';
		
		$data = array(
			'username' 	=> $usernameIn,
			'pass' 		=> $passIn,
			'email' 	=> $emailIn,
			'address' 	=> $addressIn,
			'mobile' 	=> $mobileIn,
			'propic'	=> $propicIn
		);
		
		$foundInDB = $this->checkDuplicates($emailIn, $usernameIn);
		
		if($foundInDB != true){
			$this->db->insert('data_user', $data);
			$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}
	
	public function checkDuplicates($emailIn, $usernameIn){
		
		$duplicate = false;
		
		$checker = array(
			'email' => $emailIn
		);
		
		
		$query = $this->db->get_where('data_user', $checker);
		
		foreach ($query->result() as $row)
		{
			$duplicate = true;
			break;
		}
		
		// if the email isnot duplicate
		// we checked once more on username
		if(!$duplicate){
		
		$checker = array(
			'username' => $usernameIn
		);
		
		$query = $this->db->get_where('data_user', $checker);
		
		foreach ($query->result() as $row)
		{
			$duplicate = true;
		}
		
		}
		
		return $duplicate;
	}
	
	public function isValidTokenExist($usernameIn){
		
		// count the time is not more than expired_date
		
		$checker = array(
			'TIMEDIFF(expired_date, NOW()) >=' =>  1,
			'username' => $usernameIn
		);
		
		$val = false;
		
		$data = array();
		
		$query = $this->db->get_where('data_token', $checker);
		
		// getting the existing token if any
		
		foreach ($query->result() as $row)
		{
			//$val = $row->token;
			$data = array(
				'username' => $usernameIn,
				'token' => $row->token,
				'expired_date' => $row->expired_date
			);
			
			$val = true;
		}
		
		
		if($val === false){
			return $val;
		}
		
		return $data;
		
	}
	
	public function verify($usernameIn, $passIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'username' => $usernameIn,
			'pass' => $passIn
		);
		
		$this->db->where($multiParam);		
		$query = $this->db->get('data_user');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$hisToken = $this->isValidTokenExist($usernameIn);
			
			// when the existing token is not exist,
			// we shall generate a new one
			if(!is_array($hisToken)){
				$hisToken = $this->generateNewToken($usernameIn);
			}
			
			$endResult['multi_data'] = $hisToken;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		$query = $this->db->get('data_user');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'username' 		=> $row->username,
				'pass' 			=> $row->pass,
				'email' 		=> $row->email,
				'address' 		=> $row->address,
				'propic' 		=> $row->propic,
				'mobile' 		=> $row->mobile,
				'date_created' 	=> $row->date_created
			);
			
			// grab all data except for admin
			if($row->username != 'admin')
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function delete($usernameIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$whereComp = array(
			'username' => $usernameIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_user');
		
		if($this->db->affected_rows() > 0){
				$endResult = $this->generateRespond('valid');
		}
		
		return $endResult;
	}
	
	public function getProfile($usernameIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$multiParam = array(
			'username' => $usernameIn
		);
		
		$this->db->where($multiParam);		
	
		$query = $this->db->get('data_user');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'username' 		=> $row->username,
				'pass' 			=> $row->pass,
				'email' 		=> $row->email,
				'address' 		=> $row->address,
				'propic' 		=> $row->propic,
				'mobile' 		=> $row->mobile,
				'date_created' 	=> $row->date_created
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function generateNewToken($usernameIn){
		
		// get 75 character randomly generated
		$token =  substr(sha1(time()), 0, 75); 
		$exp_date = date('Y-m-d H:m:s', strtotime('+1 day'));
		
		$data = array(
			'username' => $usernameIn,
			'token' => $token,
			'expired_date' => $exp_date
		);
		
		$this->db->insert('data_token', $data);
		return $data;
		
	}
}