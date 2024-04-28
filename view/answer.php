<?php
require_once "common.php";
require_once "connection.php";

$session = new Session();
$db = new Database();

if (!$session->get('user')) {
    header("Location: 403.php");
}

$data = getBody();

if (!isPost() || !isset($data)) {
    header("Location: 403.php");
}

$userID = $session->get("user");
$question = $data["question"];

if (!isset($question) || !filter_var($question, FILTER_VALIDATE_INT)) {
    header("Location: 404.php");
}

$questionSQL = "SELECT * FROM question WHERE id = :id";
$statement = $db->prepare($questionSQL);
$statement->bindParam(":id", $question, PDO::PARAM_INT);
$statement->execute();
$record = $statement->fetchObject();

if (!$record) {
    header("Location: 404.php");
}

$sql = "INSERT INTO answer(answer, user, question, created) VALUES (:answer, :user, :question, :created)";
$now = new DateTime("now");
$now = $now->format('Y-m-d\TH:i:s');
$statement = $db->prepare($sql);
$statement->bindValue(":answer", $data["answer"]);
$statement->bindValue(":created", $now);
$statement->bindParam(":question", $record->id, PDO::PARAM_INT);
$statement->bindParam(":user", $userID, PDO::PARAM_INT);
$statement->execute();
header("Location: /examplequestion.php?id=$record->id");

?>