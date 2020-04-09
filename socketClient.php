<?php

$socketClient = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$doesConnect = socket_connect($socketClient, $argv[1], $argv[2]);
$writeBuffer = '';
$doesQuit = false;

if (!$doesConnect) {
    echo("connection failed!\n");
}

while (true) {
    $readSockets = [$socketClient];
    $writeSockets = [];
    $exceptionSockets = [];

    @socket_select($readSockets, $writeSockets, $exceptionSockets, 0);

    if (in_array($socketClient, $readSockets)) {
        $readBuffer = socket_read($socketClient, 1024);

        if (strlen($readBuffer) === 0) {
            echo("server closed!\n");
            break;
        }
        echo($readBuffer);
    }

    if ($writeBuffer !== 'quit') {
        $writeBuffer = fgets(STDIN, 1024);
        $writeBuffer = trim($writeBuffer);
    }

    if (empty($writeBuffer)) {
        continue;
    }

    if ($writeBuffer === 'quit' && $doesQuit === false) {
        $doesQuit = true;

        if (!socket_shutdown($socketClient, 1)) {
            echo("shutdown fail!\n");
            break;
        }
    } elseif ($doesQuit === false) {
        socket_write($socketClient, $writeBuffer, 1024);
    }
}
