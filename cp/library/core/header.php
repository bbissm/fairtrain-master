<?php
class header {
	public $content;

	public function addTitle($content) {
		$this->content.="<h1>$content</h1>";
	}

	public function addSubtitle($content) {
		$this->content.="<h2>$content</h2>";
	}

	public function addParagraph($content) {
		$this->content.="<p>$content</p>";
	}

	public function addHTML($content) {
		$this->content.=$content;
	}

	public function render() {
		ob_start();
		echo "<section>";
		echo "<header>";
		echo $this->content;
		echo "</header>";
		echo "</section>";
		return ob_get_clean();
	}
}
?>