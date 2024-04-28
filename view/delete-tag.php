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

$tagID = $data["id"];

if (!isset($tagID) || !filter_var($tagID, FILTER_VALIDATE_INT)) {
    header("Location: 404.php");
}

$tagSQL = "DELETE FROM tags WHERE id = :id";
$statement = $db->prepare($tagSQL);
$statement->bindParam(":id", $tagID, PDO::PARAM_INT);
$statement->execute();
header("Location: /admin.php#tags");
?>