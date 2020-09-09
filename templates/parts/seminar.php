<?php
	$obj = new seminar();
	$data = $obj->get(["container"=>$value["cms_container_id"]]);
?> 
<section class="seminar" <?php $this->attr(); ?>>

	<?php $this->controls([
		// "/cp/async/seminar/settings?id=".$value["cms_container_id"]=>["value"=>translation::get("settings")],
		"/cp/async/container/rmv?id=".$value["cms_container_id"]=>["value"=>translation::get("rmv"),"target"=>"destruct"] 
		]);
		$obj->getSeminar($value["cms_container_id"]);
		
		echo "<div class=\"fadeout_containers\">";
			$obj->getSeminarContent();		
		echo "</div>";
	?>
</section> 
		