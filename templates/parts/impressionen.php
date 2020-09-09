
<?php
$obj_html = new html();
$obj_html->setup();
$data = $obj_html->get(["container" => $value["cms_container_id"]]);

?>
<section class="impressionen_section" <?php if ($_SESSION["login"]) {echo "action='/cp/async/html/update?id=" . $value["cms_container_id"] . "'";}?>>
<?php
$this->controls(
	[
		"/cp/async/container/rmv?id=" . $value["cms_container_id"] => ["value" => translation::get("rmv"), "target" => "destruct"],

	]
);
?>
<div class="container">
	<div class="wysiwyg" action="/cp/async/html/update?id=<?php echo $value["cms_container_id"] . "\""; ?>">
		<?php echo $data["html"]; ?>
	</div>
	<div class="impressionen">
		<!-- INSTAGRAM CONTENT -->
	</div>
	<!-- <div class="centered_btn"> -->
		<a class="button icon_btn" href="https://www.instagram.com/fairtrain_fur_hund_und_mensch/"><?php echo translation::get("mehr_auf"); ?><svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<g>
	<g>
		<path d="M352,0H160C71.648,0,0,71.648,0,160v192c0,88.352,71.648,160,160,160h192c88.352,0,160-71.648,160-160V160
			C512,71.648,440.352,0,352,0z M464,352c0,61.76-50.24,112-112,112H160c-61.76,0-112-50.24-112-112V160C48,98.24,98.24,48,160,48
			h192c61.76,0,112,50.24,112,112V352z"/>
	</g>
</g>
<g>
	<g>
		<path d="M256,128c-70.688,0-128,57.312-128,128s57.312,128,128,128s128-57.312,128-128S326.688,128,256,128z M256,336
			c-44.096,0-80-35.904-80-80c0-44.128,35.904-80,80-80s80,35.872,80,80C336,300.096,300.096,336,256,336z"/>
	</g>
</g>
<g>
	<g>
		<circle cx="393.6" cy="118.4" r="17.056"/>
	</g>
</g>
</svg>
</a>
	<!-- </div> -->
	<script>instagramApi();</script>
</section>