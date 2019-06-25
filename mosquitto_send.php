<?php

        $host = "localhost";
        $port = 9000;

        $client = new Mosquitto\Client('MyClient');

        $client->onConnect(function($code, $message) use ($client) {
            $client->subscribe('cchiappone', 1);
        });

        $client->onMessage(function($message) {
            echo $message->topic, "\n", $message->payload, "\n\n";

        if ($socket = socket_create(AF_INET, SOCK_STREAM, 0)) {
            $success = @socket_connect($socket, $host, $port);
            if ($success) {
                if (socket_write($socket, $message->payload, strlen($message->payload))) {
                    socket_close($socket);
                    echo "Error al enviar el mensaje";
                }
            } else {
                echo "Error al conectar al socket";
            }
        }
        });

        $client->connect($host, 1883);

        $client->loopForever();
