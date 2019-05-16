#include "arg.h"
#include "parsear.h"
#include "queue.h"

int main(int argc, char **argv) {
    char **options = get_opt_sub(argc, argv);
    char *queue_name = options[0];
    mqd_t cliHndl;
    char *payload = NULL;
    while (true) {
        cliHndl = clientUp(queue_name);
        payload = serverReceive(cliHndl);
        switch (fork()) {
            case 0:
                parsear(payload, " ");
                exit(0);
                break;
            case -1:
                perror("fork()");
                break;
        }
        serverDown(cliHndl);
    }
    return 0;
}