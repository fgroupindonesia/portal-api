<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('AttendanceModel');
		$this->load->model('TokenEngineModel');
		$this->load->model('ScheduleModel');
		
		date_default_timezone_set('Asia/Jakarta');
		
	}

	public function uploadPicture($filename, $key){
		
			// if there file given'
			
			$filename = time() . '_' . str_replace(str_split(' ()\\/,:*?"<>|'), '', $_FILES[$key]['name']);
	
			//echo $_FILES[$key]['name'];
	
		$config['upload_path'] = 'images/attendance/'; 
		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg|pdf|doc|docx|xls|xlsx|txt|zip|rar';
		$config['file_name'] = $filename;
		
		$this->load->library('upload', $config);
		$uploadRes = $this->upload->do_upload($key);
		
		return $filename;
		
	}

	
	// this is called by Admin from Portal / automate task

	public function generateEmailNotification(){
		
	$dayNow = strtolower(date('l')); // englishbased day name	
	$tanggal = date('d-M-Y');
	$hourNow = date('h:00');
	
	$tanggal .= " jam " . $hourNow;
	$dataObtained =	$this->ScheduleModel->getAllByDayWithEmail($dayNow);
		
		//echo $dayNow;
		//echo var_dump($dataObtained);
		//echo "<br> <br>";
		$x = 0;
		$totalData = count($dataObtained['multi_data']);
		
		// data schedule tiap orang ada disini
		// dalam array $dataObtained multi_data
		
		for($x=0; $x<$totalData; $x++){
	
		$namaOrang = $dataObtained['multi_data'][$x]['username'];
		$namaKelas = $dataObtained['multi_data'][$x]['class_registered'];
		$namaKelas = urlencode($namaKelas);
		
		
		$email = $dataObtained['multi_data'][$x]['email'];
		
		
		// read the view template
		// replace several data :
		// %NAMA_KELAS%
		// %TANGGAL%
		// %NAMA_PESERTA%
		// %URL_HADIR%
		// %URL_IDZIN%
		// %URL_SAKIT%
	
	// generate token for 1 hour only
	$tk = $this->TokenEngineModel->generateEmailUsage($namaOrang);
		
		// we generate the links
	$emailHadir = $this->generateEmailAttendanceLink('hadir', $namaOrang, $namaKelas, $tk);
	$emailIdzin = $this->generateEmailAttendanceLink('idzin', $namaOrang, $namaKelas, $tk);
	$emailSakit = $this->generateEmailAttendanceLink('sakit', $namaOrang, $namaKelas, $tk);
		
		$dataIn = array(
		'URL_SAKIT' => $emailSakit,
		'URL_IDZIN' => $emailIdzin,
		'URL_HADIR' => $emailHadir,
		'NAMA_PESERTA' => $namaOrang,
		'NAMA_KELAS' => $namaKelas,
		'TANGGAL' => $tanggal
		);
		
		
		$dataTemplate =  $this->load->view('email_attandance_template', $dataIn, true);
			
		$this->sendingEmail($email, "Pake StyleInLine Code Coba Lagi Absen Digital!!! Kelas " . $namaKelas, $dataTemplate);
		
		//echo $dataTemplate;
		}
		echo 'Bisa totalnya ' . $totalData . "<br>";
		//echo 'terkirim ' . $totalData;
	}
	
	public function sendingEmail($em, $title, $htmlCode){
		
		$url = 'https://fgroupindonesia.com/email/sending-schedule-notification.php';
		$data = array(
		'email' => $em,
		'title' => urldecode($title),
		'template' => $htmlCode
		);
		
		$cURLConnection = curl_init($url);
		curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
		curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

		$apiResponse = curl_exec($cURLConnection);
		curl_close($cURLConnection);
		$apiResponse;
		
	}
	 
	public function generateEmailAttendanceLink($status, $username,
	$classReg, $tokenKey){
		$url_parts = parse_url(current_url());
		$validCurrentDomain = $url_parts['scheme'] . '://' . str_replace('www.', '', $url_parts['host']);
		// above part is the REAL LIVE SERVER LINK GENERATED
		// this is short token for email clickable attendance
		
		$realLink = $validCurrentDomain . "/attendance/submit?";
		// un comment ABOVE CODE for real LIVE
		
		// below 192 IP address is for local testing ability in the same WIFI 
		// network for clickable testing
		//$realLink = "http://192.168.0.8/attendance/submit?";
		
		$realLink .= "&status=" . $status;
		$realLink .= "&username=". $username;
		$realLink .= "&class_registered=" . $classReg;
		$realLink .= "&webemail=1"; // adding a parameter of web email
		$realLink .= "&token=" . $tokenKey;
		
		return $realLink;
	}

	// clicked by user through email
	public function addByEmail(){
		
		// clicked by email inbox (user)
		// i.e call:
		// http://portal.fgroupindonesia.com/attendance/submit?webemail=1
		// &token=abc
		// &status=abc
		// &username=asd
		// &class_registered=abc
		
		// this has email generated token
		// everyone can access this one
		// with that email token for 1 hour expiracy
		
		$v = $this->validateEmailToken();
		
		
		if($v){
		
		$status			= $this->input->get('status');
		$username 		= $this->input->get('username');
		$classReg 		= $this->input->get('class_registered');
		
		$filename		= 'email_web_signature.png';
		
		$endRespond = $this->AttendanceModel->add($username, $status, $filename, $classReg);
		
		echo json_encode($endRespond);
			
			
			// show the animated GIF attendace updated!
			$this->showAnimatedRobot('Attendance Digital Success!', true);
		} else {
			// show the animated GIF token expired
			$this->showAnimatedRobot('Attendance Digital Failed!', false);
		}
	}
	
	private function showAnimatedRobot($stat, $show){

			// default
			$robotPath = base_url() . "/images/robot_sleep.gif";
		
			if($show){
				$robotPath = base_url() . "/images/robot_ok.gif";
			}
		
			$finalData = array(
			'status' => $stat,
			'robot_gif' => $robotPath
			);
			
			// show the animated on browser for simplicity
			$dataTemplate =  $this->load->view('attendance_digital_status', $finalData, true);
			
			echo $dataTemplate;
		
	}

	// this is for ADMIN
	public function add(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$status			= $this->input->post('status');
		$username 		= $this->input->post('username');
		$classReg 		= $this->input->post('class_registered');
		
		$filename		= null;
		
		$key 			= 'signature';
		
		if(isset($_FILES[$key])) {
			
			$filename = $this->uploadPicture($filename, $key);
		
		}
	
		
		$endRespond = $this->AttendanceModel->add($username, $status, $filename, $classReg);
		
		echo json_encode($endRespond);
		
		// calling the email notification into admin
		// to ensure that this person is Real Attending / not Attending the class
		
		
		}
	}
	
	// this is for ADMIN
	public function delete(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->AttendanceModel->delete($idIn);
		echo json_encode($endRespond);
		
		}
	}
	
	// this is for ADMIN
	public function detail(){
		
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 	= $this->input->post('id');
		
		$dataUsed = $this->AttendanceModel->getDetail($idIn);
		
		echo json_encode($dataUsed);
		
		}
	}
	
	// this is for ADMIN
	public function update(){
		$v = $this->validateToken();
		
		if($v){
			
		$id 			= $this->input->post('id');
		$status			= $this->input->post('status');
		$username 		= $this->input->post('username');
		$classReg 		= $this->input->post('class_registered');
		
		$filename		= null;
		
		$key 			= 'signature';
		
		if(isset($_FILES[$key])) {
			$filename = $this->uploadPicture($filename, $key);
		
		}
		
		$endRespond = $this->AttendanceModel->edit($id, $username, $status, $filename, $classReg);
		
		echo json_encode($endRespond);
		
		}
		
	}
	
	
	
	private function validateEmailToken(){
		$key = $this->input->get('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->AttendanceModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}
	
	private function validateToken(){
		$key = $this->input->post('token');
		$valid = $this->TokenEngineModel->isValidTokenExist($key);
		
		if(!$valid){
			$r = $this->AttendanceModel->generateRespond('invalid');
			echo json_encode($r);
		}
		
		// either true or false
		
		return $valid;
	}

	// attendance/all
	public function all()
	{
		
		$v = $this->validateToken();
		
		if($v){
		
		$username = $this->input->post('username');
		
		$data = $this->AttendanceModel->getAll($username);
		echo json_encode($data);
		
		}
	}

	// attendance/signature
	public function signature(){
		// we dont use token for accessing picture
		// because it's for public usage
		
		//$file 	= $this->input->post('signature');
		//$file = 'logo.png';
		$file 	= $this->input->get('signature');
		
		$targetFile = 'images/attendance/' . $file;
		
		force_download($targetFile,NULL);
		
	}	

	public function deleteSignature(){
		$v = $this->validateToken();
		
		if($v){
		
		$idIn 			= $this->input->post('id');
		$endRespond 	= $this->AttendanceModel->deleteSignature($idIn);
		echo json_encode($endRespond);
		
		}
	}

}
