<!DOCTYPE html>
<html lang="en">
<head>
    <title>thus application Installer</title>
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link type='text/css' href='css/style.css' rel='stylesheet'/>
    <link type='text/css' href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body style='background-color: #FBFBFB;'>
	<div id='main-container'>
		<div class='header'>
			<div class="header-box wrapper">
				<div class="hd-logo"><a href="#"><img src="img/logo.png" alt="Logo"/ width="50"></a></div>
			</div>
		</div>
		
		<div class="span12">
			<h4> HotspotBillingManager v1.0 Installer </h4>
			<?php
			$passed = '';
			$ltext = '';
			if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
				$ltext .= 'To run thus application, you need at least PHP version 5.2.0, Your PHP Version is: ' . PHP_VERSION . " Tested <strong>---PASSED---</strong><br/>";
				$passed .= '1';

			} else {
				$ltext .= 'To run thus application, you need at least PHP version 5.2.0, Your PHP Version is: ' . PHP_VERSION . " Tested <strong>---FAILED---</strong><br/>";
				$passed .= '0';

			}

			if (extension_loaded('PDO')) {
				$ltext .= 'PDO is installed on your server: ' . "Tested <strong>---PASSED---</strong><br/>";
				$passed .= '1';
			} else {
				$ltext = 'PDO is installed on your server: ' . "Tested <strong>---FAILED---</strong><br/>";
				$passed .= '0';
			}

			if (extension_loaded('pdo_mysql')) {
				$ltext .= 'PDO MySQL driver is enabled on your server: ' . "Tested <strong>---PASSED---</strong><br/>";
				$passed .= '1';
			} else {
				$ltext .= 'PDO MySQL driver is not enabled on your server: ' . "Tested <strong>---FAILED---</strong><br/>";
				$passed .= '0';
			}
			
			if ($passed == '111') {
				echo("<br/> $ltext <br/> Great! System Test Completed. You can run HotspotBillingManager on your server. Click Continue For Next Step.
				<br><br>
				<a href=\"step3.php\" class=\"btn btn-primary\">Continue</a>");
			} else {
				echo("<br/> $ltext <br/> Sorry. The requirements of thus application is not available on your server.
				Please contact with us- iesien22@yahoo.com with this code- $passed Or contact with your server administrator
				<br><br>
				<a href=\"#\" class=\"btn btn-primary disabled\">Correct The Problem To Continue</a>");
			}
			?>
		</div>
	</div>
	<div class="footer">Copyright &copy; 2021 HotspotBillingManager<br/><br/></div>
</body>
</html>

