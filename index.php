<?php

require_once('src/SqlAdapter.php');
require_once('src/MySqlAdapter.php');
require_once('src/QueryBuilder.php');
require_once('src/DatabaseManager.php');
require_once('src/DB.php');

use FS\DB;

$querySql = DB::table('users')
    ->where('gender', 'm')
    ->select('id', 'name', 'age')
    ->orderBy('age')
    ->toSql();

var_dump($querySql);

$querySql = DB::table('posts')
    ->where('is_hidden', 1)
    ->orWhere(function ($query) {
        return $query->where('author', null)->where('read_count', '<', 100);
    })
    ->toSql();

var_dump($querySql);