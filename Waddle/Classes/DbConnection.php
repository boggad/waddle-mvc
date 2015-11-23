<?php

namespace Waddle\classes;


interface DbConnection {
    // TODO: Needs to add lazy init for DdProvider!
    function __construct($host, $user, $password, $name = false);
    function __destruct();

    public function select(array $fields, array $tables, $whereClause, $post = null);
    public function selectAll(array $fields, array $tables, $post = null);
    public function update(array $fields, $table, array $filter);
    public function insert($table, array $fields, array $values);
    public function delete($table, array $filter);
    public function query($sql);
    public function escape($str);
    public function createDatabase($name);
}