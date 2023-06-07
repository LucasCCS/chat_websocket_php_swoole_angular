<?php
use Swoole\WebSocket\Server;

$server = new Server("0.0.0.0", 9501, SWOOLE_BASE);

$connections = [];

$server->on('start', function ($server) {
    echo "Server iniciado na porta: $server->port" . PHP_EOL;
});

$server->on('open', function ($server, $request) use ($connections) {
    if (!empty($request->get['channelId']) && !in_array($request->get['channelId'], $connections)) {
        $connections[] = $request->get['idProviderCompany'];
    }

    print_r($connections);
});

$server->on('message', function($server, $frame) {
    foreach ($server->connections as $connection) {

        if ($connection == $frame->fd) {
            continue;
        }

        $server->push($connection,$frame->data);
    }
});

$server->start();