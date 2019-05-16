#ifndef _error_h_
#define _error_h_
#include <stdio.h>
#include <string.h>
#include <unistd.h>

/* Error values */
enum mod_err_t {
    MOD_ERR_PUB_SERVER_UP = 1,
    MOD_ERR_PUB_FORK_SUB = 2,
    MOD_ERR_PUB_EXEC_SUB = 3,
    MOD_ERR_PUB_CREATE_CHILD_SEND = 4,
};

const char *modulo_strerror(int);

#endif