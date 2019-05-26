#ifndef _publicador_h_
#define _publicador_h_
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include "mosquitto.h"

int mqtt_send(char *message, char *topic, char *host);
#endif
