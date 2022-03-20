<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RemoteLoginModel extends CI_Model {

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
	
	public function getAll($usernameIn, $unwantedStatus){
		$found = false;
		
		$endResult = $this->generateRespond('invalid');
		$dataFound = array();
		
		$today = date('Y-m-d');
		
		$checker = array(
			"username" => $usernameIn,
			"status !=" => $unwantedStatus
		);
		
		// if the data matched with this user
		$query = $this->db->get_where('data_remote_login', $checker);
		
		foreach ($query->result() as $row)
		{
				$endResult['status'] = 'valid';
			
				$dataFound = array(
					'id' 				=> $row->id,
					'username' 			=> $row->username,
					'machine_unique' 	=> $row->machine_unique,
					'country'			=> $row->country,
					'region'			=> $row->region,
					'city'				=> $row->city,
					'isp'				=> $row->isp,
					'isp_as'			=> $row->isp_as,
					'ip_address'		=> $row->ip_address,
					'status'			=> $row->status,
					'date_created'		=> $row->date_created
				);
				
				$endResult['multi_data'][] = $dataFound;
			
			
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
	}
	
	public function checkStatus($macUnique){
		$found = false;
		
		$res = $this->generateRespond('invalid');
		$dataFound = array();
		
		$today = date('Y-m-d');
		
		$checker = array(
			"machine_unique" => $macUnique,
			"DATE(date_created)" => $today
		);
		
		// if the date is 'opened' within today
		$query = $this->db->get_where('data_remote_login', $checker);
		
		foreach ($query->result() as $row)
		{
				$found = true;
				
				$dataFound = array(
					'id' 				=> $row->id,
					'username' 			=> $row->username,
					'machine_unique' 	=> $row->machine_unique,
					'country'			=> $row->country,
					'region'			=> $row->region,
					'city'				=> $row->city,
					'isp'				=> $row->isp,
					'isp_as'			=> $row->isp_as,
					'ip_address'		=> $row->ip_address,
					'status'			=> $row->status,
					'date_created'		=> $row->date_created
				);
				
				break;
			
			
		}
		
		if($found){
			$res = $this->generateRespond('valid');
			$res['multi_data'] = $dataFound;
		}
		
		return $res;
	}
	
	public function checkDuplicates($macUnique){
		
		$duplicate = false;
		
		$today = date('Y-m-d');
		
		$checker = array(
			"machine_unique" => $macUnique
		);
		
		$query = $this->db->get_where('data_remote_login', $checker);
		
		foreach ($query->result() as $row)
		{
			$duplicate = true;
			break;
		}
		
		return $duplicate;
		
	}
	
	public function updateStatus($macUnique, $username, $newStat){
		
		$res = $this->generateRespond('invalid');
		
		$today = date('Y-m-d');
		
		// we use checking for today only for non-out (disconnect) stat
		if($newStat != 'out'){
		
		$checker = array(
			"machine_unique" => $macUnique,
			"DATE(date_created)" => $today
		);
		
		}else{
			
			// for disconecting purposes just use one parameter
			$checker = array(
			"machine_unique" => $macUnique
			);
			
		}
		
		$data = array(
			'status' 	=> $newStat,
			'username'	=> $username
		);
		
		$this->db->where($checker);
		$this->db->update('data_remote_login', $data);
		
		if($this->db->affected_rows() > 0){
				$res = $this->generateRespond('valid');
		}
		
		return $res;
	}
	
	public function add($macUnique, $country, $regionName, $city, $isp, $ispAs, $ipAddress){
		
		$stat = 'invalid';
		$statusEntry = 'ready';
		
		$data = array(
			'machine_unique' 	=> $macUnique,
			'country' 			=> $country,
			'region' 			=> $regionName,
			'city' 				=> $city,
			'isp'				=> $isp,
			'isp_as'			=> $ispAs,
			'ip_address'		=> $ipAddress,
			'status'			=> $statusEntry
		);
		
		$this->db->insert('data_remote_login', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}
	
}