<?php

namespace Modules\Comments\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment',
    ];

    public function commenter()
    {
        return $this->morphTo();
    }

    public function commentable()
    {
        return $this->morphTo();
    }
}
