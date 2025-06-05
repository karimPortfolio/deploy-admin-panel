<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Support\Carbon;

class DateFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $date = Carbon::parse($value)->format('Y-m-d');

        $query->whereDate($property, $date);
    }
}
