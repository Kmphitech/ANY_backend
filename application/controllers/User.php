<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->session->set_flashdata("success","");
        $this->session->set_flashdata("error","");
        if(isset($_REQUEST['email'])){
            $email=base64_decode($_REQUEST['email']);
            date_default_timezone_set('Asia/Calcutta');
            $cdate=date('Y-m-d H:i:s');
            $admdtl=$this->model->sel_row("user_master",array("email"=>$email));
            
            if(count($admdtl)>0){
                $userdtl=$this->model->querydatar("select * from user_master where email='$email' and is_confirm=0");  
                
                if(count($userdtl)>0){              
                    $this->model->update("user_master",array("is_confirm"=>1,"updated_date"=>$cdate),array("email"=>$email));

                    $success="Your registration has been done successfully.<br/>Thank you.";
                    $this->session->set_flashdata("success",$success);
                }
                else{
                    $error="Your email is already confirmed.<br/>Thank you.";
                    $this->session->set_flashdata("error",$error);
                }
            }
            else{
                $error='Email Address Not Exist.';
                $this->session->set_flashdata("error",$error);
            }   
        }
        else{
            $error='No record Found.';
            $this->session->set_flashdata("error",$error);
        }

        $data['admdtl']=$admdtl;
        $this->load->view("Core/user",$data);

    }

    public function resetPassword()
    {
        $this->load->view("Core/resetPassword");
        $email = base64_decode($_REQUEST['email']);
        $userdtl = $this->model->querydatar("select email,ucode from user_master where email='$email'");

        if ($userdtl->ucode != "") {

            if (isset($_REQUEST['reset_pass'])) {
                $npass = $_REQUEST['npass'];
                $cpass = $_REQUEST['cpass'];

                if (count($userdtl) > 0) {
                    if ($npass == $cpass) {
                        $newpass = md5($npass);
                        $this->model->update("user_master", array("pass" => $newpass, "ucode" => "", "updated_date" => cur_date_time), array("email" => $email));

                        $success = 'Password reset successfully.';
                        $sucPar = 1;
                        $this->session->set_userdata("success", "yes");
                        redirect(base_url() . 'User/resetPasswordDone');
                        //$this->session->set_flashdata("sucPar",$sucPar);
                    } else {
                        $error = 'Password mismatch, please enter same password.';
                        $this->session->set_flashdata("error1", $error);
                        redirect(current_url() . '?email=' . $_REQUEST['email']);
                    }
                } else {
                    $error = 'Unable to change password because of Invalid email.';
                    $this->session->set_flashdata("error1", $error);
                    redirect(current_url() . '?email=' . $_REQUEST['email']);
                }
            }

        } else {
            redirect(base_url() . 'User/resetPasswordDone');
        }
    }

    public function resetPasswordDone()
    {
        $this->load->view("Core/resetPasswordDone");
        $this->session->unset_userdata("success");
    }

}

?>