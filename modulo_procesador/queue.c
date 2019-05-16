#include "queue.h"

mqd_t serverUp(void) {
    int rc;
    mqd_t svrHndl;
    struct mq_attr mqAttr;

    rc = mq_unlink(MQNAME);
    if (rc == -1) {
        perror("mq_unlink()");
        return -1;
    } else {
        mqAttr.mq_maxmsg = 10;
        mqAttr.mq_msgsize = 1024;
        svrHndl = mq_open(MQNAME, O_RDWR | O_CREAT, S_IWUSR | S_IRUSR, &mqAttr);
        if (svrHndl < 0) {
            perror("mq_open()");
            return -1;
        }
        printf("Servidor publicando en cola mqd_t -> %d.\n", svrHndl);
        return svrHndl;
    }
}

mqd_t clientUp(char *queue_name) {
    mqd_t cliHndl;

    cliHndl = mq_open(queue_name, O_RDONLY);
    if (cliHndl < 0) {
        perror("mq_open()");
        exit(1);
    }
    printf("Ciente leyendo en cola mqd_t -> %d con nombre: \"%s\".\n", cliHndl,queue_name);
    return cliHndl;
}

char *serverReceive(mqd_t svrHndl) {
    int rc;
    static char buffer[1024];
    memset(buffer, 0, sizeof(buffer));
    rc = mq_receive(svrHndl, buffer, sizeof(buffer), NULL);
    if (rc < 0) {
        perror("mq_receive()");
        exit(1);
    }
    return buffer;
}

void serverDown(mqd_t svrHndl) { mq_close(svrHndl); }

void clientSend(char *mensaje) {
    mqd_t cliHndl;
    int rc;
    printf("Client sending -> %s\n", mensaje);
    cliHndl = mq_open(MQNAME, O_RDWR);
    if (cliHndl < 0) {
        perror("mq_open()");
        exit(1);
    }

    rc = mq_send(cliHndl, mensaje, strlen(mensaje), 1);
    if (rc < 0) {
        perror("mq_send()");
        exit(1);
    }
    mq_close(cliHndl);
}