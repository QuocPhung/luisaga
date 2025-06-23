<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'name', 'description', 'price',
        'category_id', 'status', 'stock'
    ];    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function thumbnail()
    {
        return $this->hasOne(ProductImage::class)->where('is_thumbnail', 1);
    }
    public function sales()
    {
        return $this->belongsToMany(Sale::class);
    }
    public function cartItems()
    {
        return $this->hasMany(\App\Models\CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }


    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function getActiveSale()
    {
        return $this->sales()
            ->where(function ($query) {
                $now = now();
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) {
                $now = now();
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('discount_percent')
            ->first();
    }
    public function getDiscountTextAttribute()
    {
        $sale = $this->sales()->where('status', 1)->first();

        if (!$sale) return null;

        return $sale->discount_type === 'percent'
        ? "-{$sale->discount_value}%"
        : '-' . number_format($sale->discount_value) . 'đ';   
    }


    public function getFinalPriceAttribute()
    {
        $sale = $this->sales()
            ->where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    
        if ($sale) {
            if ($sale->discount_type === 'percent') {
                return round($this->price * (1 - $sale->discount_value / 100));
            } elseif ($sale->discount_type === 'fixed') {
                return max(0, $this->price - $sale->discount_value);
            }
        }
    
        return $this->price;
    }
    public function getSaleNameAttribute()
    {
        $sale = $this->sales()
            ->where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        return $sale ? $sale->name : null;
    }
    public function getSaleStatusLabelAttribute()
    {
        $sale = $this->sales()
            ->orderBy('start_date')
            ->first();

        if (!$sale) return null;

        $now = now();
        if ($now->lt($sale->start_date)) {
            return 'Sắp bắt đầu';
        } elseif ($now->between($sale->start_date, $sale->end_date)) {
            $daysLeft = $now->diffInDays($sale->end_date);
            return "Còn {$daysLeft} ngày";
        } else {
            return 'Hết hạn';
        }
    }
    public function scopeOnSale($query)
    {
        return $query->whereHas('sales', function ($q) {
            $q->where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
        });
    }

}