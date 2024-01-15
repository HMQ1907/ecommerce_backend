<?php

namespace Modules\Documents\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Wildside\Userstamps\Userstamps;

class DocumentCategory extends Model implements Transformable
{
    use HasFactory, TransformableTrait;
    use SoftDeletes, Userstamps;

    protected $fillable = [
        'name',
    ];
}
