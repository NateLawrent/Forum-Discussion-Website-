<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();
$db = new Database();

if (!$session->get('user') && !$session->get("isAdmin")) {
    header("Location: 403.php");
}

if (!isPost()) {
    header("Location: 403.php");
}

$data = getBody();

if(!isset($data)) {
    header("Location: 403.php");
}

$name = $data["name"];

if (!isset($name)) {
    header("Location: 404.php");
}

$tagSQL = "INSERT INTO tags (name) VALUES(:name)";
$statement = $db->prepare($tagSQL);
$statement->bindValue(":name", $data["name"]);
$statement->execute();
header("Location: /admin.php#tags");

?>