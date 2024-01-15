<?php

namespace Modules\Documents\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Branches\Models\Branch;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Wildside\Userstamps\Userstamps;

class Document extends Model implements HasMedia, Transformable
{
    use HasFactory, TransformableTrait;
    use InteractsWithMedia;
    use SoftDeletes, Userstamps;

    protected $fillable = [
        'branch_id',
        'category_id',
        'type',
        'name',
        'content',
        'document_number',
        'issued_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('branch', function (Builder $builder) {
            if (auth()->user()->branch_id) {
                return $builder->where('branch_id', auth()->user()->branch_id);
            } else {
                return $builder;
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }
}
