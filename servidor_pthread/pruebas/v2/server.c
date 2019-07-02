#include <arpa/inet.h> /* inet_aton */
#include <ctype.h>
#include <netdb.h>
#include <netinet/in.h>
#include <pthread.h>
#include <signal.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/wait.h>
#include <unistd.h>
#include "arg.h"
#include "service.h"

#include "sys/mman.h"

pthread_mutex_t mut;
#define max 50
char logs[max][100];

void *atender_cliente(void *arg) {
    int conn = (long)arg;
    service(conn,(* logs)[50]);
}

void detach_state(pthread_t tid,     
                  const char *tname 
) {
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
        detach_state(tid, "thread");
    }

    return 0;
}
