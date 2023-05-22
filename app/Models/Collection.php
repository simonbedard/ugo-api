<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collection extends Model
{

    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collection';
    protected $fillable = [
        'user_id', 
        'images', 
        "title", 
        "description",
        "share_link",
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
