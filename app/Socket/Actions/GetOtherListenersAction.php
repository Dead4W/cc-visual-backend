<?php

namespace App\Socket\Actions;

use App\Enums\ConnectionClientTypeEnum;
use App\Models\Connection;
use Illuminate\Support\Collection;
use OpenSwoole\Server;

class GetOtherListenersAction
{

    public static function run(Connection $connection): Collection {
        $filterClientType = match ($connection->client_type) {
            ConnectionClientTypeEnum::WEB_IDE => ConnectionClientTypeEnum::CC,
            ConnectionClientTypeEnum::CC      => ConnectionClientTypeEnum::WEB_IDE,
            default => null
        };

        return Connection::query()
            ->where('session_id', $connection->session_id)
            ->where('id', '!=', $connection->id)
            ->where('client_type', $filterClientType->name)
            ->pluck('id');
    }

}
