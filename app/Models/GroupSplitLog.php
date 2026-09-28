<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSplitLog extends Model
{
    protected $fillable = [
        'group_split_id',
        'old_status',
        'new_status',
        'description',
        'triggered_by',
    ];

    public function group_split()
    {
        return $this->belongsTo(GroupSplit::class);
    }
}
