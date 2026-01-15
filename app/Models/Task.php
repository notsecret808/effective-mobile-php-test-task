<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Status;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "status"
    ];

   protected $hidden = [
    'created_at',
    'updated_at'
   ];

   protected $casts = [
    'status' => Status::class,
   ];
}
