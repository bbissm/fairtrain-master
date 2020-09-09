<?php error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR); ?>
<!DOCTYPE HTML>
<!--
****
**** Concept, design and implementation by
**** DimasterSoftware GmbH
**** Sellenbueren 59a
**** 8143 Stallikon
**** Switzerland
**** http://www.dimastersoftware.ch
**** 
-->
<html lang="de">
<head>
	<title>DimasterSoftware</title>

	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/MyFontsWebfontsKit.css">
	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/cropper.min.css">
	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/cms.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="/cp/js/sortable.js"></script>
	<script src="/cp/js/dragable.js"></script>
	<script src="/cp/js/dform.js"></script>
	<script src="/cp/js/tabsystem.js"></script>
	<script src="/cp/js/fileupload.js"></script> 
	<script src="/cp/js/accordion.js"></script>
	<script src="/cp/js/dragmove.js"></script>
	<script src="/cp/js/cropper.min.js"></script>
	<script src="/cp/js/cms.js"></script>  

	<?php echo cp::appendHead(); ?>
</head>
<body class="gradient"> 
	<aside id="message"></aside>
	<div id="login">
		<?php echo cp::login(); ?>
	</div>
</body>
</html>