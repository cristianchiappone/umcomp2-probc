#include <ctype.h>
#include <errno.h>
#include <fcntl.h>
#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <unistd.h>

const char *jpg_header = "HTTP/1.0 200 Ok\r\nContent-Type: image/jpeg\r\n\r\n";
const char *png_header = "HTTP/1.0 200 Ok\r\nContent-Type: image/png\r\n\r\n";
const char *pdf_header =
    "HTTP/1.0 200 Ok\r\nContent-Type: application/pdf\r\n\r\n";
const char *html_header = "HTTP/1.0 200 Ok\r\nContent-Type: text/html\r\n\r\n";
const char *gif_header = "HTTP/1.0 200 Ok\r\nContent-Type: image/gif\r\n\r\n";
const char *textplain_header =
    "HTTP/1.0 200 Ok\r\nContent-Type: text/plain\r\n\r\n";
const char *err_404 =
    "HTTP/1.1 404 NOT FOUND\nContent-type: text/html\nContent-Length: "
    "13\n\n404 NOT FOUND";
const char *err_403 =
    "HTTP/1.1 403 FORBIDDEN\nContent-type: text/html\nContent-Length: "
    "13\n\n403 FORBIDDEN";
const char *err_405 =
    "HTTP/1.1 405 METHOD NOT ALLOWED\nContent-type: "
    "text/html\nContent-Length: 23\n\n405 METHOD NOT ALLOWED";
const char *err_500 =
    "HTTP/1.1 500 INTERNAL SERVER ERROR\nContent-type: "
    "text/html\nContent-Length: 25\n\n500 INTERNAL SERVER ERROR";

void parser(int sdc, char *buff) {
    struct Response {
        char resource[100];
    };

    const char *last_token;

    char copy[2048], method[4], resource[100], filename[50], extension[7];
    int leido, fd;

    strcpy(copy, buff);  // hago una copia del buffer para poder usar strtok

    struct Response Resp;
    if ((strcmp(buff, "") == 0)) {
        write(sdc, err_500, strlen(err_500));
        exit(0);
    }

    last_token =
        strtok(copy, " ");  // busco un espacio en copia, cuando lo encuentra me
                            // devuelve un puntero al ultimo token
    strcpy(method, last_token);

    if (!strcmp(method, "GET") && !strcmp(method, "POST")) {
        write(sdc, err_405, strlen(err_405));
        exit(0);
    } else {
        last_token = strtok(NULL, " ");
        strcpy(resource, last_token + 1);

        if ((fd = open(resource, O_RDONLY)) < 0) {
            switch (errno) {
                case 13:
                    write(sdc, err_403, strlen(err_403));
                    break;
                case 2:
                    write(sdc, err_404, strlen(err_404));
                    break;
            }
        } else {
            strcpy(filename, resource);

            strcpy(Resp.resource, resource);

            last_token = strtok(filename, ".");
            last_token = strtok(NULL, ".");
            strcpy(extension, last_token);

            if (strcmp(extension, "jpg") ==
                0) {  // == 0 si ambas cadenas son identicas
                write(sdc, jpg_header, strlen(jpg_header));
            } else if (strcmp(extension, "png") == 0) {
                write(sdc, png_header, strlen(png_header));
            } else if (strcmp(extension, "pdf") == 0) {
                write(sdc, pdf_header, strlen(pdf_header));
            } else if (strcmp(extension, "html") == 0) {
                write(sdc, html_header, strlen(html_header));
            } else if (strcmp(extension, "gif") == 0) {
                write(sdc, gif_header, strlen(gif_header));
            } else {
                write(sdc, textplain_header, strlen(textplain_header));
            }

            while ((leido = read(fd, copy, sizeof copy)) != 0) {
                write(sdc, copy, leido);
            }
            printf("Información de la solicitud \n");
            printf("Método: %s \n", method);
            printf("Recurso: %s \n", resource);
            printf("-----------------------\n");
            close(fd);
            close(sdc);
            pthread_exit(NULL);
        }
    }

    close(fd);
    close(sdc);
    pthread_exit(NULL);
    // printf("Método: %s \n", buff);
}