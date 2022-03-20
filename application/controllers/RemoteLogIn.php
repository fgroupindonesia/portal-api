<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RemoteLogin extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('RemoteLoginModel');
		$this->load->model('TokenEngineModel');
		$this->load->model('UserModel');
	}
	
	public function getMyIP(){
		$ip = file_get_contents('https://api.ipify.org');
		return $ip;
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->RemoteLoginModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	
	public function activate(){
		
		// no token available here
	
		// the ip address will be the client ip address not the mobile ip address
		// please uncomment below code when LIVE in real SERVER
		//$anIP = $this->input->ip_address();
		// or this one
		$anIP = $this->getMyIP();
		
		
		// below code is for SIMULATION
		//$anIP = $this->input->post('ip-address');
		
		//$anIP = "103.247.197.4";
		
		//var_dump($anIP);
		
		// get the machine id unique
		$macUnique = $this->input->post('machine_unique');
	
	
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, "http://ip-api.com/json/" . $anIP);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$returnData = curl_exec($curl);

		//var_dump($returnData);

		curl_close($curl);

		$jsonObject = json_decode($returnData);
		
		// see the output here
		//echo var_dump($returnData);
		
		// see the ip output here
		//echo ($jsonObject->query);

		// but finally only here we sent back his generated public IP
		// for generating QRCode along with validated JSON
		
		
		$dataBack = $this->RemoteLoginModel->generateRespond('invalid');
		
		if(!empty($anIP)){
		
		$dataBack = $this->RemoteLoginModel->generateRespond('valid');
		
		$dataBack['multi_data'] = array(
			'machine_unique' => $macUnique,
			'ip_address' => $jsonObject->query
		);

		// data needed to be posted to db
		$country = $jsonObject->country;
		$regionName = $jsonObject->regionName;
		$city = $jsonObject->city;
		$isp = $jsonObject->isp;
		$ispAs = $jsonObject->as;
		$ipAddress = $jsonObject->query;
		
		// post to the db with status 'ready'
		// for the machine that has not yet been recorded in DB
		$exist = $this->RemoteLoginModel->checkDuplicates($macUnique);
		
		if(!$exist){
		
		$this->RemoteLoginModel->add($macUnique, $country, $regionName, $city, $isp, $ispAs, $ipAddress);
		
		}
		
		}
		
		echo json_encode($dataBack);
		
	
	}
	
	// checking by client
	
	public function check(){
		
		$macUnique = $this->input->post('machine_unique');
		
		// receive the status back
		// whether this machine state is already have for today?
		
		// machine has 3 status:
		// 'ready' / 'opened' / 'out'
		
		$result = $this->RemoteLoginModel->checkStatus($macUnique);
		
		// if this machine is opened thus we build another data for multi_data entry:
		
		if($result['multi_data']['status'] == 'opened'){
			
			$username = $result['multi_data']['username'];
			//$username = 'admin';
			//echo $username;
			
			$newOrder = array();
			$newOrder[] = $result['multi_data'];
			
			$tokenData = $this->UserModel->isValidTokenExist($username);
			
				// we add the entry if it is not FALSE
				// instead it is array entry
				
				if(is_array($tokenData)){
					$newOrder[] = $tokenData;
				}
			
			// return back to multi_data replacement
			$result['multi_data'] = $newOrder;
		}
		
		
		echo json_encode($result);
		
	}
	
	// calling by mobile
	
	public function show(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$username = $this->input->post('username');
		
		// obtaining all data for this user only
		// but dont show any data with 'out' status
		$result = $this->RemoteLoginModel->getAll($username, 'out');
		
		echo json_encode($result);
		
		}
		
	}
	
	// disconnect by mobile
	
	public function disconnect(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$mac_unique = $this->input->post('machine_unique');
		$username = $this->input->post('username');
		
		// updating the status to be 'out' for this machine
		$result = $this->RemoteLoginModel->updateStatus($mac_unique, $username, 'out');
		
		echo json_encode($result);
		
		}
		
	}
	
	// verifying by mobile
	
	public function verify(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$mac_unique = $this->input->post('machine_unique');
		$username = $this->input->post('username');
		
		// updating the status to be 'opened' for this machine
		$result = $this->RemoteLoginModel->updateStatus($mac_unique, $username, 'opened');
		
		echo json_encode($result);
		
		}
		
	}
}