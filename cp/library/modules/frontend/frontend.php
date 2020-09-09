<?php
class frontend extends module {
	public static $auth = true;
	public static $authLevel = [1,2,3,4];
	
	public function setup() {
		cp::addSubnavigation([
			"#desktop"=>translation::get("desktop"),
			"#tablet"=>translation::get("tablet"),
			"#mobile"=>translation::get("mobile")
		]);
	}

	public function view() {
		$default = "/";
		if($_GET["path"]!="") {
			$default = $_GET["path"];
		}
		?>
		<script>
			wysiwygSuccess = "<?php echo translation::get("wysiwyg_success"); ?>";
			wysiwygError = "<?php echo translation::get("wysiwyg_error"); ?>";
		</script>
		<main>
			<?php
			$table = new table();
			$table->setup(["form"=>["method"=>"get","action"=>"/cp/async/frontend/view"],"td"=>[0,35,50]]);
			$table->add(["cols"=>[$table->addFormField(["name"=>"path","type"=>"text","attr"=>["readonly"=>true]]),$table->addFormField(["name"=>"copy","type"=>"button","value"=>translation::get("copy")]),$table->addFormField(["name"=>"reload","type"=>"submit","value"=>translation::get("reload")])]]);
			echo $table->render();
			?>

			<div id="container">
				<iframe class="desktop" id="web" frameborder="0" src="<?php echo $default; ?>"></iframe>
			</div>
		</main> 
		<script>
			<?php if($this->user["type"]>=4) { ?>cmsdisabled = true;<?php } ?>
			cms.init(); 
			var laststate = "desktop";
			var last = "";

			function copyToClipboard() {
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val("//"+window.location.hostname+window.location.pathname+"?path="+last).select(); 
				document.execCommand("copy");
				$temp.remove(); 
			}

			$("input[name=copy]").click(function() {
				copyToClipboard();
			}); 

			setInterval(function() {
				if(last!=$("#web").contents().get(0).location.pathname) {
					last = $("#web").contents().get(0).location.pathname;
					$("input[name=path]").val($("#web").contents().get(0).location.pathname);
				}
				
				if(location.hash.replace(/#/g,"").indexOf(laststate)<0 && location.hash.replace(/#/g,"").length>0) {
					laststate=location.hash.replace(/#/g,"");
					if(laststate.length>0) $("#web").attr("class",laststate); 
				}
			},500); 
		</script>  
		<?php 
	}
}
?>