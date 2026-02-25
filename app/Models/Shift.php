<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Shift extends Model
{
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'shifts';

    protected $guarded = [
        'id',
    ];

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by', 'id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            DB::transaction(function () use ($model) {
                $maxNumber = DB::table('shifts')
                    ->lockForUpdate()
                    ->whereRaw("shift_number REGEXP '^[0-9]+$'")
                    ->max(DB::raw('CAST(shift_number AS UNSIGNED)'));
                $model->shift_number = ($maxNumber ?? 0) + 1;
            });
        });
    }
}
