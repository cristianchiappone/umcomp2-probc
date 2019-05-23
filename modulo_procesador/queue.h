#ifndef _queue_h_
#define _queue_h_
#include <errno.h>
#include <mqueue.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/wait.h>
#define MQNAME "/pax"

void clientSend(char *);
mqd_t clientUp(char *);
void serverDown(mqd_t);
char* serverReceive(mqd_t);
mqd_t serverUp();

#endif
