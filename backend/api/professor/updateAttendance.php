<?php
header("Content-Type: application/json");
session_start();
require_once "../../config/database.php";

$db=new Database();
$conn=$db->connect();

$conn->prepare("DELETE FROM attendance WHERE session_id=:s")
     ->execute([":s"=>$_POST["session_id"]]);

$present=json_decode($_POST["present"],true);

foreach($present as $sid){
    $conn->prepare("
        INSERT INTO attendance(session_id,student_id,status)
        VALUES(:s,:u,'present')
    ")->execute([":s"=>$_POST["session_id"], ":u"=>$sid]);
}

echo json_encode(["success"=>true,"message"=>"Présence enregistrée"]);
