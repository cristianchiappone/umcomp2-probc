#include "http_parser.h"

struct Response {
    char usuario_id[3];
    char accion[2];
    char tabla[30];
    char valores[150];
    char fields[2];
} Resp;

void parser_log(int sdc, char *buff) {
    char *last_token;

    char copy[300], copy2[300];
    char path[7];

    memset(copy, 0, sizeof copy);
    memset(copy2, 0, sizeof copy2);
    memset(path, 0, sizeof path);
    strcpy(copy, buff);
    strcpy(copy2, copy);

    last_token = strtok(copy, " ");
    strcpy(Resp.usuario_id, last_token);

    last_token = strtok(NULL, " ");
    strcpy(Resp.accion, last_token);

    last_token = strtok(NULL, " ");
    strcpy(Resp.tabla, last_token);

    last_token = strtok(NULL, " ");
    strcpy(Resp.valores, last_token);

    strcat(path, "log/");
    strcat(path, Resp.usuario_id);
    int log = open(path, O_APPEND | O_CREAT | O_WRONLY, 0777);
    if (log < 0) {
        perror("Open: ");
    }
    strcat(copy2, "\n\r");
    write(1, copy2, sizeof copy2);
    int escrito = write(log, copy2, sizeof copy2);
    if (escrito < 0) {
        perror("Write");
    }

    printf("Información del log \n\t");
    printf("#UsuarioId: %s \n\t", Resp.usuario_id);
    printf("#Acción: %s \n\t", Resp.accion);
    printf("#Tabla: %s \n\t", Resp.tabla);
    printf("#Valores: %s \n\t", Resp.valores);
    printf("-----------------------\n");

    if(close(log) < 0){
        perror("Close");
    }
    close(sdc);
    pthread_exit(NULL);
}
