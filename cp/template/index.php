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

	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/MyFontsWebfontsKit.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/cropper.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/jquery.datetimepicker.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/cp/css/cms.css" />

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=1280" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="/cp/js/sortable.js"></script>
	<script src="/cp/js/dragable.js"></script> 
	<script src="/cp/js/dform.js"></script>
	<script src="/cp/js/tabsystem.js"></script>
	<script src="/cp/js/fileupload.js"></script> 
	<script src="/cp/js/filesearch.js"></script> 
	<script src="/cp/js/filebrowser.js"></script>
	<script src="/cp/js/accordion.js"></script>
	<script src="/cp/js/dragmove.js"></script>
	<script src="/cp/js/imagepreview.js"></script>
	<script src="/cp/js/autopreview.js"></script>
	<script src="/cp/js/cropper.min.js"></script>
	<script src="/cp/js/jquery.datetimepicker.full.min.js"></script>
	<script src="/cp/js/tinymce/tinymce.min.js"></script>
	<script src="/cp/js/tinymce/jquery.tinymce.min.js"></script>
	<script src="/cp/js/cms.js"></script>  

	<?php echo cp::appendHead(); ?>
</head>
<body> 
	<aside id="message"></aside>
	<header>
		<nav>
			<?php echo cp::renderNavigation(); ?>
		</nav>
	</header>
	<?php echo cp::renderSubnavigation(); ?>
	<div id="wrapper"<?php echo cp::subnavigation(); ?>> 
	<?php echo cp::renderView(); ?>
	</div>
	<div id="popup">
		<div>
			<div>
				<div id="close"></div> 
				<div id="popup_content"></div> 
			</div>
		</div>
	</div>
	<div id="imagepreview">
		
	</div>
</body>
</html>