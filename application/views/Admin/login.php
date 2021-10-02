<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="description" content="<?=server_name?>">
		<meta name="author" content="Mayur">
		<!-- Title -->
		<title><?=server_name?></title>
		<link rel="shortcut icon" href="<?=email_logo?>" type="image/x-icon">
		<!-- Vendor CSS -->
		<link rel="stylesheet" href="<?=base_url()?>assets/css/bootstrap.min.css">
		<link rel="stylesheet" href=".<?=base_url()?>assets/css/themify-icons.css">
		<link rel="stylesheet" href="<?=base_url()?>assets/css/font-awesome.min.css">
		
		<link rel="stylesheet" href="<?=base_url()?>assets/css/core.css">
	</head>

	<body class="img-cover" style="background-image: url(assets/images/bg.jpg);">
		
		<div class="container-fluid">
			<div class="sign-form">
				<div class="row">
					<div class="col-md-4 offset-md-4 px-3">
						<div class="box b-a-0">
							<div class="p-2 text-xs-center">
								<h5>Login Panel</h5>
							</div>

							<form method="post" class="form-material">
								<div class="form-group">
									<input name="email" type="email" class="form-control" id="exampleInputEmail" placeholder="Email" value="<?=$_COOKIE['lc_mail'];?>"  />
								</div>

								<div class="form-group">
									<input name="password" type="password" class="form-control" id="exampleInputPassword" placeholder="Password" value="<?=$_COOKIE['lc_pass'];?>" />
								</div>
 
								<div class="form-group">
									<input type="checkbox" name="rem" class="remember" value="rem_email_pas" style="width:18px;height: 18px; margin-left: 5px;" />
									<span style="position:relative;top:-4px;">Remember me</span>
									<br/>
								</div>
								
								<div class="px-2 form-group mb-0">
									<input type="submit" name="login" class="btn btn-purple btn-block text-uppercase" value="Sign In" />
								</div>
							</form>

							<div class="p-2 text-xs-center text-muted">
								<p style="color: red; clear: both;"><?php echo $this->session->error; ?></p>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Vendor JS -->
		<script type="text/javascript" src="<?=base_url()?>assets/js/jquery-1.12.3.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>assets/js/tether.min.js"></script>
		<script type="text/javascript" src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
	</body>

</html>