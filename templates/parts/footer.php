<footer class="darkbeige">
	<div class="container centered">
		<div class="article_footer text_white text_small">
			<img src="/templates/web/img/footer.png">
			<p><strong><?php echo translation::get("fairtrain_fuer_hund_und_mensch");?></strong>
			Renée (Mumi) Schenk-ILL <br>
			<a style="color:white;" href="mailto:info@fairtrain.ch">info@fairtrain.ch</a>
			Bolletweg 10 <br>
			CH-8934 Knonau</p>
			<p style="display:flex; flex-direction:row;">
			<?php
				
				echo "<a style=\"color:white;\" href=\"/".$_SESSION['lang']['short']."/impressum\">".translation::get("impressum")."</a> | <a style=\"color:white;\" href=\"/".$_SESSION['lang']['short']."/privacy\">".translation::get("privacy")."</a> | <a style=\"text-decoration:none;color:white;\">Copyright 2019</a></p>";
			?>	
		</div>
	</div>
</footer>
