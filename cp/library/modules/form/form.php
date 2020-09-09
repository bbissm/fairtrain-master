<?php
class form extends module {
	public static $auth = true;
	public static $authLevel = [1,2,3,4];
	
	public function setup() {
		
	}

	public function export() {
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=export.xls"); 
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);

		echo "<html>";
		echo "<head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />";
		echo "</head>";
		echo "<body>";

		$table = new table();
		$table->setup([]);

		$query = "SELECT name, form_field_id FROM tbl_form_field WHERE form_fk = '".$_GET["entries"]."' AND field_type <> 'fill' ORDER BY sort ASC";
		$q = $this->db->query($query);
		$cols = [];
		while($res = $q->fetch_assoc()){
			if($res["name"] == "Fill"){
				continue;
			}
			$cols[$res["form_field_id"]] = $res["name"];
		}

		array_push($cols, "Timestamp");

		$table->addSubtitle(["cols"=>$cols]);


		$query = "SELECT form_submit_id,DATE_FORMAT(timestamp,'%d.%m.%Y %H:%i:%s') as ts FROM tbl_form_submit WHERE form_fk = '".$_GET["entries"]."' ORDER BY timestamp DESC";
		$q = $this->db->query($query);
		while($res = $q->fetch_assoc()){
			$cols2 = [];
			foreach ($cols as $field_id=>$v) {
				$query2 = "SELECT value, form_field_fk FROM tbl_form_submit_value WHERE form_field_fk = '".$field_id."' AND form_submit_fk = '".$res["form_submit_id"]."'";
				$q2 = $this->db->query($query2);
				while($res2 = $q2->fetch_assoc()){
					$res2["value"] = str_replace("+","&plus;",$res2["value"]);

					$cols2[] = $res2["value"];
				}
			}

			array_push($cols2, $res["ts"]);

			$table->add(["cols"=>$cols2]);
		}

		echo $table->render();

		echo "</body>";
		echo "</html>";
	}

	public function controller() {
		if($_GET["clear"]==1) {
			$this->db->query("DELETE FROM tbl_form_submit WHERE form_fk = '".$_GET["entries"]."'");
		}

		if(isset($_GET["copy"])){
			$q=$this->db->query("SELECT * FROM tbl_form WHERE form_id=".$_GET["copy"]);
			if($q->num_rows>0){
				$res=$q->fetch_assoc();
				$this->db->query("INSERT INTO tbl_form (form_title, form_desc, title, email, bgcolor ,mailchimp_list,class) VALUES ('".$res["form_title"]."','".$res["form_desc"]."','".$res["title"]."','".$res["email"]."','".$res["bgcolor"]."','".$res["mailchimp_list"]."','".$res["class"]."')");
				$id=$this->db->insert_id;

				$q=$this->db->query("SELECT * FROM  tbl_form_field WHERE form_fk=".$_GET["copy"]);
				if($q->num_rows>0){
					while($res=$q->fetch_assoc()){
						$this->db->query("INSERT INTO tbl_form_field (form_fk, name, field_name, field_type, is_required, default_value, mailchimp_name, sort, width) VALUES ('".$id."', '".$res["name"]."', '".$res["field_name"]."', '".$res["field_type"]."', '".$res["is_required"]."', '".$res["default_value"]."', '".$res["mailchimp_name"]."', '".$res["sort"]."', '".$res["width"]."')");
					}
				}
			}
			?>
			<script type="text/javascript">location.href="/cp/form/?edit=<?php echo $id; ?>";</script>
			<?php
		}
	}

	public function view()
	{
		$header = new header();
		$header->addTitle(translation::get("form_plugin_title"));
		$header->addParagraph(translation::get("form_plugin_text")); 
		echo $header->render();

		$this->showForm();

	}

	public function showForm()
	{
		$table = new table();
		$table->setup(["form"=>["sqltable"=>"tbl_form"],"td"=>[0,0]]);
		$table->controller(); 
		$table->addTitle(["cols"=>["Formulare"], "controls"=>["/cp/form?add=1"=>"Hinzufügen"]]); 
		$table->addSubtitle(["cols"=>["Bezeichnung", "E-Mail"]]); 
		$forms["1"] = "Kontaktformular";
		$forms["2"] = "Anmeldeformular Beratung";
		

		$table->add(["cols"=>[$forms["1"]]]);
		$table->add(["cols"=>[$forms["2"]]]);
		echo $table->render();
	}

	public function settings() {
		$header = new header();
		$header->addTitle("Formular");
		$header->addParagraph("Wählen Sie ein Formular aus welches im Content dargestellt werden soll"); 
		echo $header->render();
 
		$table = new table();
		$table->setup(["form"=>["method"=>"post","action"=>"/cp/async/form/settings","sqltable"=>"tbl_form_hardcoded","sqlwhere"=>["cms_container_fk"=>$config["id"]],"attr"=>["target"=>"#popup_content"]],"td"=>[120]]);
		$table->controller(); 
		$table->addTitle(["cols"=> ["Formular"]]);

		$forms = [];
		$forms["1"] = "Kontaktformular";
		$forms["2"] = "Anmeldeformular Beratung";
		

		$table->add(["cols"=>["Formular", $table->addFormField(["name"=>"selected","type"=>"select", "options"=>$forms])]]);
		$table->add(["cols"=>[$table->addFormField(["name"=>"cms_container_fk","type"=>"hidden","value"=>$_GET["id"]]),$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
		echo $table->render();   
	}
}
?>