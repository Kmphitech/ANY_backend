<?php
ob_start();
error_reporting(0);
class Home extends CI_Controller{
	public function __construct()
    {
    	parent::__construct();
    }
	
	public function index()
	{
		if($this->session->userdata('sess_id'))
		{
		 	
		 	
		 	$data['user_total']=$this->model->record_count("user_master",array("is_confirm"=>1));

		 	$data['property_total']=$this->model->record_count("property_master",array(1=>1));

		 	

		 	$this->load->view('Admin/home',$data);
		}
		else
		{
			redirect(base_url());	
		}	
	}



  

}		