<?php

namespace Modules\Comments\Traits;

use Illuminate\Database\Eloquent\Model;
use Modules\Comments\Models\Comment;

trait HasComments
{
    public static function bootHasCommentable()
    {
        static::deleting(function ($commentable) {
            foreach ($commentable->comments as $comment) {
                $comment->delete();
            }
        });
    }

    /**
     * Return all comments for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Attach a comment to this model.
     */
    public function comment(string $comment): Model
    {
        return $this->commentAsUser(auth()->user(), $comment);
    }

    /**
     * Attach a comment to this model as a specific user.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function commentAsUser(?Model $user, string $comment)
    {
        $model = new Comment();
        $model->commenter()->associate($user);
        $model->commentable_id = $this->getKey();
        $model->commentable_type = get_class();
        $model->comment = $comment;

        return $this->comments()->save($model);
    }

    /**
     * Update a comment.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateComment(int $id, string $comment)
    {
        $model = self::findOrFail($id);
        $model->update([
            'comment' => $comment,
        ]);

        return $model;
    }

    /**
     * Delete a comment.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function deleteComment(int $id)
    {
        $model = self::findOrFail($id);
        $model->delete();

        return $model;
    }
}
