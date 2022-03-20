<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentModel extends CI_Model {

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
		
		// we sort it out for non admin
		// but if it's for admin we show all
		if($usernameIn != 'admin')
		{
			$multiParam = array(
				'username' => $usernameIn
			);
			
			$this->db->where($multiParam);				
		}
		
		$query = $this->db->get('data_document');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 		=> $row->id,
				'title' 	=> $row->title,
				'description' 	=> $row->description,
				'filename'		=> $row->filename,
				'username'		=> $row->username,
				'url'			=> $row->url,
				'date_created'	=> $row->date_created
			);
			
			$endResult['multi_data'][] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}
	
	public function add($titleIn, $descriptionIn, $usernameIn, $urlIn, $filenameIn){
		
		$stat = 'invalid';
		
		$data = array(
			'title' 		=> $titleIn,
			'description' 	=> $descriptionIn,
			'filename' 		=> $filenameIn,
			'username' 		=> $usernameIn,
			'url' 			=> $urlIn
		);
		
		
		$this->db->insert('data_document', $data);
		$stat = 'valid';
		
		return $this->generateRespond($stat);
	}
	
	public function edit($idIn, $titleIn, $descriptionIn, $usernameIn, $urlIn, $filenameIn){
		
		$endRes = $this->generateRespond('invalid');
		
		$data = array(
			'title' 		=> $titleIn,
			'description' 	=> $descriptionIn,
			'username' 		=> $usernameIn,
			'url' 			=> $urlIn
			);
		
		
		// delete the picture previously
		// because now the picture is different
		if($filenameIn != null){
			$multiData = $this->getDetail($idIn);
			$filePrev = $multiData["multi_data"]["filename"];
			
			$targetFile = "./documents/" . $filePrev;
			//echo $targetFile;
			
			unlink($targetFile);
			
			// now updating the data
			$data['filename'] = $filenameIn;
			
		}
		
		
		$this->db->where('id', $idIn);
		$this->db->update('data_document', $data);
		
		if($this->db->affected_rows() > 0){
				$endRes = $this->generateRespond('valid');
		}
		
		return $endRes;
		
	}
	
	public function delete($idIn){
		
		$endResult = $this->generateRespond('invalid');
		
		$whereComp = array(
			'id' => $idIn
		);
		
		$this->db->where($whereComp);
		$this->db->delete('data_document');
		
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
	
		$query = $this->db->get('data_document');
		
		foreach ($query->result() as $row)
		{
			$endResult['status'] = 'valid';
			
			$data = array(
				'id' 			=> $row->id,
				'title' 		=> $row->title,
				'description'	=> $row->description,
				'filename' 		=> $row->filename,
				'username' 		=> $row->username,
				'url' 			=> $row->url,
				'date_created' 	=> $row->date_created
			);
		
			$endResult['multi_data'] = $data;
		}
		
		if($endResult['status'] == 'invalid'){
			unset($endResult['multi_data']);
		}
		
		return $endResult;
		
	}

}