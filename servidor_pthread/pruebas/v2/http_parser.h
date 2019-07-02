#ifndef _MY_HTTP_PARSER_H_
#define _MY_HTTP_PARSER_H_

#include <ctype.h>
#include <errno.h>
#include <fcntl.h>
#include <pthread.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <unistd.h>

void parser_log(int, char *, char);
#endif
