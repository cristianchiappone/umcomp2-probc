#include "publicador.h"

int mqtt_send(char *msg, char *topic, char *host) {
    int port = 1883;
    int keepalive = 60;
    bool clean_session = true;
    struct mosquitto *mosq;

    mosquitto_lib_init();
    mosq = mosquitto_new(NULL, clean_session, NULL);
    if (!mosq) {
        fprintf(stderr, "Error: Out of memory.\n");
        return 0;
    }

    if (mosquitto_connect(mosq, host, port, keepalive)) {
        fprintf(stderr, "Unable to connect.\n");
        return 0;
    }
    int loop = mosquitto_loop_start(mosq);
    if (loop != MOSQ_ERR_SUCCESS) {
        fprintf(stderr, "Unable to start loop: %i\n", loop);
        return 0;
    }
    return mosquitto_publish(mosq, NULL, topic, strlen(msg), msg, 0, 0);
}