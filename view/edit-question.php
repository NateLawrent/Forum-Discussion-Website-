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

$id = $data["id"];


if (!isset($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: 404.php");
}

$userSQL = "UPDATE question SET thread = :thread, message = :message, tag = :tag WHERE id = :id";
$statement = $db->prepare($userSQL);
$statement->bindParam(":id", $id, PDO::PARAM_INT);
$statement->bindValue(":thread", $data["thread"]);
$statement->bindValue(":message", $data["message"]);
$statement->bindValue(":tag", $data["tag"]);
$statement->execute();
header("Location: /admin.php#users");

?>