<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory, SoftDeletes;
    
    protected $dates = ['deleted_at', 'expires_at', 'last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'tokenable_id');
    }

    /**
     * Soft delete o token ao invés de hard delete
     */
    public function delete()
    {
        return parent::delete();
    }
}