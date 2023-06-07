<?php
use Swoole\WebSocket\Server;

class WebSocketServer {

    private $server;
    private $channels = [];
    public function __construct() {
        $this->server = new Server("0.0.0.0", 9501, SWOOLE_BASE);

        
        $this->server->on('start', function ($server) {
            echo "Server iniciado na porta: $server->port" . PHP_EOL;
        });

        $this->server->on('open', function ($server, $request) {

            if (empty($this->channels[$request->get['channelId']])) {
                $this->channels[$request->get['channelId']] = [];
            }

            if (!in_array($request->fd, $this->channels[$request->get['channelId']])) {
                $this->channels[$request->get['channelId']][] = $request->fd;
            }
        });

        $this->server->on('message', function($server, $frame) {
            
            $data = json_decode($frame->data);

            $connections = [];

            if (!empty($this->channels[$data->toChannel])) {
                $connections = $this->channels[$data->toChannel];
            }

            foreach ($connections as $connection) {
                if ($frame->fd == $connection) {
                    continue;
                }

                $server->push($connection,$frame->data);
            }
           
        });

        // remover conexao quando houver evento de disconnect


    }

    public function run() {
        $this->server->start();
    }
}

$ws = new WebSocketServer();

$ws->run();