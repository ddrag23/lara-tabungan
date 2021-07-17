<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;
    protected $table = 'tb_deposit';
    protected $fillable = ['user_id', 'nominal'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
