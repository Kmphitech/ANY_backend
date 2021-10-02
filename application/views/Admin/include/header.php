<div class="site-header">
				<nav class="navbar navbar-light">
					<div class="navbar-left">
						<a class="navbar-brand" href="<?=base_url().'Home'?>">
							<div class="logotxt"><h3><?=server_name?></h3></div>
						</a>

						<div class="toggle-button dark sidebar-toggle-first float-xs-left hidden-md-up">
							<span class="hamburger"></span>
						</div>

						<div class="toggle-button-second dark float-xs-right hidden-md-up">
							<i class="ti-arrow-left"></i>
						</div>

						<div class="toggle-button dark float-xs-right hidden-md-up" data-toggle="collapse" data-target="#collapse-1">
							<span class="more">
								
							</span>
						</div>

					</div>

					<div class="navbar-right navbar-toggleable-sm collapse" id="collapse-1">
						
						<ul class="nav navbar-nav float-md-right">							
							
							<!-- <li class="nav-item dropdown">
								<a class="nav-link" href="#" data-toggle="dropdown" aria-expanded="false">
									<i class="ti-email"></i>
									<span class="hidden-md-up ml-1">Notifications</span>
									<span class="tag tag-danger top">2</span>
								</a>
								
								<div class="dropdown-messages dropdown-tasks dropdown-menu dropdown-menu-right animated fadeInUp">
									<div class="m-item">
										<div class="mi-icon bg-info"><i class="ti-comment"></i></div>
										<div class="mi-text"><a class="text-black" href="#">Bhavini Patel</a> <span class="text-muted">Create new item</span> <a class="text-black" href="#">View new item</a></div>
										<div class="mi-time">5 min ago</div>
									</div>

									<div class="m-item">
										<div class="mi-icon bg-danger"><i class="ti-heart"></i></div>
										<div class="mi-text"><a class="text-black" href="#">Megha Zadafiya</a> <span class="text-muted">Create new store</span> <a class="text-black" href="#">View new store</a></div>
										<div class="mi-time">15:07</div>
									</div>
								</div>
							</li> -->

							<li class="nav-item dropdown hidden-sm-down">
								<a href="#" data-toggle="dropdown" aria-expanded="false">
									<span class="avatar box-32">
										<img src="<?=base_url()?>assets/images/1.png" alt="">
									</span>
								</a>
								<div class="dropdown-menu dropdown-menu-right animated fadeInUp">
									<a class="dropdown-item" href="<?=base_url().'Login/logout'?>"><i class="ti-power-off mr-0-5"></i> Sign out</a>
								</div>
							</li>
						</ul>
					</div>

				</nav>
			</div>