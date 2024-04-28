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
$firstname = $data["firstname"];
$lastname = $data["lastname"];
$email = $data["email"];
$username = $data["username"];

if (!isset($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: 404.php");
}

$userSQL = "UPDATE user SET firstname = :firstname, lastname = :lastname, email = :email, username = :username WHERE id = :id";
$statement = $db->prepare($userSQL);
$statement->bindParam(":id", $id, PDO::PARAM_INT);
$statement->bindValue(":firstname", $data["firstname"]);
$statement->bindValue(":lastname", $data["lastname"]);
$statement->bindValue(":email", $data["email"]);
$statement->bindValue(":username", $data["username"]);
$statement->execute();
header("Location: /admin.php#users");

?>