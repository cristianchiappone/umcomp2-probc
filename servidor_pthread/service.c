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

int service(int sdc) {
    char buff[2048];
    int leido;

    if ((leido = read(sdc, buff, sizeof buff)) == -1) {
        printf("error %s\n", strerror(errno));
    }

    parser(sdc, buff);

    return 0;
}
