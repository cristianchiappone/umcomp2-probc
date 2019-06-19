#ifndef _ARG_h_
#define _ARG_h_
#include <getopt.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <ctype.h>
#define shared_topic_default "$shared/sensors/umcomp2"

char** get_opt_pub(int argc, char** argv);
char** get_opt_sub(int argc, char** argv);
int get_opt_server(int argc, char **argv);

#endif