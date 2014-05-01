<?php
	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

?>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<style type="text/css">
			body,input {font-size:16px; font-family:Verdana, Arial, Helvetica, sans-serif;}
			body {text-align:center; width:640px; margin:64px auto;}
			table {margin: 16px auto;}
		</style>
	</head>
	<body>
<?php
$version = qa_opt('cs_version');
$suggest='<p><a href="'.qa_path_html('admin', null, null, QA_URL_FORMAT_SAFEST).'">Go to admin center</a></p>';
// first installation
if (!(isset($version))){ 
	reset_theme_options();
	echo '<p>CleanStrap is installed.</p>';
}
/*
if ($version < CS_VERSION){
	echo '<p>CleanStrap is updated to version 2.1</p>';
}
if ($version < CS_VERSION){
	echo '<p>CleanStrap is updated to version 2.2</p>';
}
if ($version < CS_VERSION){
	echo '<p>CleanStrap is updated to version 2.3</p>';
}
*/

qa_opt('cs_version',CS_VERSION);
if ($version==CS_VERSION){
	echo '<p>Your Theme is up to date.</p>';
}
echo $suggest;
?>

	</body>
</html>	