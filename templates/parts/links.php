<?php
	$obj = new links();
	$data = $obj->get(["container"=>$value["cms_container_id"]]);
		$this->controls(
			[
				"/cp/async/container/rmv?id=".$value["cms_container_id"] => ["value"=>translation::get("rmv"),"target"=>"destruct"]
			]
		);
?>
<section class="seminar" <?php $this->attr(); ?>>

	<?php $this->controls([
		"/cp/async/container/rmv?id=".$value["cms_container_id"]=>["value"=>translation::get("rmv"),"target"=>"destruct"] 
		]);
		$obj->getLinks($value["cms_container_id"]);
		
		echo "<div class=\"container_articles\">";
			$obj->getLinksContent();		
		echo "</div>";
	?>

</section>