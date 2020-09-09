<?php

	$obj = new socialmedia_slider();
	$data = $obj->get($value["cms_container_id"]);

?>
<section class="slider" <?php $this->attr();?>>
	<?php
		$this->controls(
			[
				"/cp/async/socialmedia_slider/view?id=".$value["cms_container_id"] => ["value"=>translation::get("settings")],
				"/cp/async/container/rmv?id=".$value["cms_container_id"] => ["value"=>translation::get("rmv"),"target"=>"destruct"]
			]
		);
	?>
<div class="container centered">
	<div class="slider_container slider">

		<?php foreach($data as $key => $value) { ?>
			<img src="/assets/media/img/facebook1.png" />		
		<?php } ?>
	
	</div>
	<a class="icon_btn button" ><?php echo translation::get("mehr_auf");?><img src="/templates/web/img/icon-fb.png"/></a>
</div>
</section>