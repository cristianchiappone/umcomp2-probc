#include "arg.h"
#include "mosquitto.h"
#include "queue.h"

void create_child(struct mosquitto_message **message, mqd_t svrHndl) {
    struct mosquitto_message *msg;

    if (!message || !*message) return;
    msg = *message;
    switch (fork()) {
        case 0:
            clientSend((char *)msg->payload);
            mosquitto_message_free(&msg);
            serverDown(svrHndl);
            exit(0);
        case -1:
            perror("fork()");
            exit(1);
    }
}

int main(int argc, char **argv) {
    int rc;
    struct mosquitto_message *msg;
    char **options = get_opt_pub(argc, argv);

    mosquitto_lib_init();
    printf("Proceso - PID(%d) -> Suscrito a Host:%s - Topico:%s - Puerto:%d\n",
           getpid(), options[0], options[1], atoi(options[2]));

    mqd_t svrHndl;
    svrHndl = serverUp();
    if (svrHndl < 0) {
        perror("serverUp()");
        return -1;
    }
    char *argv_list[] = {"./suscriptor", "-q", MQNAME, NULL};
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
            while (true) {
                rc = mosquitto_subscribe_simple(
                    &msg, 1, true, options[1], 0, options[0], atoi(options[2]),
                    NULL, 60, true, NULL, NULL, NULL, NULL);

                if (rc) {
                    printf("Error: %s\n", mosquitto_strerror(rc));
                    mosquitto_lib_cleanup();
                    return rc;
                }
                create_child(&msg, svrHndl);
            }
            mosquitto_lib_cleanup();
            break;
    }
    return 0;
}
