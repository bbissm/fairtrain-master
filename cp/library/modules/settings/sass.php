<?php
class sass extends module {
	public static $auth = true;
	public static $authLevel = [1];
	
	public function setup() {
		cp::addSubnavigation([
			"/cp/usermanagement"=>translation::get("subnavigation_usermanagement"),
			"/cp/translations"=>translation::get("subnavigation_translation"),
			"/cp/language"=>translation::get("subnavigation_language"),
			"/cp/htaccess"=>translation::get("subnavigation_htaccess"),
			"/cp/sass"=>translation::get("subnavigation_sass")
		]);
	}

	public function compile() {
		putenv('PATH=' . getenv('PATH') . ':/usr/local/bin');

		$prefix = explode("/",realpath(dirname(__FILE__)));
		unset($prefix[count($prefix)-1]);
		unset($prefix[count($prefix)-1]);
		unset($prefix[count($prefix)-1]);
		unset($prefix[count($prefix)-1]);
		$prefix = implode("/", $prefix)."/";

		if(!is_array($_SESSION["lastupdate"])) $_SESSION["lastupdate"] = [];

		$dir = scandir("../".$_GET["dir"]."/scss/");
		foreach($dir as $file) {
			if(stristr($file,".scss")) {
				$time = filemtime("../".$_GET["dir"]."/scss/$file");

				if($_SESSION["lastupdate"][$_GET["dir"]."/scss/$file"]<$time) {
					$cmd = config::sassPath." ".$prefix.$_GET["dir"]."/scss/$file ".$prefix.$_GET["dir"]."/css/".str_replace(".scss",".css",$file)." --output-style compressed --source-map-embed";

					$output = "";
					$return_var = ""; 

					exec($cmd." 2>&1",$output,$return_var);

					foreach($output as $line) {
						if(trim($line)!="") echo trim($line)."\n";
					}
					$_SESSION["lastupdate"][$_GET["dir"]."/scss/$file"] = $time;
				}
			}
		} 
	}

	public function view() {		
		$header = new header();
		$header->addTitle(translation::get("sass_title"));
		$header->addParagraph(translation::get("sass_text"));
		echo $header->render();

		echo "<pre class=\"console\">SASS compiler initialized...\n\n</pre>";
		echo "<style type=\"text/css\">";
		echo ".console { background-color:#000;overflow:auto;padding:10px;font-family:courier;font-size:12px;color:#FFF;height:400px;margin:0px; }";
		echo "</style>";

		echo "<script>";
		echo "$('.console').css({height:'calc(100% - '+($('.console').offset().top-77)+'px)'});";
		echo "setInterval(function() {
			$.get('/cp/async/sass/compile?dir=cp',function(data) {
				if(data.length>10) {
					$('.console').append(data+'\\n');
					$('.console').animate({ scrollTop: $('.console')[0].scrollHeight+'px' }, 'fast');
				}
			});

			$.get('/cp/async/sass/compile?dir=templates/web',function(data) {
				if(data.length>10) {
					$('.console').append(data+'\\n');
					$('.console').animate({ scrollTop: $('.console')[0].scrollHeight+'px' }, 'fast');
				}
			});
		},1000);";
		echo "</script>";
	} 
}
?> 