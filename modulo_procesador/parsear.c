#include "parsear.h"

int parsear(char *buffer, char *delimitador) {
    int contador = 0;
    char *token, *string, *tofree;

    tofree = string = strdup(buffer);
    if (string == NULL) {
        perror("strdup()");
        return -1;
    }
    while ((token = strsep(&string, delimitador)) != NULL) {
        printf("token -> %s\n",token);
    }
    free(tofree);

    /*
    char *delimitador_2 = ",";
    char *campo;
    char *array_campos[7];
    int contador_2 = 0;

    char query[150];
    char *str = "";

    MYSQL *con = init_connection();
    for (int j = 0; j < 13; j++)
    {
        memset(query, 0, sizeof query);
        str = "INSERT INTO registros VALUES(NULL";
        strcpy(query, str);
        contador_2 = 0;
        strcpy(auxiliar, array_lineas[j]);
        campo = strtok(auxiliar, delimitador_2);
        while (campo != NULL)
        {
            strcat(query, ",'");
            strcat(query, campo);
            strcat(query, "'");
            contador_2++;
            campo = strtok(NULL, delimitador_2);
        }
        strcat(query, ")");
        mysql_insert(con, query);
    }
    close_connection(con);
 */
    return contador;
}