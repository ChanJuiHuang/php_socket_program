<?php

$socketClient = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$doesConnect = socket_connect($socketClient, $argv[1], $argv[2]);
$writeBuffer = '';

if (!$doesConnect) {
    echo("connection failed!\n");
}
$socketClients = [$socketClient];
$streamClients = [STDIN];

while (true) {
    $readSockets = $socketClients;
    $writeSockets = [];
    $exceptionSockets = [];
    $readStreams = $streamClients;
    $writeStreams = [];
    $exceptionStreams = [];

    socket_select($readSockets, $writeSockets, $exceptionSockets, 0);
    stream_select($readStreams, $writeStreams, $exceptionStreams, 0, 500);

    if (in_array($socketClient, $readSockets)) {
        $readBuffer = socket_read($socketClient, 1024);

        if (strlen($readBuffer) === 0) {
            echo("server closed!\n");
            break;
        }
        echo($readBuffer);
    }

    if (in_array(STDIN, $readStreams)) {
        $writeBuffer = fgets(STDIN, 1024);
        $writeBuffer = trim($writeBuffer);
    }

    if (empty($writeBuffer)) {
        continue;
    }

    if ($writeBuffer === 'quit') {
        if (!socket_shutdown($socketClient, 1)) {
            echo("shutdown fail!\n");
            break;
        }
    } else {
        socket_write($socketClient, $writeBuffer, 1024);
    }
    $writeBuffer = '';
}
