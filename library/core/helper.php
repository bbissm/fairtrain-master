<?php
class helper {
	public static function requestFormat($obj) {
		return base64_encode(json_encode($obj));
	}

	public static function cmsHead() {
		if($_SESSION["login"]) {
			echo "<link class=\"removenocms\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/cp/css/MyFontsWebfontsKit.css\">";
			echo "<link class=\"removenocms\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"/cp/css/cmsframe.css\">";
		
			echo "<script class=\"removenocms\" src=\"/cp/js/tinymce/tinymce.min.js\"></script>";
			echo "<script class=\"removenocms\" src=\"/cp/js/tinymce/jquery.tinymce.min.js\"></script>";
			echo "<script class=\"removenocms\" src=\"/cp/js/filebrowser.js\"></script>";

			echo "<script>";
			echo "if ( self == top ) {
				$('.removenocms').remove();
			}";
			echo "</script>";
		}
	}
}
?>