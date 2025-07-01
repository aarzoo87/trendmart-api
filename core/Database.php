<?php
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    public $conn;

    public function __construct($host, $user, $pass, $dbname) {
        $this->host   = $host;
        $this->user   = $user;
        $this->pass   = $pass;
        $this->dbname = $dbname;

        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }

        $this->conn->set_charset('utf8');
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function query_get_id($query) {
        $this->conn->query($query);
        return $this->conn->insert_id;
    }

    public function row($sql) {
        $result = $this->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function field($query)
    {
        $result = mysqli_query($this->conn, $query);
        if ($result && $row = mysqli_fetch_row($result)) {
            return $row[0];
        }
        return null;
    }

    public function close() {
        $this->conn->close();
    }
}
?>
