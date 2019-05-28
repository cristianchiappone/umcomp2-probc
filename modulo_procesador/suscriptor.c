#include "arg.h"
#include "mosquitto.h"
#include "mysql_connect.h"
#include "validar.h"

void create_child(struct mosquitto_message **message) {
    struct mosquitto_message *msg;
    int rc;
    if (!message || !*message) return;
    msg = *message;
    switch (fork()) {
        case 0:
            if (validar_payload((char *)msg->payload)) {
                printf("");
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
    char **options = get_opt_sub(argc, argv);
    printf(
        "Proceso suscriptor - PID(%d) -> Suscrito a Host:%s - Topico:%s - "
        "Puerto:%d\n",
        getpid(), options[0], options[1], atoi(options[2]));
    for (int i = 0; i < 4; i++) {
        switch (fork()) {
            case 0:
                mosquitto_lib_init();
                while (true) {
                    rc = mosquitto_subscribe_simple(
                        &msg, 1, true, options[1], 0, options[0],
                        atoi(options[2]), NULL, 60, true, NULL, NULL, NULL,
                        NULL);
                    if (rc) {
                        printf("Error: %s\n", mosquitto_strerror(rc));
                        mosquitto_lib_cleanup();
                        return rc;
                    }
                    create_child(&msg);
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