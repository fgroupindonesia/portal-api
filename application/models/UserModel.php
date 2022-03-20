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
			'propic'	=> $propicIn,
			'warning_status'	=> 0
		);
		
		$foundInDB = $this->checkDuplicates($emailIn, $usernameIn);
		
		if($foundInDB != true){
			$this->db->insert('data_user', $data);
			$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
	}
	
	public function edit($id, $usernameIn, $passIn, $emailIn, $addressIn, $mobileIn, $propicIn, $tmv_idIn, $tmv_passIn, $warn){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
			'username' 	=> $usernameIn,
			'pass' 		=> $passIn,
			'email' 	=> $emailIn,
			'address' 	=> $addressIn,
			'mobile' 	=> $mobileIn,
			'warning_status'	=> $warn
		);
		
		// we use propic field as picture file
		// if its not null
		if($propicIn != null) {
			$data['propic'] = $propicIn;
		}
		
		if($tmv_idIn != null){
			$data['tmv_id'] = $tmv_idIn;
		}
		
		if($tmv_passIn != null){
			$data['tmv_pass'] = $tmv_passIn;
		}
		
		$this->db->where('id', $id);
		$this->db->update('data_user', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
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
			
			$warnStatus = $row->warning_status;
			
			// when the existing token is not exist,
			// we shall generate a new one
			if(!is_array($hisToken)){
				$hisToken = $this->generateNewToken($usernameIn);
				
			}
			
				// we also embed this new keytag
				$hisToken['warning_status'] = $warnStatus;
			
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
				'access_level' 		=> $row->access_level,
				'date_created' 	=> $row->date_created,
				'warning_status' => $row->warning_status
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
	
	public function deletePicture($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);		
		$query = $this->db->get('data_user');
		
		foreach ($query->result() as $row)
		{
			
			$filename =	$row->propic;
		
		}
		// delete the real file on server side
		
		unlink('images/propic/'.$filename);
		
		$newData = array(
			'propic' => 'default.png'
		);
		
		$this->db->where($whereComp);
		$this->db->update('data_user', $newData);
		
		if($this->db->affected_rows() > 0){
				$stat = 'valid';
		}
		
		return $this->generateRespond($stat);
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
				'tmv_id' 		=> $row->tmv_id,
				'tmv_pass' 		=> $row->tmv_pass,
				'date_created' 	=> $row->date_created,
				'warning_status' => $row->warning_status
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