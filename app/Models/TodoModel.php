<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property bool $is_done
 * @property string|null $cover
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class TodoModel extends Model
{
    protected $table = 'm_todos';

    protected $primaryKey = 'id';

    // id berupa string / UUID, bukan auto-increment integer
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'description',
        'is_done',
        'cover',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public $timestamps = true;
}
