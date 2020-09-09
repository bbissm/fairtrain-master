<?php
spl_autoload_register(function ($class_name) {
    includeRecursive("library/",$class_name);
});

function includeRecursive($root,&$class_name) { 
	$dir = scandir($root);
	foreach ($dir as $k => $v) {
		if($v=="." || $v=="..") continue;
		if(is_dir($root.$v."/")) {
			includeRecursive($root.$v."/",$class_name);
		} else {
			if($v==$class_name.".php") {
		    	require_once($root.$v);
		    	return;
		    }
		}
	}
}
?>