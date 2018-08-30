#include "parsear.h"
#include "arg.h"
#include "mysql_connect.h"

int main(int argc, char **argv)
{
    int lineas, file;
    char *ivalue = get_opt(argc, argv);
    char *ptr;

    file = open(ivalue, O_RDONLY, 0775);
    struct stat st1;
    stat(ivalue, &st1);
    printf("Cantidad de bytes del archivo  %lld \n", (long long)st1.st_size);
    char buffer[st1.st_size];

    if (ivalue)
    {
        ptr = mmap(NULL, st1.st_size, PROT_READ | PROT_WRITE, MAP_SHARED | MAP_ANON, -1, 0);
        read(file, buffer, sizeof buffer);
        for (int i = 0; i <= st1.st_size; i++)
        {
            *(ptr + i) = buffer[i]; //Agrega el contenido del archivo a un espacio de memoria compartida entre procesos
        }

        char *delimitador = " \n\t";
        lineas = parsear(st1.st_size, ptr, delimitador);
        printf("Cantidad de Lineas: %d \n", lineas);
    }
    else
    {
        printf("No tengo nada que leer :( \n");
    }

    return 0;
}
