<?php

$socketClient = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$doesConnect = socket_connect($socketClient, $argv[1], $argv[2]);

if (!$doesConnect) {
    echo("connection failed!\n");
}

while ($writeBuffer = fgets(STDIN, 1024)) {
    $writeBuffer = trim($writeBuffer);

    if ($writeBuffer === 'quit') {
        socket_shutdown($socketClient, 1);
        break;
    }
    socket_write($socketClient, $writeBuffer, 1024);
    $readBuffer = socket_read($socketClient, 1024);

    if (!$readBuffer) {
        socket_close($connection);
        break;
    }

    echo($readBuffer);
}

if (!$writeBuffer) {
    echo("stdin error!\n");
}
