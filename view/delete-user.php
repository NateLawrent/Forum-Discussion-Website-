<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();
$db = new Database();

if (!$session->get('user') && !$session->get("isAdmin")) {
    header("Location: 403.php");
}

if (!isGet()) {
    header("Location: 403.php");
}

$data = getBody();

if(!isset($data)) {
    header("Location: 403.php");
}

$userID = $data["id"];

if (!isset($userID) || !filter_var($userID, FILTER_VALIDATE_INT)) {
    header("Location: 404.php");
}

$userSQL = "DELETE FROM user WHERE id = :id";
$statement = $db->prepare($userSQL);
$statement->bindParam(":id", $userID, PDO::PARAM_INT);
$statement->execute();
header("Location: /admin.php#users");
?>