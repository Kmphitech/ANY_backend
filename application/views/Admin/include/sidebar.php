<!-- Preloader -->
			<div class="preloader"></div>
			<div class="site-sidebar">
				<div class="custom-scroll custom-scroll-light">
					<ul class="sidebar-menu">						
						<?php
						if ($this->session->is_client == 1) { ?>
							
							<li class="<?php if($this->uri->segment(1)=='Property'){ echo 'active'; }?>">
								<a href="<?=base_url().'Property'?>" class="waves-effect  waves-light">
									<span class="s-icon"><i class="fa fa-building"></i></span>
									<span class="s-text">Property</span>
								</a>
							</li>
							
							
						<?php
						}
						else
						{ ?>
						<li class="<?php if($this->uri->segment(1)=='Home'){ echo 'active'; }?>">
							<a href="<?=base_url().'Home'?>" class="waves-effect  waves-light">
								<span class="s-icon"><i class="ti-anchor"></i></span>
								<span class="s-text">Dashboard</span>
							</a>
						</li>

						<li class="<?php if($this->uri->segment(1)=='User'){ echo 'active'; }?>">
							<a href="<?=base_url().'User'?>" class="waves-effect  waves-light">
								<span class="s-icon"><i class="ti-user"></i></span>
								<span class="s-text">User</span>
							</a>
						</li>

						<li class="<?php if($this->uri->segment(1)=='Property'){ echo 'active'; }?>">
							<a href="<?=base_url().'Property'?>" class="waves-effect  waves-light">
								<span class="s-icon"><i class="fa fa-building"></i></span>
								<span class="s-text">Property</span>
							</a>
						</li>
						
						<li class="<?php if($this->uri->segment(1)=='Exportdata'){ echo 'active'; }?>">
							<a href="<?=base_url().'Exportdata'?>" class="waves-effect  waves-light">
								<span class="s-icon"><i class="fa fa-building"></i></span>
								<span class="s-text">Export Data</span>
							</a>
						</li>
						
						<li class="<?php if($this->uri->segment(1)=='Clients'){ echo 'active'; }?>">
								<a href="<?=base_url().'Clients'?>" class="waves-effect  waves-light">
									<span class="s-icon"><i class="fa fa-users"></i></span>
									<span class="s-text">Client List</span>
								</a>
							</li>
                    <?php
						}
						?>
						
					</ul>
				</div>
			</div>