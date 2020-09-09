<section <?php $this->attr(); ?>>
	<?php
		$this->controls(
			[
				"/cp/async/container/rmv?id=".$value["cms_container_id"] => ["value"=>translation::get("rmv"),"target"=>"destruct"]
			]
		);
		$team = new team();
		$mitglied = $team->getTeam();
	?>
	<div class="container gallery">
		<h1>Fairtrain Team</h1>
		<div class="row">
			<?php
				while($res = $mitglied->fetch_assoc()){
					echo "<div class=\"container_img2\" style=\"background-image:url(".$res["image"].");\">";
						echo "<div class=\"half_container\">";
							echo "<h3>".$res["name"]."</h3>";
							echo "<a href=\"/".$_SESSION['lang']['short']."/".$_SESSION["path"][1]."/teamdetails?mitglied=".$res["leitung_id"]."\" class=\"button\">Details</a>";
						echo "</div>";
					echo "</div>";
				}
			?>
		</div>
	</div>
</section>