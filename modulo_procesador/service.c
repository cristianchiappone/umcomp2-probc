#include "service.h"
#include "pthread.h"
#include "publicador.h"

const char *err_500 =
    "HTTP/1.1 500 INTERNAL SERVER ERROR\nContent-type: "
    "text/html\nContent-Length: 25\n\n500 INTERNAL SERVER ERROR";

int service(int sdc) {
    char buff[2048];
    int leido,rc;
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
            rc = mqtt_send(buff, "umcomp2/test", "test.mosquitto.org");
            if(rc){
                printf("Error: %s\n",mosquitto_strerror(rc));
            }
            write(1, buff, sizeof buff);
            close(sdc);
            pthread_exit(NULL);
            break;
    }

    return 0;
}
