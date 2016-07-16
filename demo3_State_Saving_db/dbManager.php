<?php

class DbManager
{
    private $mysqli;
    private $table_name;

    public function __construct()
    {
        $this->db = new PDO('mysql:host=localhost;dbname=datatable', "root", "root");
        $this->table_name = "datatables_states";
    }

    public function saveState($name, $state)
    {
        try {
            //if the name already exist then update else insert a new row in db
            $query = "INSERT INTO " . $this->table_name . "( `name`, `state`) VALUES ( :name , :state)";
            $query.= " ON DUPLICATE KEY UPDATE state=VALUES(state)";//to avoid multiple inserts, we update the column "state" if a row with the same name  exists
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':state', json_encode($state));
            $stmt->execute();
            echo "saved";
        } catch (PDOException $e) {
            print "ERROR !: " . $e->getMessage() . "<br/>";
            die();
        }
    }


    public function loadState($name)
    {
        try {

            $stmt = $this->db->prepare("SELECT state FROM `datatables_states` WHERE `name` LIKE :name");

            $stmt->execute(array(":name" => $name));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $result['state'] ;
        } catch (PDOException $e) {
            print "ERROR !: " . $e->getMessage() . "<br/>";
            die();
        }
    }

}


if(isset($_GET['action'])){
    //start saving or loading
    $dbManager = new DbManager();
    switch( $_GET['action']){
        case "save": if(isset($_POST['state'] )) $dbManager->saveState($_POST["name"],$_POST["state"]);break;
        case "load": $dbManager->loadState($_POST["name"]);break;
    }
}
