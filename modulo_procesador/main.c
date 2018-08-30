#include "contar.h"
#include "mysql_connect.h"

int main(int argc, char **argv)
{
	int leido;
	char buffer[9999];
	int lineas;

	while ((leido = read(STDIN_FILENO, buffer, sizeof buffer)) > 0)
	{
		char *delimitador = " \n\t";
		lineas = contar(leido, buffer, delimitador);
		printf("Cantidad de Lineas: %d \n", lineas);
	}
	return 0;
}
