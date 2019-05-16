#ifndef _ARG_h_
#define _ARG_h_
#include <getopt.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <stdbool.h>

char** get_opt_pub(int argc, char** argv);
char** get_opt_sub(int argc, char** argv);

#endif