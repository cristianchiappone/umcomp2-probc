#ifndef _ARG_h_
#define _ARG_h_
#include <getopt.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

char** get_opt_pub(int argc, char** argv);
char** get_opt_sub(int argc, char** argv);

#endif