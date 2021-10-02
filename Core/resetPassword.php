<script src="../assets/core/jquery.min.js"></script>
<?php 
	require_once("connect.php");

	$email=base64_decode($_REQUEST['email']);
	$query=$conn->query("select email,ucode from user_master where email='$email'");  
    $userdtl=$query->fetch_object();
    if($userdtl->ucode!=""){
	if(isset($_REQUEST['reset_pass'])){
        	$npass=$_REQUEST['npass'];
        	$cpass=$_REQUEST['cpass'];
        	
        	//$email=base64_decode($_REQUEST['email']);

        	$query=$conn->query("select email,ucode from user_master where email='$email'");  
        	$userdtl=$query->fetch_object();

        	if(count($userdtl)>0){
		          if($npass==$cpass){
		          	date_default_timezone_set('Asia/Calcutta');
		          	$cdate=date('Y-m-d H:i:s');
		          	$newpass=md5($npass);
		          	$qrUpd="update user_master set pass='$newpass',ucode='',updated_date='$cdate' where email='$email'";
		            $conn->query($qrUpd);
		            $error='<div style="color:green; text-align:center; font-size:20px"> Password reset successfully.</div>';?>
					<script type="text/JavaScript">
						$(document).ready(function(){
							$("#form").hide();
						});
					</script>
		        <?php   }
		          else{
		            $error='Password mismatch, please enter same password.';
		          }	
		                  
		    }
		    else{
		        $error='Unable to change password because of Invalid email.';
				
		    }
    	}
	}
	else{
		header("location:resetPasswordDone.php");
	} 	
?>
<?php require_once("head.php"); ?>
   
 <body class="sign-in-up" style="text-align: center;">
    <section>
			<div id="page-wrapper" class="sign-in-wrapper">
				<div class="graphs">
					<div class="sign-in-form">
						<center><img src="<?=logo;?>" class="logosize" />	
						</center><br/>	
						<div class="sign-in-form-top">
							<p><span>Reset Password</span> </p>
						</div>
						<div class="signin" style="position: static;">
							
						<form method="post" id="form">
							

							<div class="log-input">
								<div class="log-input-left" style="width: 100%">
								   <input type="password" placeholder="New Password" name="npass" class="lock" required="required" minlength="5" />
								</div>
								
								<div class="clearfix"> </div>
							</div>
							<div class="log-input">
								<div class="log-input-left" style="width: 100%">
								   <input type="password" class="lock" name="cpass" placeholder="Confirm Password" required="required"  />
								</div>
								
								<div class="clearfix"> </div>
							</div>
							<center><input type="submit" name="reset_pass" value="Change" />
								</center>
						</form><br/>
						
						<p style="color: red; clear: both;font-size: 20px;"><?php echo $error; ?></p>
						
						<br/>
						
						</div>

					</div>
				</div>
			</div>
		<!--footer section start-->
			<?php require_once("footer.php"); ?>
		<!--footer section end-->
	</section>

<!-- Bootstrap Core JavaScript -->
   <script src="../assets/core/bootstrap.min.js"></script>
</body>
</html>


 