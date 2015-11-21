<?php

namespace Waddle\Classes;


class MysqlConnection implements DbConnection {
    /**
     * @var \mysqli
     */
    private $conn;

    function __construct($host, $user, $password, $name = false) {
        if ($name) {
            $this->conn = new \mysqli($host, $user, $password, $name);
            $this->conn->set_charset('utf8');
        } else {
            $this->conn = new \mysqli($host, $user, $password);
        }
    }

    function __destruct() {

    }

    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    /**
     * @param array $fields
     * @param array $tables
     * @param $whereClause
     * @param null $post
     * @return array
     */
    public function select(array $fields, array $tables, $whereClause, $post = null) {
        $query = 'SELECT ';
        $query .= join(',', $fields) . ' ';
        $query .= 'FROM ' . join(',', $tables);
        if ($whereClause !== null) {
            $where = preg_replace('/\:([\w]+)/', '`$1`', $whereClause);
            $query .= ' WHERE ' . $where;
        }
        if ($post) {
            $query .= ' ' . preg_replace('/\:([\w]+)/', '`$1`', $post);
        }
        $query .= ';';
        //var_dump($query);
        return $this->conn->query($query)->fetch_all(MYSQL_ASSOC);
    }

    /**
     * @param array $fields
     * @param array $tables
     * @param null $post
     * @return array
     */
    public function selectAll(array $fields, array $tables, $post = null) {
        return $this->select($fields, $tables, null, $post);
    }

    /**
     * @param array $fields
     * @param $table
     * @param array $filter
     * @throws \Exception
     * @return int
     */
    public function update(array $fields, $table, array $filter) {
        if (!isset($fields) or (count($fields) == 0)) return 0;
        $query = 'UPDATE `' . $table . '` SET ';
        $sets = array();
        foreach ($fields as $field => $value) {
            $sets[] = '`' . $field . '`=' . $value;
        }
        $query .= join(',', $sets);
        if ($filter !== null) {
            $query .= ' WHERE `' . key($filter) . '`=' . current($filter);
        }
        $query .= ';';
        //var_dump($query);
        if (!$this->conn->query($query)) {
            throw new \Exception('Mysql Error (' . $this->conn->errno . ') ' . $this->conn->error);
        }
        return $this->conn->affected_rows;
    }

    /**
     * @param $table
     * @param array $fields
     * @param array $values
     * @throws \Exception
     * @return int
     */
    public function insert($table, array $fields, array $values) {
        if (!isset($fields) or (count($fields) == 0) or (count($values) == 0)) return 0;
        $query = 'INSERT' . ' INTO `' . $table .'` ';
        array_walk($fields, function(&$value) {
            $value = '`' . $value . '`';
        });
        $query .= '(' . join(',', $fields) . ') VALUES ';
        $vals = array();
        foreach($values as $valArray) {
            $vals[] = '(' . join(',', $valArray) . ')';
        }

        $query .= join(',', $vals) . ';';
        //var_dump($query);
        if (!$this->conn->query($query)) {
            throw new \Exception('Mysql Error (' . $this->conn->errno . ') ' . $this->conn->error);
        }
        return $this->conn->affected_rows;
    }

    /**
     * @param $table
     * @param array $filter
     * @throws \Exception
     * @return int
     */
    public function delete($table, array $filter) {
        $query = 'DELETE' . ' FROM `' . $table . '`';
        if ($filter !== null and count($filter) > 0) {
            $query .= ' WHERE `' . key($filter) . '`=' . current($filter);
        }
        $query .= ';';
        if (!$this->conn->query($query)) {
            throw new \Exception('Mysql Error (' . $this->conn->errno . ') ' . $this->conn->error);
        }
        return $this->conn->affected_rows;
    }

    public function query($sql) {
        $sql = preg_replace('/\:([\w]+)/', '`$1`', $sql);
        return $this->conn->query($sql)->fetch_all(MYSQL_ASSOC);
    }

    public function createDatabase($name) {
        $sql = 'CREATE DATABASE IF NOT EXISTS `' . $name . '`;';
        $r = $this->conn->query($sql);
        if ($r === true) {
            return $r;
        } else {
            return 'Mysql Error['. $this->conn->errno . '] ' . $this->conn->error;
        }
    }
}