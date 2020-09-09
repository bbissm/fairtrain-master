<?php
		$obj = new kurse();
		$data = $obj->get(["container"=>$value["cms_container_id"]]);
?> 
<section class="kurse" <?php $this->attr(); ?>>
	
		<?php $this->controls([
			// "/cp/async/kurse/view?id=".$value["cms_container_id"]=>["value"=>translation::get("settings")],
			"/cp/async/container/rmv?id=".$value["cms_container_id"]=>["value"=>translation::get("rmv"),"target"=>"destruct"] 
			]);
			$obj->getCourse($value["cms_container_id"]);
			$obj->getCourseContent();
	?>
</section> 
		