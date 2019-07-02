#ifndef _MY_SERVICE_H_
#define _MY_SERVICE_H_

#include "service.h"
#include "http_parser.h"
#include <string.h>
#include <stdio.h>
#include "pthread.h"
#include <unistd.h>
#include <errno.h>

int service(int sdc, char logs);

#endif
