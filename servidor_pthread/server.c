#include <arpa/inet.h> /* inet_aton */
#include <errno.h>
#include <fcntl.h>
#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>
#include "arg.h"

pthread_mutex_t mut;
#define max 5
int rear = 1, front = 1;

int insq(char logs[max][500], int *rear, char data[500]) {
    if (*rear == max - 1)
        return (-1);
    else {
        *rear = *rear + 1;
        strcpy(logs[*rear], data);
        printf("Se insertó: %s", data);
        return (1);
    }
}

int clearq(char logs[max][500], int *rear, int *front, char data[500]) {
    if (*front == *rear)
        return (-1);
    else {
        int log = open("log", O_APPEND | O_CREAT | O_WRONLY, 0777);
        if (log < 0) {
            perror("Open: ");
        }

        for (int i = 0; i < max; i++) {
            (*front)++;
            int escrito = write(log, logs[*front], strlen(logs[*front]));
            (*rear)--;
            if (escrito < 0) {
                perror("Write");
            }
        }
        *front = -1;

        if (close(log) < 0) {
            perror("Close");
        }
        return 0;
    }
}

void *atender_cliente(void *arg) {
    int conn = (long)arg;

    char logs[max][500];
    char buff[2048];
    int reply;

    memset(buff, 0, sizeof buff);
    read(conn, buff, sizeof buff);

    char copy[500];
    memset(copy, 0, sizeof copy);
    strcpy(copy, buff);

    if (rear == max - 1) {
        pthread_mutex_lock(&mut);
        reply = clearq(logs, &rear, &front, copy);
        if (reply == -1)
            printf("Cola llena \n");
        else
            printf("Se pasó la cola al archivo\n");
        pthread_mutex_unlock(&mut);
        reply = insq(logs, &rear, copy);
    } else {
        reply = insq(logs, &rear, copy);
        if (reply == -1) printf("Cola llena \n");
    }

    close(conn);
    return 0;
}

void detach_state(pthread_t tid, const char *tname) {
    int rc;
    rc = pthread_join(tid, NULL);
    if (rc == EINVAL) {
        printf("%s is detached\n", tname);
    } else if (rc == 0) {
        printf("%s was joinable\n", tname);
    } else {
        printf("%s: pthread_join() = %d (%s)\n", tname, rc, strerror(rc));
    }
}

int main(int argc, char **argv) {
    struct sockaddr_in direccion;
    int sockfd, conn;
    int val = 1;
    pthread_t tid;

    memset(&direccion, 0, sizeof(struct sockaddr_in));

    sockfd = socket(PF_INET, SOCK_STREAM, 0);

    if (sockfd == -1) {
        perror("socket()");
        exit(EXIT_FAILURE);
    }

    direccion.sin_family = AF_INET;
    int port = get_opt(argc, argv);
    direccion.sin_port = htons(port);
    inet_pton(AF_INET, "127.0.0.1", &direccion.sin_addr);
    if (setsockopt(sockfd, SOL_SOCKET, SO_REUSEADDR, &val, sizeof(val)) < 0) {
        perror("setsockopt()");
        exit(EXIT_FAILURE);
    }

    if (bind(sockfd, (struct sockaddr *)&direccion, sizeof(direccion)) < 0) {
        perror("bind()");
        exit(EXIT_FAILURE);
    }

    if (listen(sockfd, 5) < 0) {
        perror("listen()");
        exit(EXIT_FAILURE);
    }

    pthread_mutex_init(&mut, NULL);
    pthread_mutex_unlock(&mut);
    while ((conn = accept(sockfd, NULL, 0)) > 0) {
        if (pthread_create(&tid, NULL, atender_cliente, (void *)(long)conn) !=
            0) {
            perror("pthread_create()");
            return -1;
        }
        if (pthread_detach(tid)) {
            perror("pthread_detach()");
        }
        // detach_state(tid, "thread");
    }

    return 0;
}
