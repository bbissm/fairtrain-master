<?php
class kursleitung extends module {
    public static $auth = true;
    public static $authLevel = [1,2,3];

    public function view()
    {
        $header = new header();
        $header->addTitle(translation::get("kursleitung"));
        $header->addParagraph(translation::get("kursleitung_text"));
        echo $header->render();
        if($_GET["edit"] || $_GET["add"]){
            $this->editKursleitung();
        }else{
            $this->showKursleitung();
        }
    }

    public function editKursleitung()
    {
        $title = [
            translation::get("name"),
            translation::get("image"),
            translation::get("phone"),
            translation::get("email")
        ];

        $fields = [
            "name",
            "image",
            "phone",
            "email",
            "extern"
        ];

        $table = new table();
        $table->setup([
            "form"=>[
                "method"=>"post",
                "action"=>"/cp/async/kursleitung/view",
                "sqltable"=>"tbl_leitung",
                "sqlwhere"=>[
                    "leitung_id"=>$_GET["edit"]
                    ]
                ],
            "td"=>[120]
            ]);
        $table->controller();
        $table->addTitle(["cols"=>[translation::get("Kursleitung bearbeiten")]]);
        
        $table->add(["cols"=>[$title[0], $table->addFormField(["name"=>$fields[0],"type"=>"text","attr"=>["drequired"=>true]])]]);
        $table->add(["cols"=>[translation::get("image_src"),$table->addFormField(["name"=>$fields[1],"type"=>"text","attr"=>["drequired"=>true]])]]);
		// $table->add(["cols"=>[translation::get("image_alt"),$table->addFormField(["name"=>"image_alt","type"=>"text"])]]);
        $table->add(["cols"=>[$title[1], $table->addImageSelect(["target"=>$fields[1],"attr"=>["targetWidth"=>1024,"targetHeight"=>1024]])]]);
        $table->add(["cols"=>[$title[2], $table->addFormField(["name"=>$fields[2],"type"=>"text"])]]);
        $table->add(["cols"=>[$title[3], $table->addFormField(["name"=>$fields[3],"type"=>"text"])]]);
        $table->add(["cols"=>["Beschrieb für Seminar", $table->addFormField(["name"=>"text","type"=>"tinymce"])]]);
        $table->add(["cols"=>["Nicht im Team",$table->addFormField(["name"=>$fields[4],"type"=>"checkbox","set"=>1])]]);
        $table->add(["cols"=>[$table->addFormField(["name"=>"save","type"=>"submit","value"=>translation::get("save")])]]);
        echo $table->render();
        
    }

    public function showKursleitung()
    {
        if($_GET["prmv"] != ""){
            $delete = "DELETE FROM tbl_leitung WHERE leitung_id=".$_GET["prmv"];
            $this->db->query($delete);
        }
        
        $fields = [
            "name",
            "image_alt",
            "phone",
            "email"
        ];

        $table = new table();
        $table->setup([
            "form"=>[
                "sql_table"=>"tbl_leitung"
            ],
            "td"=>[0,120,240,0]
        ]);
        $table->addTitle(["cols"=>[translation::get("kursleiter")]]);
        $table->addSubtitle(["cols"=>[translation::get("name"),translation::get("image"),translation::get("phone"),translation::get("email")],"controls"=>["/cp/kursleitung?add=1"=>translation::get("add")]]);
        // $table->auto(["id"=>"leitung_id","select"=>implode(",",$fields),"from"=>"tbl_leitung","controls"=>["/cp/kursleitung?edit={id}"=>translation::get("edit"),"/cp/async/kursleitung/view?prmv={id}"=>translation::get("rmv")]]); 
        $q = $this->db->query("SELECT leitung_id, name,image,phone,email FROM tbl_leitung");
        while($res = $q->fetch_assoc()){
            $table->add(["cols"=>[$res["name"],"<image src=\"".$res["image"]."\" width=\"200px\"/>",$res["phone"],$res["email"]],"controls"=>["/cp/kursleitung?edit=".$res["leitung_id"]=>translation::get("edit"),"/cp/async/kursleitung/view?prmv=".$res["leitung_id"]=>translation::get("rmv")]]);
        }
        
        echo $table->render();
        
        
    }
}

?>
