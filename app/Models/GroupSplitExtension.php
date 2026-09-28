<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSplitExtension extends Model
{
    use HasFactory;

    // No updated_at for this table
    public $timestamps = false;

    protected $fillable = [
        'group_split_id',
        'old_end_time',
        'new_end_time',
        'extend_reason',
        'confirmed_by_guide_id',
        'confirmed_by_guide_name',
        'created_at',
    ];

    protected $casts = [
        'old_end_time' => 'datetime',
        'new_end_time' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function groupSplit()
    {
        return $this->belongsTo(GroupSplit::class, 'group_split_id');
    }
}
