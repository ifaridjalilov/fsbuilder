<?php

namespace FS;

class MySqlAdapter implements SqlAdapter
{
    public array $bindings = [];

    public function build(QueryBuilder $builder): string
    {
        if ( count($builder->columns) === 0 ) {
            $builder->select(['*']);
        }

        $selectQuery = 'select ' . implode(', ', $builder->columns);
        $fromQuery = 'from ' . $builder->from;

        $whereQuery = $this->buildWhere($builder);
        if (strlen($whereQuery) > 0) {
            $whereQuery = 'where ' . $whereQuery;
        }

        $orderQuery = $this->buildOrder($builder);
        if (strlen($orderQuery) > 0) {
            $orderQuery = 'order by ' . $orderQuery;
        }

        $query = "{$selectQuery} {$fromQuery} {$whereQuery} {$orderQuery}";

        // It is for var_dump
        foreach($this->bindings as $parameter){
            $query = preg_replace('/\?/i', $this->getWithType($parameter), $query, 1);
        }

        return $query;
    }

    // It is for var_dump
    private function getWithType($parameter)
    {
        if (is_string($parameter)) return "'{$parameter}'";

        return $parameter;
    }

    private function buildWhere(QueryBuilder $builder): string
    {
        $whereQuery = '';

        if ($builder->wheres && count($builder->wheres) > 0) {
            foreach ($builder->wheres as $where) {

                if ($where['type'] === 'root') {
                    $whereQuery .= "{$where['combine']} {$where['column']} {$where['operator']} ? ";
                    $this->bindings[] = $where['parameter'];
                } elseif ($where['type'] === 'sub') {
                    $whereQuery .= "{$where['combine']} ({$this->buildWhere($where['builder'])}) ";
                } elseif ($where['type'] === 'null') {
                    $whereQuery .= "{$where['combine']} {$where['column']} is null ";
                } else {
                    throw new \InvalidArgumentException("Wrong type");
                }
            }

            $whereQuery = $this->removeCombineOperatorAtBegin($whereQuery);
        }

        return  $whereQuery;
    }

    private function removeCombineOperatorAtBegin(string $query): string
    {
        return trim( substr($query, strpos($query, ' ')) );
    }

    private function buildOrder(QueryBuilder $builder): string
    {
        $orderQuery = '';

        if ($builder->orders && count($builder->orders) > 0) {
            $orders = [];
            foreach ($builder->orders as $order) {
                $orders[] = "{$order['column']} {$order['direction']}";
            }

            $orderQuery = implode(',', $orders);
        }

        return $orderQuery;
    }
}