<?php
$class = "";
$obj = new html();
$obj->setup();
$data = $obj->get(["container"=>$value["cms_container_id"]]);
if($data["html"]=="" || $data["html"]=="undefined") {
	$data["html"]="<p>Testinhalt</p>";   
}

if(!$_SESSION["login"]) {
    $data["html"] = str_replace("{coursename}",$obj->getCourseName($_GET["kurs"]),$data["html"]);
}
if($_SESSION["path"][1] == "kontakt" || $_SESSION["path"][1] == "contact") {
    $style1 = "max-width:unset;width: 100%;
    margin: 0 auto;";
}

if($data["background"]==0) {
    $class = "white";
}elseif($data["background"]==1) {
    $class = "grey";
    $style = "padding-bottom:75px;margin-top:75px;max-width:unset;
	margin:0 auto;";
}elseif($data["background"]==2) {
    $class = "beige";
    $style = "padding-bottom:75px;margin-top:75px;max-width:unset;
	margin:0 auto;";

}
?>
<section style="<?php echo $style;?>" class="<?php echo $class; ?>" <?php $this->attr(); if($_SESSION["login"]) { ?> action="/cp/async/html/update?id=<?php echo $value["cms_container_id"]."\""; } ?>>
    <?php $this->controls([
        "/cp/async/html/settings?id=".$value["cms_container_id"]=>translation::get("settings"),
        "/cp/async/container/rmv?id=".$value["cms_container_id"]=>["value"=>translation::get("rmv"),"target"=>"destruct"]
    ]);

    srand(time());
    $rnd = rand(0,2);
    ?>
    <div>
        <article style='<?php echo $style1;?>' class="wysiwyg centered <?php echo $class ?>">
            <?php echo $data["html"]; ?>
        </article>  
        <br class="adsbr" clear="both" />
    </div>   
</section>