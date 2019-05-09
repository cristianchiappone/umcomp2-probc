#include "service.h"
#include <arpa/inet.h> /* inet_aton */
#include <ctype.h>
#include <errno.h>
#include <fcntl.h>
#include <netdb.h>
#include <netinet/in.h>
#include <signal.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>
#include "http_parser.h"
#include "pthread.h"

const char *err_500 =
    "HTTP/1.1 500 INTERNAL SERVER ERROR\nContent-type: "
    "text/html\nContent-Length: 25\n\n500 INTERNAL SERVER ERROR";

int service(int sdc) {
    char buff[2048], copy[2048];
    char *token, *string, *tofree;
    int leido;
    memset(buff, 0, sizeof buff);
    leido = read(sdc, buff, sizeof buff);

    switch (leido) {
        case 0:
            printf("error \n");
            close(sdc);
            pthread_exit(NULL);
            break;
        case -1:
            printf("error %s\n", strerror(errno));
            close(sdc);
            pthread_exit(NULL);
            break;
        case 2:
            write(sdc, err_500, strlen(err_500));  // telnet vacio
            close(sdc);
            pthread_exit(NULL);
            break;
        default:
            parser_header(sdc, buff);
            break;
    }

    return 0;
}
