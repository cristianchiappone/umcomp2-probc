#include "arg.h"
#include "mosquitto.h"
#include "publicador.h"
#include "validar.h"
#define child_count_default 4;

void create_child(struct mosquitto_message **message, char *topic,char* device_id) {
    struct mosquitto_message *msg;
    int rc;
    if (!message || !*message) return;
    msg = *message;
    bool default_topic = (strcmp(topic, shared_topic_default) == 0) ? true : false;
    char *local_topic = "interno";
    switch (fork()) {
        case 0:
            if(default_topic){
                if (validar_dispositivo((char *)msg->payload)) {
                    rc = mqtt_send((char *)msg->payload, local_topic, "localhost");
                    if (rc) {
                        printf("Error: %s\n", mosquitto_strerror(rc));
                    }
                }
            } else {
                if (validar_dispositivo_generico((char *)msg->payload,device_id)) {
                    rc = mqtt_send((char *)msg->payload, "interface_php", "localhost");
                    if (rc) {
                        printf("Error: %s\n", mosquitto_strerror(rc));
                    }
                }
            }
            mosquitto_message_free(&msg);
            exit(0);
        case -1:
            perror("fork()");
            exit(1);
        default:
            break;
    }
}

int main(int argc, char **argv) {
    int rc;
    struct mosquitto_message *msg;
    char **options = get_opt_pub(argc, argv);

    printf(
        "Proceso Interface - PID(%d) -> Suscrito a Host:%s - Topico:%s - Puerto:%d - User:%s - Dispositivo_id:%s\n",
        getpid(), options[0], options[1], atoi(options[2]),options[3],options[5]);

    char *host = options[0];
    char *topic = options[1];
    char *port = options[2];
    char *user = options[3];
    char *password = options[4];
    char *device_id = options[5];
    int child_count;

    if (!validar_usuario(user, password)) {
        return 0;
    }

    if (strcmp(topic, shared_topic_default) == 0) {
        child_count = child_count_default;
        char *argv_list[] = {"./suscriptor", "-h", "localhost", "-t", "interno","p","1883",NULL};
        switch (fork()) {
            case -1:
                perror("fork()");
                return -1;
                break;
            case 0:
                if (execv("suscriptor", argv_list) < 0) {
                    perror("execv()");
                    return -1;
                }
                exit(0);
                break;
            default:
                break;
        }
    } else {
        child_count = 1;
    }

    for (int i = 0; i < child_count; i++) {
        switch (fork()) {
            case 0:
                mosquitto_lib_init();
                while (true) {
                    rc = mosquitto_subscribe_simple(
                        &msg, 1, true, topic, 0, host, atoi(port), NULL, 60,
                        true, NULL, NULL, NULL, NULL);
                    if (rc) {
                        printf("Error: %s\n", mosquitto_strerror(rc));
                        mosquitto_lib_cleanup();
                        return rc;
                    }
                    create_child(&msg, topic,device_id);
                }
                mosquitto_lib_cleanup();
                exit(0);
                break;
            case -1:
                perror("fork()");
                return -1;
                break;
            default:
                break;
        }
    }
    wait(NULL);
    return 0;
}
