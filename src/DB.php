<?php

namespace FS;

/**
 * @method static QueryBuilder query()
 * @method static QueryBuilder select($columns)
 * @method static QueryBuilder table($name, $alias = null)
 * @method static QueryBuilder where($column, $operator = null, $parameter = null, $combineOperator = 'and')
 * @method static QueryBuilder orWhere($column, $operator = null, $parameter = null)
 * @method static QueryBuilder orderBy($column, $direction = 'asc')
 * @method static string toSql()
 */
class DB
{
    public static function __callStatic(string $name, array $arguments)
    {
        $dm = new DatabaseManager();

        return $dm->builder->$name(...$arguments);
    }
}