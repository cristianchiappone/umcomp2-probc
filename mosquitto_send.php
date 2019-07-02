<?php

        $host = 'localhost';
        $port = 9000;

        $client = new Mosquitto\Client('MyClient');

        $client->onConnect(function ($code, $message) use ($client) {
            $client->subscribe('interface_php', 1);
        });

        $client->onMessage(function ($message) {
            echo $message->topic, "\n", $message->payload, "\n\n";
            $mensaje = $message->payload."\r\n";
            if ($socket = socket_create(AF_INET, SOCK_STREAM, 0)) {
                if (socket_connect($socket, 'localhost', '9000')) {
                    if (!socket_write($socket, $mensaje, strlen($mensaje))) {
                        echo "Error al enviar el mensaje\n";
                    }
                    socket_close($socket);
                } else {
                    echo "Error al conectar al socket\n";
                }
            }
        });

        $client->connect($host, 1883);

        $client->loopForever();
