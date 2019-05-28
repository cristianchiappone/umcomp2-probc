#ifndef _validar_h_
#define _validar_h_
#include <errno.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <math.h>

bool validar_dispositivo(char *payload);
bool validar_payload(char *payload);

#endif