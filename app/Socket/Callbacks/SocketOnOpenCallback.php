<?php

namespace App\Socket\Callbacks;

use App\Enums\ConnectionClientTypeEnum;
use App\Models\Connection;
use App\Models\Session;
use App\Socket\Actions\GetOtherListenersAction;
use App\Socket\Actions\SocketErrorAction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use OpenSwoole\Http\Request;
use OpenSwoole\WebSocket\Server;

class SocketOnOpenCallback extends AbstractSocketCallback
{

    public function __invoke(Server $server, Request $request) {
        $this->info("Received connection");

        $connectionId = $request->fd;
        $sessionKey = $request->get['session_key'] ?? '';

        $session = Session::query()
            ->where(function (Builder $q) use ($sessionKey) {
                $q
                    ->orWhere('web_ide_session_key', $sessionKey)
                    ->orWhere('cc_session_key', $sessionKey);
            })
            ->first();

        if ($session === null) {
            $this->warning('Bad new connection session key, disconnecting...');
            SocketErrorAction::run($server, $connectionId, "Session key not found!");
            return;
        }

        $clientType = match ($sessionKey) {
            $session->web_ide_session_key => ConnectionClientTypeEnum::WEB_IDE,
            $session->cc_session_key => ConnectionClientTypeEnum::CC,
            default => null
        };

        $currentConnection = new Connection();
        $currentConnection->id = $connectionId;
        $currentConnection->client_type = $clientType;
        $currentConnection->session_id = $session->id;
        $currentConnection->last_message_at = Carbon::now();
        $currentConnection->save();

        $connectionsCount = Connection::count();

        $this->info("Connection <{$currentConnection->id}> open by {$currentConnection->name}. Total connections: {$connectionsCount}");

        $connectionIds = GetOtherListenersAction::run($currentConnection);

        $server->push($currentConnection->id, json_encode([
            'type' => 'list_connections',
            'data' => $connectionIds,
        ]));

        foreach ($connectionIds as $connectionId) {
            $server->push($connectionId, json_encode([
                'type' => 'new_connection',
                'meta' => [
                    'from' => $currentConnection->meta_from,
                ]
            ]));
        }
    }

}
