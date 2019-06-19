#ifndef _validar_h_
#define _validar_h_
#include <errno.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <math.h>
#include <time.h>

bool validar_usuario(char *user,char *password);
bool validar_dispositivo(char *payload);
bool validar_payload(char *payload);
bool validar_dispositivo_generico(char *payload,char* device_id);

#endif