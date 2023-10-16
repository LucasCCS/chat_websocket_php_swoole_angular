<?php

namespace App;

use Swoole\WebSocket\Server;

class WebSocketServer {

    /**
     * @var Server
     */
    private $server;
    /**
     * @var ChannelManager
     */
    private $channelManager;
    
    public function __construct() {
        $this->server = new Server("0.0.0.0", 9501, SWOOLE_BASE);
        $this->channelManager = new ChannelManager();

        
        $this->server->on('start', function ($server) {
            echo "Server iniciado na porta: $server->port" . PHP_EOL;
        });

        $this->server->on('open', function ($server, $request) {
            $this->onConnectionOpen($request);
        });

        $this->server->on('message', function($server, $frame) {
            $this->onMessageReceived($server, $frame);
        });

        $this->server->on('close', function ($server, $fd) {
           $this->onConnectionClose($fd);
        });
    }

    /**
     * @return void
     */
    public function run(): void {
        $this->server->start();
    }

    /**
     * @param mixed $request
     *
     * @return void
     */
    public function onConnectionOpen($request): void {
        if (empty($request->get['channelId'])) {
            return;
        }

        $this->channelManager->addConnectionToChannel($request->get['channelId'], $request->fd);
    }

    /**
     * @param mixed $server
     * @param mixed $frame
     *
     * @return void
     */
    public function onMessageReceived($server, $frame): void {
        $data = json_decode($frame->data);
        
        if (!$this->isValidMessage($frame->data)) {
            return;
        }

        if (empty($data->channelId)) {
            return;
        }

        $connections = $this->channelManager->getConnectionsInChannels($data->channelId);

        foreach ($connections as $connection) {
            if ($frame->fd == $connection) {
                continue;
            }

            $server->push($connection,$frame->data);
        }
    }

    /**
     * @param mixed $fd
     *
     * @return void
     */
    public function onConnectionClose($fd): void {
        $this->channelManager->removeConnectionFromChannels($fd);
    }

    /**
     * @param mixed $message
     *
     * @return bool
     */
    private function isValidMessage($message): bool {
        $message = trim($message);
        return !empty($message);
    }
}
