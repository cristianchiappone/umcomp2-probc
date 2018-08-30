#include "contar.h"

int contar(int leido, char *buffer, char *delimitador)
{
    int contador = 0;
    char *cadena, auxiliar[leido], *array_lineas[14];

    strcpy(auxiliar, buffer);
    cadena = strtok(auxiliar, delimitador);
    array_lineas[contador] = cadena;
    while (cadena != NULL)
    {
        /* printf("Linea_%d en funcion contar: %s \n", contador + 1, array_lineas[contador]); */
        contador++;
        cadena = strtok(NULL, delimitador);
        array_lineas[contador] = cadena;
    }

    char *delimitador_2 = ",";
    char *campo;
    /* char *array_campos[7]; */
    int contador_2 = 0;

    char query[150];
    char *str = "";

    MYSQL *con = init_connection();
    for (int j = 0; j < 14; j++)
    {
        memset(query, 0, sizeof query);
        str = "INSERT INTO test VALUES(NULL";
        strcpy(query, str);
        contador_2 = 0;
        strcpy(auxiliar, array_lineas[j]);
        campo = strtok(auxiliar, delimitador_2);
        /* array_campos[contador_2] = campo; */
        while (campo != NULL)
        {
            strcat(query, ",'");
            strcat(query, campo);
            strcat(query, "'");
            //printf("Linea_%d campo_%d valor: %s \n", j + 1, contador_2 + 1, array_campos[contador_2]);
            contador_2++;
            campo = strtok(NULL, delimitador_2);
            /* array_campos[contador_2] = campo; */
        }
        strcat(query, ")");
        /* printf("query %s \n", query); */
        mysql_insert(con, query);
    }
    close_connection(con);

    return contador;
}