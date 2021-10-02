<?php
ob_start();
error_reporting(0);
class Login extends CI_Controller{
	public function __construct()
    {
    	parent::__construct();
    }

	public function index()
	{
		//$this->load->view('Admin/login');

		/*if($this->input->post('login')){			
        	$email=$this->input->post('email');
			$pass=md5($this->input->post('password'));
			$pass1=$this->input->post('password');
			$rem=$this->input->post('rem');

			$data=array("email"=>$email,"password"=>$pass);

			$admindata=$this->model->sel_row("admin_master",$data);
			$total_admin=$this->model->record_count("admin_master",$data);

			if($total_admin>0){
					$datas=array("sess_id"=>$admindata->id,"is_client"=>$admindata->is_client);
					$this->session->set_userdata($datas);
					if($rem=="rem_email_pas"){
						setcookie("lc_mail", $email,strtotime('+1 month'));
						setcookie("lc_pass", $pass1,strtotime('+1 month'));
					}
					if ($admindata->is_client == 1) 
					{
						redirect(base_url()."Property");
					}
					else
					{
						redirect(base_url()."Home");	
					}
			}
			else{
				$this->session->set_userdata('error', 'Incorrect Email or Password.');
				redirect(base_url());
			}
		}*/
	}

	public function logout()
	{
		$this->session->sess_destroy();	
		redirect(base_url());	
	}


}

?>