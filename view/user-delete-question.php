<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();
$db = new Database();

if (!$session->get('user')) {
    header("Location: 403.php");
}

if (!isGet()) {
    header("Location: 403.php");
}

$data = getBody();

if(!isset($data)) {
    header("Location: 403.php");
}

if ($data["user"] != $session->get("user") || !isset($data["user"])) {
    header("Location: 403.php");
}

$questionID = $data["id"];

if (!isset($questionID) || !filter_var($questionID, FILTER_VALIDATE_INT)) {
    header("Location: 404.php");
}

$questionSQL = "UPDATE question SET isActive = :isActive WHERE id = :id";
$statement = $db->prepare($questionSQL);
$statement->bindParam(":id", $questionID, PDO::PARAM_INT);
$isActive = 0;
$statement->bindParam(":isActive", $isActive, PDO::PARAM_INT);
$statement->execute();
header("Location: /questions.php");
?>