<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'parent_id', 'status'];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    public function childrenRecursive()
    {
        return $this->children()->with(['childrenRecursive' => function ($query) {
            $query->whereColumn('id', '!=', 'parent_id');
        }]);
    }    
    public function getAllDescendantCategoryIds()
    {
        $ids = [$this->id];
    
        foreach ($this->childrenRecursive as $child) {
            $ids = array_merge($ids, $child->getAllDescendantCategoryIds());
        }
    
        return $ids;
    }    
    
    protected static function booted()
    {
        static::deleting(function ($category) {
            $category->children()->delete(); // Xóa danh mục con trước
        });
    }
}
