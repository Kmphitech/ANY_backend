<script src="../scripts/jquery.min.js"></script>
<?php 
error_reporting(0);
require_once("connect.php");

	if(isset($_REQUEST['email'])){
	    
		$email=base64_decode($_REQUEST['email']);
		date_default_timezone_set('Asia/Calcutta');
		$cdate=date('Y-m-d H:i:s');
		$query=$conn->query("select * from user_master where email='$email'");
		
	    $admdtl=$query->fetch_object();
	    if(count($admdtl)>0){
	    	$query1=$conn->query("select * from user_master where email='$email' and is_confirm=0");  
	    	$userdtl=$query1->fetch_object();
	    	if(count($userdtl)>0){	    		
	    		$conn->query("update user_master set is_confirm=1,updated_date='$cdate' where email='$email'");
	    		$success="Your registration has been done successfully.<br/>Thank you.";
	    	}
	    	else{
	    		$error="Your email is already confirmed.<br/>Thank you.";
	    	}
	    }
	    else{
	    	$error='Email Address Not Exist.';
	    }   
	}
	else{
		$error='No record Found.';
	}

?>

<?php require_once("head.php"); ?>
   
 <body>
			<div class="container">
				<div class="row">					
					<br/><br/>
					<center><a href="https://tb4o.app.link" target="_blank"><img class="logosize" src="<?=logo;?>"/></a>
						<br/><br/>
						<?php
							if(isset($_REQUEST['email']) && count($admdtl)>0){
						?>
								<h2>Hi, <?=utf8_encode($admdtl->name);?></h2><br/><br/>
						<?php } ?>
						<p style="color: red; clear: both;text-align: center; font-size: 20px"><?php echo $error; ?></p>
						<p style="color: green; clear: both;text-align: center; font-size: 20px"><?php echo $success; ?></p>
						
					</center>
				</div>
			</div>
		<!--footer section start-->
			<?php require_once("footer.php"); ?>
		<!--footer section end-->

</body>
</html>

