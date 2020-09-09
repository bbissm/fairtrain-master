<section>

<?php
    $teamdetails = new teamdetails();
    $mitglied = $teamdetails->getTeamdetails();

?>
	<div class="container kontakt_bio">
        <?php
            while($res = $mitglied->fetch_assoc()){
                echo "<div class=\"kontakt_article\">";
                echo "<img src=\"".$res["image"]."\">";
                echo "<div class=\"article\">";
                echo "<h2>".$res["name"]."</h2>";
                echo "<p class=\"text_center\">".$res["phone"]."</p>";
                echo "<a href=\"mailto:".$res["email"]."\" class=\"text_center\">".$res["email"]."</a>";
                echo "</div>";
                echo " </div>";
            }
		?>
	</div>
</section>
<?php

    $obj = new teamdetails();
    $obj->setup();
    $data = $obj->get();


    $q = $this->db->query("SELECT cms_lang_id FROM cms_lang");
   
?>
<div class="grey kurs_details">
<section class=" " <?php if($_SESSION["login"]){echo "action='/cp/async/html/update?id=".$value["cms_container_id"]."'";} ?>>
	<div class="container centered wysiwyg" action="/cp/async/teamdetails/update?lang_fk=<?php echo $_SESSION["lang"]["key"]; ?>&type=1&id=<?php echo $_GET['mitglied']; ?>">
        <?php
            foreach($data as $k){
                if($k["type"] == 1){
                    echo $k["text"];
                }
            }
        // das musst du jetzt über eine methode aus der teamdetail tabelle holen
        ?>
	</div>
</section>
</div>
<div class="beige kurs_details">
<section class=" " <?php if($_SESSION["login"]){echo "action='/cp/async/html/update?id=".$value["cms_container_id"]."'";} ?>>
	<div class="container centered wysiwyg" action="/cp/async/teamdetails/update?lang_fk=<?php echo $_SESSION["lang"]["key"]; ?>&type=2&id=<?php echo $_GET['mitglied']; ?>">
		<?php
       	    foreach($data as $k){
                if($k["type"] == 2){
                    echo $k["text"];
                }
            }
        ?>
	</div>
</section>
</div>
<section class="button-container">
	<div class="centered_btn">
		<a class="button" href="javascript:history.go(-1)"><?php echo translation::get("back");?></a>
	</div>
	
</section>


