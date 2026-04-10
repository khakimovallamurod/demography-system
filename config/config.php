<?php
function load_env_file($path) {
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        if (($value[0] ?? '') === '"' && substr($value, -1) === '"') {
            $value = substr($value, 1, -1);
        } elseif (($value[0] ?? '') === "'" && substr($value, -1) === "'") {
            $value = substr($value, 1, -1);
        }

        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        putenv($name . '=' . $value);
    }
}

function env($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($value === false || $value === null || $value === '') ? $default : $value;
}

load_env_file(__DIR__ . '/../.env');

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
define('SITE_LOGO', BASE_URL . '/assets/images/logo.png');
