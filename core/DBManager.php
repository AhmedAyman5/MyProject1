<?php
//Conection to database 
class DBManager
{
    private PDO $pdo;

    public function __construct(private $host, private $dbname, private $username, private $password)
    {
        $this->getConnecetion();
    }

    public function getConnecetion(): PDO
    {
        if (empty($this->pdo)) {
            $this->pdo = new PDO($this->getDSN(), $this->username, $this->password);
        }
        return $this->pdo;
    }

    public function query($sql, ...$params)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    private function getDSN(): string
    {
        return sprintf("mysql:host=%s;dbname=%s", $this->host, $this->dbname);
    }

}
