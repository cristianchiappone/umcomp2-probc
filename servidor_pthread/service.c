#include "service.h"

const char *err_500 =
    "HTTP/1.1 500 INTERNAL SERVER ERROR\nContent-type: "
    "text/html\nContent-Length: 25\n\n500 INTERNAL SERVER ERROR";

int service(int sdc) {
    char buff[2048];
    int leido;
    memset(buff, 0, sizeof buff);
    leido = read(sdc, buff, sizeof buff);

    switch (leido) {
        case 0:
        case -1:
            printf("error \n");
            close(sdc);
            pthread_exit(NULL);
            break;
        default:
            parser_log(sdc, buff);
            break;
    }

    return 0;
}
