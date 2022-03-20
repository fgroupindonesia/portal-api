<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportBugsModel extends CI_Model {

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
	
	public function getAll(){
		
		$endResult = $this->generateRespond('invalid');
		
		$query = $this->db->get('data_report_bugs');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'app_name'		=> $row->app_name,
				'username'		=> $row->username,
				'ip_address' 	=> $row->ip_address,
				'title'			=> $row->title,
				'description'	=> $row->description,
				'screenshot'	=> $row->screenshot,
				'date_created'	=> $row->date_created
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function add($app_name, $username, $ip_address, $title, $desc, $screenshot){
		
		$stat = 'invalid';
		
		$dateCreated = date('Y-m-d H:i:s');
		
			$data = array(
				'app_name' 			=> $app_name,
				'username' 			=> $username,
				'ip_address' 		=> $ip_address,
				'title' 			=> $title,
				'description' 		=> $desc
			);
		
		// we use screenshot field as picture file
		// if its not null
		if($screenshot != null) {
			$data['screenshot'] = $screenshot;
		}
		
		$this->db->insert('data_report_bugs', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}
		
	public function delete($idIn){
		
		$stat = 'invalid';
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_report_bugs');
		
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
	
		$query = $this->db->get('data_report_bugs');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'app_name'		=> $row->app_name,
				'username'		=> $row->username,
				'ip_address' 	=> $row->ip_address,
				'title'			=> $row->title,
				'description'	=> $row->description,
				'screenshot'	=> $row->screenshot,
				'date_created'	=> $row->date_created
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}

	public function edit($id, $app_name, $username, $ip_address, $title, $desc, $screenshot){
		
		$endRes = $this->generateRespond('invalid');
		
			$data = array(
				'app_name' 			=> $app_name,
				'username' 			=> $username,
				'ip_address' 		=> $ip_address,
				'title' 			=> $title,
				'description' 		=> $desc
			);
		
		// we use screenshot field as picture file
		// if its not null
		if($screenshot != null) {
			$data['screenshot'] = $screenshot;
		}
		
		$this->db->where('id', $id);
		$this->db->update('data_report_bugs', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
}