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
        ob_start();
        $start_time = microtime(true);
        echo "==============START============ " . date('Y-m-d H:i:s') . PHP_EOL;
        echo "Query: " . $sql . PHP_EOL;
        $query_result = pg_query($this->conn, $sql);

        $log_dir = $_SERVER['DOCUMENT_ROOT'] . '/log';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0775, true);
        }

        if (!$query_result) {
            $filepath = $log_dir . '/pgsql_error_' . date('Y-m-d') . '.log';
            echo "Error: " . pg_last_error($this->conn) . PHP_EOL;
        } else {
            $filepath = $log_dir . '/pgsql_query_' . date('Y-m-d') . '.log';
        }

        echo "==============END============== " . date('Y-m-d H:i:s') . PHP_EOL;
        $end_time = microtime(true);
        echo "Execution Time: " . round(($end_time - $start_time), 4) . " sec" . PHP_EOL;

        $result = ob_get_clean();
        file_put_contents($filepath, $result . PHP_EOL, FILE_APPEND);

        return $query_result;
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
