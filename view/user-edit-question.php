<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();
$db = new Database();

if (!$session->get('user')) {
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

if ($data["user"] != $session->get("user") || !isset($data["user"])) {
    header("Location: 403.php");
}

$image_path = upload_question_image();

$update = $image_path ? "UPDATE question SET thread = :thread, message = :message, tag = :tag, image = :image" : "UPDATE question SET thread = :thread, message = :message, tag = :tag";

$questionSQL = $update." WHERE id = :id";
$statement = $db->prepare($questionSQL);
$statement->bindParam(":id", $id, PDO::PARAM_INT);
$statement->bindValue(":thread", $data["thread"]);
$statement->bindValue(":message", $data["message"]);
$statement->bindParam(":tag", $data["tag"], PDO::PARAM_INT);
if ($image_path) {
    $statement->bindValue(":image", $image_path);
}
$statement->execute();
header("Location: /examplequestion.php?id=$id");

?>