<?php

namespace FS;

interface SqlAdapter
{
    public function build(QueryBuilder $builder): string;
}