<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use App\ModelFilters\Admin\CategoryFilter;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, Filterable, SoftDeletes;
    protected $guarded = [];
    public function modelFilter()
    {
        return $this->provideFilter(CategoryFilter::class);
    }
}
