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

void parser_header(int sdc, char *buff) {
    struct Response {
        char http_method[6];
        char resource[100];
        char http_version[10];
        char form[100];
    } Resp;

    char *last_token, *string, *tofree, *token;

    char copy[2048], filename[50], extension[7];
    int leido, fd;

    strcpy(copy, buff);  // hago una copia del buffer para poder usar strtok

    last_token = strtok(copy, " ");
    strcpy(Resp.http_method, last_token);

    last_token = strtok(NULL, " ");
    strcpy(Resp.resource, last_token + 1);

    last_token = strtok(NULL, " \n");
    strcpy(Resp.http_version, last_token);

    tofree = string = strdup(buff);
    if (string == NULL) {
        perror("strdup()");
        return -1;
    }
    while ((token = strsep(&string, "\r\n\r\n")) != NULL) {
        if (strstr(token, "&")) {
            get_key_value(token);
        }
    }
    free(tofree);

    printf("Información de la solicitud \n\t");
    printf("Método: %s \n\t", Resp.http_method);
    printf("Recurso: %s \n\t", Resp.resource);
    printf("Versión HTTP: %s \n", Resp.http_version);
    printf("-----------------------\n");

    if ((strcmp(Resp.http_method, "POST") == 0) ||
        (strcmp(Resp.http_method, "GET") == 0)) {
        if ((fd = open(Resp.resource, O_RDONLY)) < 0) {
            switch (errno) {
                case 13:
                    write(sdc, err_403, strlen(err_403));
                    break;
                case 2:
                    write(sdc, err_404, strlen(err_404));
                    break;
            }
        } else {
            strcpy(filename, Resp.resource);

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

            close(fd);
            close(sdc);
            pthread_exit(NULL);
        }
    } else {
        write(sdc, err_405, strlen(err_405));
        close(sdc);
        pthread_exit(NULL);
    }

    close(fd);
    close(sdc);
    pthread_exit(NULL);
}

struct key_value {
    char *key;
    char *value;
};
typedef struct key_value form_data;

void print_struct_elements(form_data *T) {
    printf("key: %s\n", T->key);
    printf("value: %s\n", T->value);
}

void get_key_value(char *string) {
    char *argv[] = {string, "&", "="};
    char *str1, *str2, *token, *subtoken;
    char *saveptr1, *saveptr2;
    int j, i = 0;
    form_data skv[2];

    for (j = 0, str1 = argv[0];; j++, str1 = NULL) {
        token = strtok_r(str1, argv[1], &saveptr1);
        if (token == NULL) break;
        printf("%d: %s\n", j, token);
        int cont = 0;
        for (str2 = token;; str2 = NULL) {
            subtoken = strtok_r(str2, argv[2], &saveptr2);
            if (subtoken == NULL) break;
            if (cont == 0) {
                skv[j].key = subtoken;
            } else {
                skv[j].value = subtoken;
            }
            printf(" --> %s\n", subtoken);
            cont++;
        }
        print_struct_elements(&skv[j]);
    }
}