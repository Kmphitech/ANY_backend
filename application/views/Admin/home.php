<?php require_once("include/head.php"); ?>

	<body class="fixed-sidebar fixed-header skin-default content-appear">
		<div class="wrapper">
			<!-- Sidebar -->
				
			<?php require_once("include/sidebar.php"); ?>			
			
			<?php require_once("include/header.php"); ?>

			<div class="site-content">
				<!-- Content -->
				<div class="content-area py-1">
					<div class="container-fluid">
						<div class="row row-md">
							
							<!-- div class="col-lg-3 col-md-6 col-xs-12">
								<div class="box box-block bg-white tile tile-1 mb-2">
									<div class="t-icon right"><span class="bg-danger"></span><i class="ti-user"></i></div>

									<div class="t-content" onclick="gotoPage('Driver')">
										<h6 class="text-uppercase mb-1">Driver</h6>
										<h1 class="mb-1"><?=$driver_total;?></h1>
										<span class="tag mr-0-5">&nbsp;</span>
										<span class="text-muted font-90"></span>
									</div>
								</div>
							</div> -->

							<div class="col-lg-3 col-md-6 col-xs-12">
								<div class="box box-block bg-white tile tile-1 mb-2">
									<div class="t-icon right"><span class="bg-success"></span><i class="ti-user"></i></div>
									<div class="t-content" onclick="gotoPage('User')">
										<h6 class="text-uppercase mb-1">User</h6>
										<h1 class="mb-1"><?=$user_total;?></h1>
										<i class="ftext-success mr-0-5"></i>
									</div>
								</div>
							</div>

							<div class="col-lg-3 col-md-6 col-xs-12">
								<div class="box box-block bg-white tile tile-1 mb-2">
									<div class="t-icon right"><span class="bg-primary"></span><i class="fa fa-building"></i></div>
									<div class="t-content" onclick="gotoPage('Property')">
										<h6 class="text-uppercase mb-1">Property</h6>
										<h1 class="mb-1"><?=$property_total;?></h1>
										<span class="tag mr-0-5">&nbsp;</span>
									</div>
								</div>
							</div>

							<!-- <div class="col-lg-3 col-md-6 col-xs-12">
								<div class="box box-block bg-white tile tile-1 mb-2">
									<div class="t-icon  right"><span class="bg-warning"></span><i class="ti-gallery"></i></div>
									<div class="t-content" onclick="gotoPage('Item')">
										<h6 class="text-uppercase mb-1">Item</h6>
										<h1 class="mb-1"><?=$item_total;?></h1>
										<div id="">&nbsp;</div>
									</div>
								</div>
							</div> -->

						</div>
						
					</div>
				</div>
				
			</div>

		</div>

		<?php require_once("include/all_script.php"); ?>
		<script type="text/javascript">
			function gotoPage(pgname) {
				window.location.href="<?=base_url()?>"+pgname;
			}
		</script>
	</body>

</html>