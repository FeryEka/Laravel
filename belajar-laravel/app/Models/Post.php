<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // protected $fillable = ['title', 'slug', 'excerpt', 'body'];
    protected $guarded = ['id'];
    protected $with = ['category', 'author'];

    public function scopeFilter($query, array $filters){
        // menggunakan isset
        if(isset($filters['search']) ? $filters['search'] : false) {
            return $query->where('title', 'like', '%' . $filters['search'] . '%')
                         ->orWhere('body', 'like', '%' . $filters['search'] . '%');
        }

        // menggunakan null coalescing operator and callback using function
        $query->when($filters['category'] ?? false, function($query, $category){
            return $query->whereHas('category', function($query) use ($category){
                $query->where('slug', $category);
            });         
        });

        // menggunakan null coalescing operator and callbanck using arrow function
        $query->when($filters['author'] ?? false, fn($query, $author) =>
            $query->whereHas('author', fn($query) =>
                $query->where('username', $author)
            )
        );
    }

    public function category(){
        
        return $this->belongsTo(Category::class);
    }

    public function author(){
        return $this->belongsTo(User::class, 'user_id');
    }
}