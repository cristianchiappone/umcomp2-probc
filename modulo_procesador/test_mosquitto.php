<?php

/* Construct a new client instance, passing a client ID of “MyClient” */
$client = new Mosquitto\Client('MyClient');

/* Set the callback fired when the connection is complete */
$client->onConnect(function($code, $message) use ($client) {
    /* Subscribe to the broker's $SYS namespace, which shows debugging info */
    $client->subscribe('zanzito/juanbrz92/location', 0);
});

/* Set the callback fired when we receive a message */
$client->onMessage(function($message) {
    /* Display the message's topic and payload */
    echo $message->topic, "\n", $message->payload, "\n\n";
});

/* Connect, supplying the host and port. */
/* If not supplied, they default to localhost and port 1883 */
$client->connect('test.mosquitto.org', 1883);

/* Enter the event loop */
$client->loopForever();