#ifndef _mysql_connect_h_
#define _mysql_connect_h_
#include <stdio.h>
#include <unistd.h>
#include <string.h>
#include <mysql/my_global.h>
#include <mysql/mysql.h>

MYSQL * init_connection();
void finish_with_error(MYSQL *);
void mysql_insert(MYSQL *, char *);
void close_connection(MYSQL *);

#endif