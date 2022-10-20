<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

		<title><?= isset($titel) ? $titel : "FileMaker" ?></title>
l
		<base href="<?= base_url() ?>" target="" />

		<link rel="stylesheet" href="css/reset.css" type="text/css" />
		<link rel="stylesheet" href="css/master.css" type="text/css" />

		<script type="text/javascript" charset="utf-8" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" charset="utf-8" src="js/common.js"></script>


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
			<form id="frmLogin" action="<?= site_url('login') ?>" method="POST">
	
				<h1>Aanmelden</h1>
	
				<p>
					<label>Gebruikersnaam</label>
					<?= form_dropdown('user', $users, set_value('user'), 'style="margin-bottom:5px;"'); ?>
					<br />

					<label>Wachtwoord</label>
					<input type="password" name="pass" id="txtPass" value="" />
		
					<label>&nbsp;</label>
					<button type="submit" name="submit" value="submit">Aanmelden</button>
				</p>
		
				<?php if(!empty($feedback)):?>
					<br />
					<div class="feedback <?= $feedback->type ?>">
						<?php if(isset($feedback->title)):?>
							<h2><?= $feedback->title ?></h2>
						<?php endif; ?>
						<p><?= $feedback->message ?></p>
					</div>
				<?php endif; ?>

			</form>
		</div>
	</div>
</body>
</html>
		