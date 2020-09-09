<?php
class media extends module {
	public static $auth = true;
	public static $authLevel = [1,2,3];
	
	public function controller() {
		if($_GET["prmv"]!="") {
			@unlink($_GET["prmv"]);
		} 

		if($_POST["target"]!="" && $_FILES["file"]["name"]!="") {
			copy($_FILES["file"]["tmp_name"],"assets/media/".$_POST["target"].$_FILES["file"]["name"]);
		}

		if($_FILES["file"]["name"]!="" && $_SESSION["push"]!="" && $_SESSION["push"]!=null) {
			copy($_FILES["file"]["tmp_name"],$_SESSION["push"]."/".$_FILES["file"]["name"]);
		}

		if($_POST["folder"]!="") {  
			mkdir("../assets/media/".$_POST["folder"]."/");
			echo cp::message(["message"=>translation::get("success"),"type"=>"success"]); 
		}

		if($_GET["rmvfolder"]!="") {
			rmdir($_GET["rmvfolder"]);
		}
	}

	public function cache($config=[]) {
		if($_GET["src"]!="") {
			$config = $_GET;
		}

		$src = "..".$config["src"];
		$dir = str_replace("../assets/","../assets/cache/",$src);
		$rcdir = explode("/",$dir);
		$sdir = ""; 
		foreach($rcdir as $k=>$ndir) {
			$sdir.=$ndir."/";
			if($k>=1 && count($rcdir)-1>$k) {
				@mkdir($sdir);
			}
		} 

		copy($src,$dir);
		echo trim(substr($dir,2));
	}

	public function resize($config=[]) {
		if($_GET["src"]!="" && $_GET["width"]!="" && $_GET["height"]!="") {
			$config = $_GET;
		}

		putenv('PATH=' . getenv('PATH') . ':/usr/local/bin');

		$output = "";
		$return_var = "";
		$src = $_GET["src"];

		if(!stristr($src,"../")) {
			$src = "..".$src;
		}

		list($width, $height, $type, $attr) = getimagesize(urldecode($src));
		$dir = "";

		if($width>=$height) {	
			$dir = str_replace("../assets/","../assets/cache/".$config["width"]."/x/",$src);

			$rcdir = explode("/",$dir);
			$sdir = ""; 
			foreach($rcdir as $k=>$ndir) {
				$sdir.=$ndir."/";
				if($k>=1 && count($rcdir)-1>$k) {
					@mkdir($sdir);
				}
			} 

			if(!file_exists($dir)) {
				exec(config::convertLocation." \"".$src."\" -geometry ".$config["width"]."x -quality 80 -density 72 \"".$dir."\" 2>&1",$output,$return_var);  
			}
		} else { 
			$dir = str_replace("../assets/","../assets/cache/x/".$config["height"]."/",$src);

			$rcdir = explode("/",$dir);
			$sdir = ""; 
			foreach($rcdir as $k=>$ndir) {
				$sdir.=$ndir."/";
				if($k>=1 && count($rcdir)-1>$k) {
					@mkdir($sdir);
				} 
			} 

			if(!file_exists($dir)) {
				exec(config::convertLocation." \"".$src."\" -geometry x".$config["height"]." -quality 80 -density 72 \"".$dir."\" 2>&1",$output,$return_var);  
			}
		}

		echo trim(substr($dir,2));
	}

	public function set() {
		if($_GET["pushid"]!="") { 
			if($_SESSION["push"]!=$_GET["pushid"]) {
				$_SESSION["push"]=$_GET["pushid"]; 
			} else {
				$_SESSION["push"]=null;  
			}
		} 
	}

	public function filesearch($config=[]) {
		ob_start(); 
		
		echo "<div class=\"filesearch\" type=\"".$config["type"]."\">";
			echo "<input type=\"text\" class=\"nosubmit\" name=\"find-filesearch\" >";
			echo "<input type=\"hidden\" name=\"target-filesearch\" value=\"".$config["target"]."\">";
			echo "<input type=\"button\" name=\"search-filesearch\" value=\"Suchbegriff\">";
		echo "</div>";
		return ob_get_clean();
	}

	public function imagepicker($config=[]) {
		if($_GET["target"]!="") {
			$config["target"]=$_GET["target"]; 
		}
 
		$table1 = new table(); 
		$table1->setup([]);
		$table1->add(["cols"=>[$table1->addFileupload(["name"=>"file","attr"=>["target"=>".fileselector","url"=>"/cp/async/media/imagepicker?target=".$config["target"]]])]]);
		echo $table1->render();  

		$config["type"]="imagepicker";
		//echo $this->filesearch($config);

		$folder = "../assets/media/";
		$root = "/assets/media/";
		$dir = scandir($folder);
		sort($dir);

		foreach($dir as $k=>$v) {
			if(in_array($v, config::mediaPriority)) {
				unset($dir[$k]);
			}
		}

		$dir = array_merge(config::mediaPriority,$dir); 

		if(isset($_GET["search"]) && $_GET["search"]!=""){
			$files = [];
			foreach($dir as $value) {
				$dir1 = scandir($folder.$value."/");
				foreach($dir1 as $key1=>$value1) {
					if(!is_file($folder.$value."/".$value1)) continue; 
					$filetype = explode(".",$value1);
					$filetype = strtolower($filetype[count($filetype)-1]);
					if($filetype!="jpg" && $filetype!="jpeg" && $filetype!="png" && $filetype!="gif") continue;

					if($_GET["search"]!="") {
						if(!stristr($value1,$_GET["search"])) continue;
					}

					$files[filemtime($folder.$value."/".$value1)."_".$value1] = $root.$value."/".$value1;
				}
			}
			krsort($files);

			$table = new table();
			$table->setup(["class"=>["sortable"],"td"=>[0,100,80,110]]);
 
			foreach($files as $key => $value1) {
				$table->add(["cols"=>["<a href=\"$value1\" class=\"fileselector\" target=\"".$config["target"]."\">".basename($value1)."</a>","<div class=\"auto-preview\" href=\"..$value1\"></div>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize("..".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime("..".$value1))]],"controls"=>["/cp/async/media/imagepicker?target=".$config["target"]."&prmv=".urlencode($value1)=>["name"=>translation::get("rmv"),"target"=>".fileselector"]]]); 
			}
			echo $table->render();  
		}else{
			$accordion = new accordion(); 
			$accordion->setup(["attr"=>["single"=>true,"push"=>"/cp/async/media/set"]]); 

			foreach($dir as $key=>$value) {
				if(is_dir($folder.$value) && $value!="." && $value!="..") {
					$table = new table();
					$table->setup(["class"=>["sortable"],"td"=>[0,100,80,110]]);

					$files = [];
					$in = 0;
					$dir1 = scandir($folder.$value."/"); 
					foreach($dir1 as $key1=>$value1) {
						if(!is_file($folder.$value."/".$value1)) continue;
						$filetype = explode(".",$value1);
						$filetype = strtolower($filetype[count($filetype)-1]);
						if($filetype!="jpg" && $filetype!="jpeg" && $filetype!="png" && $filetype!="gif") continue;

						$files[filemtime($folder.$value."/".$value1)."_".$value1] = $value1;
						$in = 1;
					}  

					krsort($files);
					foreach($files as $key1=>$value1) {
						$table->add(["cols"=>["<a href=\"$root$value/$value1\" class=\"fileselector\" target=\"".$config["target"]."\">$value1</a>","<div class=\"auto-preview\" href=\"$folder$value/$value1\"></div>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize($folder.$value."/".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime($folder.$value."/".$value1))]],"controls"=>["/cp/async/media/imagepicker?target=".$config["target"]."&prmv=".urlencode($folder.$value."/".$value1)=>["name"=>translation::get("rmv"),"target"=>".fileselector"]]]); 
					}

					$row = ["title"=>$value,"content"=>$table->render(),"attr"=>["pushid"=>"$folder$value"]];

					if($_SESSION["push"]==$folder.$value) { 
						$row["active"]=true;
					}
					$accordion->add($row);
				}
			} 
			echo $accordion->render();
		}
	}

	public function videopicker($config=[]) {
		if($_GET["target"]!="") { 
			$config["target"]=$_GET["target"]; 
		}

		$table1 = new table(); 
		$table1->setup([]);
		$table1->add(["cols"=>[$table1->addFileupload(["name"=>"file","attr"=>["target"=>".fileselector","url"=>"/cp/async/media/videopicker?target=".$config["target"]]])]]);
		echo $table1->render();   

		$config["type"]="videopicker";
		echo $this->filesearch($config);

		$folder = "../assets/media/";
		$root = "/assets/media/";
		$dir = scandir($folder);
		sort($dir);

		foreach($dir as $k=>$v) {
			if(in_array($v, config::mediaPriority)) {
				unset($dir[$k]);
			}
		}

		$dir = array_merge(config::mediaPriority,$dir); 

		if(isset($_GET["search"]) && $_GET["search"]!=""){
			$files = [];
			foreach($dir as $value) {
				$dir1 = scandir($folder.$value."/");
				foreach($dir1 as $key1=>$value1) {
					if(!is_file($folder.$value."/".$value1)) continue; 
					$filetype = explode(".",$value1);
					$filetype = strtolower($filetype[count($filetype)-1]);
					if($filetype!="mp4") continue;

					if($_GET["search"]!="") {
						if(!stristr($value1,$_GET["search"])) continue;
					}

					$files[filemtime($folder.$value."/".$value1)."_".$value1] = $root.$value."/".$value1;
				}
			}
			krsort($files);

			$table = new table();
			$table->setup(["class"=>["sortable"],"td"=>[0,100,80,110]]);
 
			foreach($files as $key => $value1) {
				$table->add(["cols"=>["<a href=\"$value1\" class=\"fileselector\" target=\"".$config["target"]."\">".basename($value1)."</a>","<div class=\"auto-preview\" href=\"..$value1\"></div>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize("..".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime("..".$value1))]],"controls"=>["/cp/async/media/imagepicker?target=".$config["target"]."&prmv=".urlencode($value1)=>["name"=>translation::get("rmv"),"target"=>".fileselector"]]]); 
			}
			echo $table->render();  
		}else{
			$accordion = new accordion(); 
			$accordion->setup(["attr"=>["single"=>true,"push"=>"/cp/async/media/set"]]); 

			foreach($dir as $key=>$value) {
				if(is_dir($folder.$value) && $value!="." && $value!="..") {
					$table = new table();
					$table->setup(["class"=>["sortable"],"td"=>[0,80,110]]);

					$files = [];
					$in = 0;
					$dir1 = scandir($folder.$value."/"); 
					foreach($dir1 as $key1=>$value1) {
						if(!is_file($folder.$value."/".$value1)) continue;
						$filetype = explode(".",$value1);
						$filetype = strtolower($filetype[count($filetype)-1]);
						if($filetype!="mp4") continue;

						$files[filemtime($folder.$value."/".$value1)."_".$value1] = $value1;
						$in = 1;
					}  

					krsort($files);
					foreach($files as $key1=>$value1) {  
						$table->add(["cols"=>["<a href=\"$root$value/$value1\" class=\"fileselector\" target=\"".$config["target"]."\">$value1</a>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize($folder.$value."/".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime($folder.$value."/".$value1))]],"controls"=>["/cp/async/media/videopicker?target=".$config["target"]."&prmv=".urlencode($folder.$value."/".$value1)=>["name"=>translation::get("rmv"),"target"=>".fileselector"]]]); 
					}

					$row = ["title"=>$value,"content"=>$table->render(),"attr"=>["pushid"=>"$folder$value"]];

					if($_SESSION["push"]==$folder.$value) { 
						$row["active"]=true;
					}
					$accordion->add($row);
				}
			}
			echo $accordion->render();
		}
	}

	public function filepicker($config=[]) {
		if($_GET["target"]!="") { 
			$config["target"]=$_GET["target"]; 
		}
		
		$table1 = new table(); 
		$table1->setup([]); 
		$table1->add(["cols"=>[$table1->addFileupload(["name"=>"file","attr"=>["target"=>".fileselector","url"=>"/cp/async/media/filepicker?target=".$config["target"]]])]]);
		echo $table1->render();  

		$config["type"]="filepicker";
		echo $this->filesearch($config);

		$folder = "../assets/media/";
		$root = "/assets/media/";
		$dir = scandir($folder);
		sort($dir);

		foreach($dir as $k=>$v) {
			if(in_array($v, config::mediaPriority)) {
				unset($dir[$k]);
			}
		}

		$dir = array_merge(config::mediaPriority,$dir); 

		if(isset($_GET["search"]) && $_GET["search"]!=""){
			$files = [];
			foreach($dir as $value) {
				$dir1 = scandir($folder.$value."/");
				foreach($dir1 as $key1=>$value1) {
					if(!is_file($folder.$value."/".$value1)) continue; 
					
					if($_GET["search"]!="") {
						if(!stristr($value1,$_GET["search"])) continue;
					}

					$files[filemtime($folder.$value."/".$value1)."_".$value1] = $root.$value."/".$value1;
				}
			}
			krsort($files);

			$table = new table();
			$table->setup(["class"=>["sortable"],"td"=>[0,100,80,110]]);
 
			foreach($files as $key => $value1) {
				$table->add(["cols"=>["<a href=\"$value1\" class=\"fileselector\" target=\"".$config["target"]."\">".basename($value1)."</a>","<div class=\"auto-preview\" href=\"..$value1\"></div>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize("..".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime("..".$value1))]],"controls"=>["/cp/async/media/imagepicker?target=".$config["target"]."&prmv=".urlencode($value1)=>["name"=>translation::get("rmv"),"target"=>".fileselector"]]]); 
			}
			echo $table->render();  
		} else {
			$accordion = new accordion(); 
			$accordion->setup(["attr"=>["single"=>true,"push"=>"/cp/async/media/set"]]); 

			foreach($dir as $key=>$value) {
				if(is_dir($folder.$value) && $value!="." && $value!="..") {
					$table = new table();
					$table->setup(["class"=>["sortable"],"td"=>[0,80,110]]);

					$files = [];
					$in = 0;
					$dir1 = scandir($folder.$value."/"); 
					foreach($dir1 as $key1=>$value1) {
						if(!is_file($folder.$value."/".$value1)) continue;
						$files[filemtime($folder.$value."/".$value1)."_".$value1] = $value1;
						$in = 1;
					}  

					krsort($files);
					foreach($files as $key1=>$value1) {  
						$table->add(["cols"=>["<a href=\"$root$value/$value1\" class=\"fileselector\" target=\"".$config["target"]."\">$value1</a>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize($folder.$value."/".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime($folder.$value."/".$value1))]],"controls"=>["/cp/async/media/filepicker?prmv=".urlencode($folder.$value."/".$value1)=>["name"=>translation::get("rmv"),"target"=>".fileselector"]]]); 
					} 

					$row = ["title"=>$value,"content"=>$table->render(),"attr"=>["pushid"=>"$folder$value"]];

					if($_SESSION["push"]==$folder.$value) { 
						$row["active"]=true;
					}
					$accordion->add($row);
				}
			}
			echo $accordion->render(); 
		}
	}

	public function view() {
		$header = new header();
		$header->addTitle(translation::get("media_title"));
		$header->addParagraph(translation::get("media_text"));
		echo $header->render();

		if($_GET["add"]!="") {
			$table = new table();
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/media/view"]]); 
			$table->addTitle(["cols"=>[translation::get("folder")]]);
			$table->add(["cols"=>[$table->addFormfield(["name"=>"folder","type"=>"text","attr"=>["drequired"=>true]])]]);
			$table->add(["cols"=>[$table->addFormfield(["name"=>"save","value"=>translation::get("save"),"type"=>"submit"])]]); 
			echo $table->render(); 
		} else {
			$table = new table();
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/media/view"]]);
			$table->add(["cols"=>[$table->addFileupload(["name"=>"file"])]]);
			echo $table->render();  

			$table = new table(); 
			$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/media/view"],"td"=>[0,120,80]]);
			$table->addTitle(["cols"=>[translation::get("folder")],"controls"=>["/cp/media?add=0"=>translation::get("add")]]);
			$table->add(["cols"=>[$table->addFormfield(["name"=>"find","type"=>"text","value"=>$_POST["find"]]),$table->addFormfield(["name"=>"type","type"=>"select","options"=>["0"=>translation::get("all"),"1"=>translation::get("images"),"2"=>translation::get("pdf"),"3"=>translation::get("movie"),"4"=>translation::get("excel")],"value"=>$_POST["type"]]),$table->addFormfield(["name"=>"search","type"=>"submit","value"=>translation::get("search")])]]); 
			echo $table->render(); 

			$folder = "../assets/media/";
			$dir = scandir($folder);
			sort($dir);

			foreach($dir as $k=>$v) {
				if(in_array($v, config::mediaPriority)) {
					unset($dir[$k]);
				}
			}

			$dir = array_merge(config::mediaPriority,$dir); 

			if($_POST["find"]!="" || ($_POST["type"]!="0" && $_POST["type"]!="")) {
				$files = [];
				foreach($dir as $value) {
					$dir1 = scandir($folder.$value."/");
					foreach($dir1 as $key1=>$value1) {
						if(!is_file($folder.$value."/".$value1)) continue; 
						if($_POST["find"]!="") {
							if(!stristr($value1,$_POST["find"])) continue;
						}

						if($_POST["type"]=="1") {
							$filetype = explode(".",$value1);
							$filetype = strtolower($filetype[count($filetype)-1]);
							if($filetype!="jpg" && $filetype!="jpeg" && $filetype!="png" && $filetype!="gif") continue;
						} else if($_POST["type"]=="2") {
							$filetype = explode(".",$value1);
							$filetype = strtolower($filetype[count($filetype)-1]);
							if($filetype!="pdf") continue;
						} else if($_POST["type"]=="3") {
							$filetype = explode(".",$value1);
							$filetype = strtolower($filetype[count($filetype)-1]);
							if($filetype!="mov" && $filetype!="avi" && $filetype!="mp4") continue; 
						} else if($_POST["type"]=="4") {
							$filetype = explode(".",$value1);
							$filetype = strtolower($filetype[count($filetype)-1]);
							if($filetype!="xls" && $filetype!="xlsx" && $filetype!="csv") continue; 
						}

						$files[filemtime($folder.$value."/".$value1)."_".$value1] = $folder.$value."/".$value1;
					}
				}
				krsort($files);

				$table = new table();
				$table->setup(["class"=>["sortable"],"td"=>[0,80,110]]);

				foreach($files as $value1) {
					$table->add(["cols"=>["<a href=\"$value1\" target=\"_BLANK\">".basename($value1)."</a>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize($value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime($value1))]],"controls"=>["/cp/async/media/view?prmv=".urlencode($folder.$value."/".$value1)=>translation::get("rmv")]]);
				}
				echo $table->render();  
			} else {
				$accordion = new accordion();
				$accordion->setup(["attr"=>["single"=>true,"push"=>"/cp/async/media/set"]]); 

				foreach($dir as $key=>$value) {
					if(is_dir($folder.$value) && $value!="." && $value!="..") {
						$table = new table();
						$table->setup(["class"=>["sortable"],"td"=>[0,100,80,110]]);

						$files = [];
						$in = 0;
						$dir1 = scandir($folder.$value."/"); 
						foreach($dir1 as $key1=>$value1) {
							if(!is_file($folder.$value."/".$value1)) continue;
							$files[filemtime($folder.$value."/".$value1)."_".$value1] = $value1;
							$in = 1;
						}  

						krsort($files);
						foreach($files as $key1=>$value1) { 
							if(stristr(strtolower($value1),".jpg") || stristr(strtolower($value1),".jpeg") || stristr(strtolower($value1),".png") || stristr(strtolower($value1),".gif") || stristr(strtolower($value1),".bmp")) {
								$table->add(["cols"=>["<a href=\"$folder$value/$value1\" target=\"_BLANK\">$value1</a>","<div class=\"auto-preview\" href=\"$folder$value/$value1\"></div>",["style"=>["text-align"=>"right"],"value"=>number_format(filesize($folder.$value."/".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime($folder.$value."/".$value1))]],"controls"=>["/cp/async/media/view?prmv=".urlencode($folder.$value."/".$value1)=>translation::get("rmv")]]);
							} else {
								$table->add(["cols"=>["<a href=\"$folder$value/$value1\" target=\"_BLANK\">$value1</a>","",["style"=>["text-align"=>"right"],"value"=>number_format(filesize($folder.$value."/".$value1)/1024/1024,2,".","'")."MB"],["style"=>["text-align"=>"right"],"value"=>date("d.m.Y H:i:s",filemtime($folder.$value."/".$value1))]],"controls"=>["/cp/async/media/view?prmv=".urlencode($folder.$value."/".$value1)=>translation::get("rmv")]]);
							}
						}

						$row = ["title"=>$value,"content"=>$table->render(),"attr"=>["pushid"=>"$folder$value"]];

						if($in==0) {
							$row["controls"] = ["/cp/async/media/view?rmvfolder=".$folder.$value=>translation::get("rmv")];
						}

						if($_SESSION["push"]==$folder.$value) { 
							$row["active"]=true;
						}
						$accordion->add($row);
					}
				}
				echo $accordion->render(); 
			}
		}
	} 
} 
?>  