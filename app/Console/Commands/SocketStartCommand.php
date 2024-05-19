<?php

namespace App\Console\Commands;

use App\Socket\Callbacks\SocketOnCloseCallback;
use App\Socket\Callbacks\SocketOnMessageCallback;
use App\Socket\Callbacks\SocketOnOpenCallback;
use App\Socket\Callbacks\SocketOnStartCallback;
use Illuminate\Console\Command;
use OpenSwoole\WebSocket\Server;
use OpenSwoole\Constant;

class SocketStartCommand extends Command
{
    public $signature = 'socket:start
                    {--host= : The IP address the server should bind to [default: "0.0.0.0"]]}
                    {--port= : The port the server should be available on [default: "9501"]}
    ';

    public $description = 'Start the Socket server';

    public function handle() {
        $server = new Server(
            host: $this->option('host') ?? "0.0.0.0",
            port: $this->option('port') ?? 9501,
            mode: Server::SIMPLE_MODE,
            sockType: Constant::SOCK_TCP
        );

        $server->on("Start", new SocketOnStartCallback($this->output));

        $server->on("Open", new SocketOnOpenCallback($this->output));
        $server->on("Message", new SocketOnMessageCallback($this->output));

        $server->on('Close', new SocketOnCloseCallback($this->output));
        $server->on('Disconnect', new SocketOnCloseCallback($this->output));

        $server->start();
    }

}
