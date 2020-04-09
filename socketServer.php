<?php

$socketServer = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socketServer, SOL_SOCKET, SO_REUSEPORT, 1);
socket_bind($socketServer, '127.0.0.1', 43211);
socket_listen($socketServer, SOMAXCONN);

while (true) {
    $connection = socket_accept($socketServer);

    while (true) {
        $readBuffer = socket_read($connection, 1024);

        if (strlen($readBuffer) === 0) {
            socket_close($connection);
            break;
        }
        $readBuffer = trim($readBuffer);

        if ($readBuffer === 'pwd') {
            $returnData = shell_exec('pwd');
        } elseif (substr($readBuffer, 0, 2) === 'cd') {
            chdir(substr($readBuffer, 3));
        } elseif ($readBuffer === 'ls') {
            $returnData = shell_exec('ls');
        } elseif ($readBuffer === '') {
            $returnData = '';
        } else {
            $returnData = "unknown input\n";
        }

        if (
            $returnData !== ''
            && !socket_write($connection, $returnData, 1024)
        ) {
            socket_close($connection);
            break;
        }
    }
}
