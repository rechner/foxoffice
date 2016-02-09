<html>
	<head>
		<title>FoxOffice</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="main.css">
		<script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">
						<i class="fa fa-ticket fa-lg fa-fw"></i>FoxOffice
					</a>
				</div>
				<ul class="nav navbar-nav">
					<li<?php if ($_SERVER['SCRIPT_NAME'] == "/index.php") echo ' class="active"';?>><a href="index.php"><i class="fa fa-tachometer"></i> Dashboard</a></li>
					<li<?php if ($_SERVER['SCRIPT_NAME'] == "/user.php") echo ' class="active"';?>><a href="user.php"><i class="fa fa-users"></i> Users</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i>
							<?php
                				if(login_check($pdo)){
                					echo $_SESSION['username'];
                				}
                			?>
						<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="logout.php"><i class="fa fa-power-off"></i> Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
		<?php show_notices(); ?>