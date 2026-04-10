<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'demographydb';
    private $username = 'root';
    private $password = '';
    private $link;

    function __construct() {
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);
        if (!$this->link) {
            exit("Bazaga ulanmadi!");
        }
        mysqli_set_charset($this->link, 'utf8mb4');
    }

    public function query($query) {
        return mysqli_query($this->link, $query);
    }

    public function get_data_by_table($table, $arr, $con = 'no') {
        $sql = "SELECT * FROM " . $table . " WHERE ";
        $t = '';
        $i = 0;
        $n = count($arr);
        foreach ($arr as $key => $val) {
            $i++;
            if ($i == $n) {
                $t .= "$key = '$val'";
            } else {
                $t .= "$key = '$val' AND ";
            }
        }
        $sql .= $t;
        if ($con != 'no') {
            $sql .= $con;
        }
        return mysqli_fetch_assoc($this->query($sql));
    }

    public function get_data_by_table_all($table, $con = 'no') {
        $sql = "SELECT * FROM " . $table;
        if ($con != 'no') {
            $sql .= " " . $con;
        }
        $result = $this->query($sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function insert($table, $arr) {
        $sql = "INSERT INTO " . $table . " ";
        $t1 = '';
        $t2 = '';
        $i = 0;
        $n = count($arr);

        foreach ($arr as $key => $val) {
            $val = mysqli_real_escape_string($this->link, $val);
            $i++;
            if ($i == $n) {
                $t1 .= $key;
                $t2 .= "'" . $val . "'";
            } else {
                $t1 .= $key . ', ';
                $t2 .= "'" . $val . "', ";
            }
        }

        $sql .= "($t1) VALUES ($t2)";

        if ($this->query($sql)) {
            return mysqli_insert_id($this->link);
        }

        return false;
    }

    public function update($table, $arr, $con = 'no') {
        $sql = "UPDATE " . $table . " SET ";
        $t = '';
        $i = 0;
        $n = count($arr);

        foreach ($arr as $key => $val) {
            $val = mysqli_real_escape_string($this->link, $val);
            $i++;
            if ($i == $n) {
                $t .= "$key = '$val'";
            } else {
                $t .= "$key = '$val', ";
            }
        }

        $sql .= $t;

        if ($con != 'no') {
            $sql .= " WHERE " . $con;
        }

        return $this->query($sql);
    }

    public function delete($table, $con = 'no') {
        $sql = "DELETE FROM " . $table;
        if ($con != 'no') {
            $sql .= " WHERE " . $con;
        }
        return $this->query($sql);
    }

    public function escape($val) {
        return mysqli_real_escape_string($this->link, $val);
    }

    public function get_link() {
        return $this->link;
    }
}

// App settings
define('SITE_NAME', 'Demografiya Tizimi');
define('BASE_URL', '/demography-system');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
