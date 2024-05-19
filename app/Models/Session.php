<?php

namespace App\Models;

use App\Models\Scopes\IsActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Session extends Model
{

    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(IsActiveScope::class);

        parent::saving(function (self $model) {
            if (empty($model->web_ide_session_key)) {
                $model->web_ide_session_key = Str::random(32);
            }

            if (empty($model->cc_session_key)) {
                $model->cc_session_key = Str::random(32);
            }
        });
    }

}
