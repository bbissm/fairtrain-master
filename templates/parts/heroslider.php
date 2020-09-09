<?php

	$obj = new heroslider();
	$data = $obj->get($value["cms_container_id"]);

?>
<!-- schreibt cms attribute rein -->
<header <?php $this->attr();?>>
	<?php
		$this->controls(
			[
				"/cp/async/heroslider/view?id=".$value["cms_container_id"] => ["value"=>translation::get("settings")],
				"/cp/async/container/rmv?id=".$value["cms_container_id"] => ["value"=>translation::get("rmv"),"target"=>"destruct"]
			]
		);
	?>
	<input type="hidden" id="header" value="<?php echo $_SESSION["path"][1];?>" />

	<div class="header-content">
		<div class="header-img slider-header slider">
			<?php 
				$class = "";
				
				foreach($data as $k => $v) { 
					if($v["no_overlay"] == 1){
						$class="";
						$style="display:flex;justify-content:center;align-items:flex-end;/* margin-bottom:30px; */padding-bottom: 70px;";
					}else{
						$class="overlay";
					}
			?>
			
				<div class="test" style="background-image: url('<?php echo $v['image']; ?>');">
					<div style="<?php echo $style?>" class="<?php echo $class; ?>">
						<h1><?php echo $v["text"]; ?></h1>
					</div>					
				</div>
			<?php } ?>
		</div>
	</div>
</header>
