<?php
class Database
{
    public \PDO $pdo;

    public function __construct($dbConfig = [])
    {
        $dbDsn = 'mysql:host=localhost;port=3306;dbname=huyforum;charset=UTF8';
        $username = 'root';
        $password = '';

        try {
			$this->pdo = new \PDO($dbDsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
		} catch (\PDOException $e) {
			echo "Connected to the database failed!<br>";
			die($e->getMessage());
		}
    }

    public function prepare($sql): \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    private function log($message)
    {
        echo "[" . date("Y-m-d H:i:s") . "] - " . $message . PHP_EOL;
    }

    public function getInsertedId(): int
    {
        return $this->pdo->lastInsertId();
    }
}
?>