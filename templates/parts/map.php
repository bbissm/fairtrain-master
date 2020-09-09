
	<div class="container" <?php $this->attr(); ?>>
	<?php
		$this->controls(
			[
				"/cp/async/container/rmv?id=".$value["cms_container_id"] => ["value"=>translation::get("rmv"),"target"=>"destruct"]
			]
		);
	?>
		<!-- <div id="map"> -->
			<!-- <div class="over-map" onmousemove="" onmouseout=""></div> -->
			<!-- <span>
				<p>CLICK FOR ENTER & PRESS C FOR ESCAPE</p>
			</span> -->
			<!-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1881.6187348132553!2d8.404518337769785!3d47.28982859624015!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x479005f04da2cb4b%3A0xa29480ec09099c20!2sJonentalstrasse+9%2C+8913+Ottenbach!5e1!3m2!1sen!2sch!4v1556029497889!5m2!1sen!2sch" width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen></iframe> -->
		<!-- </div> -->

	</div>