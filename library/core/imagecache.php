<?php
class imagecache extends module {
	public function get() {
		if (file_exists($_GET["path"])) {
			/*header('Pragma: public');
		header('Cache-Control: max-age=86400');
		header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + config::IMAGECACHETIME));
		header('Content-Type: '.mime_content_type($_GET["path"]));

		readfile($_GET["path"]);*/
		}
	}
}
?>
