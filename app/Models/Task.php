<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        "title",
        "description",
        "status"
    ];

    protected function casts(): array {
        return [
            "title" => "string",
            "description" => "string",
            "status" => "string"
        ];
    }
}
