<?php

namespace FS;

class DatabaseManager
{
    public QueryBuilder $builder;
    public SqlAdapter $adapter;

    public function __construct()
    {
        $this->adapter = new MySqlAdapter();
        $this->builder = new QueryBuilder($this->adapter);
    }
}