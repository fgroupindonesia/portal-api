<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	function __construct() {
		parent::__construct();
		
	}
	
	public function blank(){
		
		echo "";
		
	}
	
	public function cobaReadGede(){
		
		$lokasi = APPPATH . 'test40Rebu.json';
		
		$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
		$data = file_get_contents($lokasi,false,$context);
		
		$dataBaru =  preg_replace('/\s+/', '', $data);
		$dataAkhir = json_decode($dataBaru);
		echo json_encode( $dataAkhir);
		
		
	}
	
	public function info(){
		phpinfo();
		
	}
	
	public function testIP(){
		$ip = $this->input->ip_address();
		echo $ip;
	}
	
	public function toCSV(){
		
		$dataProduct = array(
			'productA' => 3000,
			'productB' => 9000,
			'productC' => 12000,
		);
		
		header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"test".".csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $handle = fopen('php://output', 'w');
		
		 foreach ($dataProduct as $key=>$val) {
            $narray=array($key, $val);
            fputcsv($handle, $narray);
        }
		
	}
	
	public function testing(){
		
		$v  = $this->input->post('aku');
		
		if($v != null){
			echo "ahahaha " . $v;
		}else {
			echo "none";
		}
		
		//$this->load->model('UserModel');
		//echo var_dump($this->UserModel->isValidTokenExist('admin'));
		
	}
	
	public function testData(){
		
		// count the time is not more than expired_date
		
		$checker = array(
			'TIMEDIFF(expired_date, NOW()) >=' =>  1,
			'username' => 'admin'
		);
		
		$val = false;
		
		$query = $this->db->get_where('data_token', $checker);
		
		foreach ($query->result() as $row)
		{
			$val = $row;
		}
		
		echo json_encode($val);
		
	}
	
	public function a(){
		echo "A";
	}
	
	public function upload(){
		
	$uploadWork = false;
	$key = 'image_file';
	$filename = 'default.png';
		
		if(isset($_FILES[$key])) {
			
			if(!empty($_FILES[$key]['name'])){
					$uploadWork = true;
			}
		}

	
	// upload started
	if($uploadWork){
		
	$new_image_name = time() . str_replace(str_split(' ()\\/,:*?"<>|'), '', 
    $_FILES[$key]['name']);
	
	$filename = $new_image_name;
	
    $config['upload_path'] = 'images/'; 
    $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
    $config['file_name'] = $new_image_name;
	
	$this->load->library('upload', $config);
	$uploadRes = $this->upload->do_upload('image_file');
    
	// upload ended
	}
	
	
	$data = $this->input->post('data');
	
	echo "didapat " . $data . ' untuk ' . $filename;
		
	}
	
}