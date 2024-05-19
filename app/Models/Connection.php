<?php

namespace App\Models;

use App\Enums\ConnectionClientTypeEnum;
use App\Models\Scopes\IsHasActiveSessionScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{

    public $timestamps = true;

    protected $appends = [
        'name',
    ];

    protected $casts = [
        'client_type' => ConnectionClientTypeEnum::class,
        'last_message_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(IsHasActiveSessionScope::class);

//        parent::saved(function (self $model) {
//            self::query()
//                ->whereRaw('lower(project_name) = lower(?)', [$model->project_name_origin])
//                ->update([
//                             'project_name_alias' => $model->project_name_alias,
//                         ]);
//        });

//        parent::deleting(function (self $model) {
//            self::query()
//                ->whereRaw('lower(project_name) = lower(?)', [$model->project_name_origin])
//                ->update([
//                             'project_name_alias' => $model->project_name_origin,
//                         ]);
//        });
    }

    public function getNameAttribute(): string {
        return sprintf("Client-%'.06d", $this->id);
    }

    public function getMetaFromAttribute(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client_type' => $this->client_type->name,
        ];
    }

    public function session(): BelongsTo {
        return $this->belongsTo(Session::class);
    }

}
