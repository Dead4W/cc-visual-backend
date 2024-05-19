<?php

namespace App\Socket\Callbacks;

use App\Enums\ConnectionClientTypeEnum;
use App\Models\Connection;
use App\Socket\Actions\GetOtherListenersAction;
use App\Socket\Actions\SocketErrorAction;
use Carbon\Carbon;
use OpenSwoole\WebSocket\Frame;
use OpenSwoole\WebSocket\Server;

class SocketOnMessageCallback extends AbstractSocketCallback
{

    public function __invoke(Server $server, Frame $frame) {
        /** @var Connection $currentConnection */
        $currentConnection = Connection::query()
            ->where('id', $frame->fd)
            ->first();

        if ($currentConnection === null) {
            $this->error("Connection not found!");
            SocketErrorAction::run($server, $frame->fd, "Connection closed");
            return;
        }

        $this->info("Received message from {$currentConnection->name}: {$frame->data}");
        $currentConnection->last_message_at = Carbon::now();
        $currentConnection->save();

        $decodedFrameData = @json_decode($frame->data, true);

        if ($decodedFrameData === null) {
            $this->error("JSON data invalid");
            SocketErrorAction::run($server, $frame->fd, "JSON data invalid");
            return;
        }

        $data = [
            'type' => 'message',
            'meta' => [
                'from' => $currentConnection->meta_from,
            ],
            'data' => $decodedFrameData,
        ];

        $connectionIds = GetOtherListenersAction::run($currentConnection);
        foreach ($connectionIds as $connectionId) {
            $server->push($connectionId,  json_encode($data));
        }
    }

}
