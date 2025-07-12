<?php
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $port;
    public $conn;

    public function __construct($host, $user, $pass, $dbname, $port = 5432) {
        $this->host   = $host;
        $this->user   = $user;
        $this->pass   = $pass;
        $this->dbname = $dbname;
        $this->port   = $port;

        $this->connect();
    }

    private function connect() {
        $conn_string = "host={$this->host} port={$this->port} dbname={$this->dbname} user={$this->user} password={$this->pass} sslmode=require";
        $this->conn = pg_connect($conn_string);

        if (!$this->conn) {
            die("PostgreSQL connection failed: " . pg_last_error());
        }
    }

    public function query($sql) {
        return pg_query($this->conn, $sql);
    }

    public function query_get_id($sql) {
        $result = $this->query($sql . " RETURNING id");
        if ($result) {
            $row = pg_fetch_assoc($result);
            return $row['id'] ?? null;
        }
        return null;
    }

    public function row($sql) {
        $result = $this->query($sql);
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }
        return null;
    }

    public function field($sql) {
        $result = $this->query($sql);
        if ($result && $row = pg_fetch_row($result)) {
            return $row[0];
        }
        return null;
    }

    public function close() {
        pg_close($this->conn);
    }
}
?>
