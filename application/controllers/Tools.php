<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('ToolsModel');
		$this->load->model('TokenEngineModel');
	}

	public function check()
	{
		$v = $this->validateToken();
		
		
		if($v){
			
			$appName = $this->input->post('app_name');
		
			$detail = $this->ToolsModel->getDetail($appName);
		
		echo json_encode($detail);
		
		}
	}
	
	
	public function download(){
		
		$appName 	= $this->input->get('app_name');
		$file = null;
		
		if($appName == "teamviewer"){
				$file = "TeamViewer_Setup.exe";
		}
		
		$targetFile = 'tools/' . $file;
		
		force_download($targetFile,NULL);
		
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->ToolsModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	

}
