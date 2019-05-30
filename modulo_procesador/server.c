#include "arg.h"
#include "service.h"

void *atender_cliente(void *arg) {
    int conn = (long)arg;
    service(conn);
    pthread_exit(NULL);
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
    int port = get_opt_server(argc, argv);
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

    while ((conn = accept(sockfd, NULL, 0)) > 0) {
        if (pthread_create(&tid, NULL, atender_cliente, (void *)(long)conn) != 0) {
            printf("error al crear el hilo\n");
            return -1;
        }
        if (pthread_join(tid, NULL)) {
            perror("pthread_join()");
            exit(EXIT_FAILURE);
        }
    }

    return 0;
}
