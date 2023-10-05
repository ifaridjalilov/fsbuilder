<?php

namespace FS;

class QueryBuilder
{
    public string $from;
    public array $columns = [];
    public array $wheres = [];
    public array $orders = [];

    public array $operators = ['=', '!=', '<', '>', '<=', '>='];

    public function __construct(private readonly SqlAdapter $adapter)
    {}

    public function query(): QueryBuilder
    {
        return new static($this->adapter);
    }

    public function select($columns)
    {
        if (is_array($columns)) {
            $this->columns = $columns;
        } else {
            $this->columns = func_get_args();
        }

        return $this;
    }

    public function table($name, $alias = null)
    {
        $this->from = $alias ? "{$name} as {$alias}" : $name;

        return $this;
    }

    public function where($column, $operator = null, $parameter = null, $combineOperator = 'and')
    {
        if (func_num_args() === 2) {
            if (is_string($column) && is_null($operator)) {
                $this->wheres[] = [
                    'type' => 'null',
                    'column' => $column,
                    'combine' => $combineOperator
                ];

                return $this;
            }

            $parameter = $operator;
            $operator = '=';
        }

        if ($column instanceof \Closure) {
            $subQueryObj = $column(new static($this->adapter));
            $this->wheres[] = [
                'type' => 'sub',
                'builder' => $subQueryObj,
                'combine' => $combineOperator
            ];
            return $this;
        }

        if (!in_array($operator, $this->operators)) {
            throw new \InvalidArgumentException('Invalid operator');
        }

        $this->wheres[] = [
            'type' => 'root',
            'column' => $column,
            'operator' => $operator,
            'parameter' => $parameter,
            'combine' => $combineOperator
        ];

        return $this;
    }

    public function orWhere($column, $operator = null, $parameter = null)
    {
        return $this->where($column, $operator, $parameter, 'or');
    }

    public function orderBy($column, $direction = 'asc')
    {
        $direction = strtolower($direction);
        if (!in_array($direction, ['asc', 'desc'])) {
            throw new \InvalidArgumentException('Invalid direction');
        }

        $this->orders[] = ['column' => $column, 'direction' => $direction];
        return $this;
    }

    public function toSql(): string
    {
        return $this->adapter->build($this);
    }
}