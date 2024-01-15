<?php

namespace Modules\Comments\Traits;

use Modules\Comments\Models\Comment;

trait HasCommenter
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commenter');
    }
}
