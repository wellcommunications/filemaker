<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

		<title><?= isset($titel) ? $titel : "FileMaker" ?></title>

		<base href="<?= base_url() ?>" target="" />

		<link rel="stylesheet" href="css/reset.css" type="text/css" />
		<link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" type="text/css" />
		<link rel="stylesheet" href="css/master.css" type="text/css" />
		<link rel="stylesheet" href="css/print.css" type="text/css" media="print" />
		
		
		<script type="text/javascript" charset="utf-8" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" charset="utf-8" src="js/jquery-ui-1.8.21.custom.min.js"></script>
		<script type="text/javascript" charset="utf-8" src="js/common.js"></script>
		<?php 
			if(isset($scripts) && count($scripts)>0){
				foreach($scripts as $script){
					echo '<script type="text/javascript" charset="utf-8" src="js/' . $script . '.js"></script>';
				}
			}
		?>

</head>
<body>
	
	<div id="wrapper">
		<!--		HEADER		-->
		<div id="header">		
			<a href="<?= site_url() ?>" title="WELL Communications | FileMaker">
				<img src="img/logo.png" alt="WELL Communications" />
			</a>
		</div>
		
		
		<!--		MAIN		-->
		<div id="content">
			
			<!--	NAVIGATION	-->
			<?php if($this->uri->segment(1)!='login'):	?>
				<div id="nav">
					<ul>
						<li class="<?= ($this->uri->segment(1)=='domains') ? 'active' : '' ?>"><a href="<?= site_url('domains') ?>" title="Domeinnamen">Domeinnamen</a></li>
						<li class="<?= ($this->uri->segment(1)=='hostings') ? 'active' : '' ?>"><a href="<?= site_url('hostings') ?>" title="Hostings">Hostings</a></li>
						<?php if($this->session->userdata('crrUser')->allow_reports):	?>
						<li class="<?= ($this->uri->segment(1)=='reports') ? 'active' : '' ?>"><a href="<?= site_url('reports') ?>" title="Rapporten">Rapporten</a></li>
						<?php endif;	?>
						<li id="btnLogout">Welkom <?= $this->session->userdata('crrUser')->username ?> (<a href="<?= site_url('logout') ?>" title="Uitloggen">uitloggen</a>)</li>
					</ul>
				</div>
			<?php endif;?>
			
			
			<div id="titles">
				<div class="sidebar"><h1><?= $page_title ?></h1></div>
				<div class="main"><h1><?= $sub_title ?></h1></div>
			</div>
			
			
			<div id="page">
				<div class="sidebar">
					<?php if(!empty($subnav)): ?>
						<ul>
							<?php foreach($subnav as $navitem): ?>
								<li class="<?= ($this->uri->segment(2)==$navitem['url']) ? 'active' : '' ?>"><a href="<?= $this->uri->segment(1) . '/' . $navitem['url'] ?>"><?= $navitem['label'] ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				
				<div class="main">
					<?= $content ?>
				</div>
				<br clear="both" />
			</div>
		</div>
		
		
		<?php if($this->uri->segment(1)!='login'):	?>
		<!--		FOOTER		-->
		<div id="footer">

		</div>
		<?php endif; ?>
	</div>


</body>
</html>
