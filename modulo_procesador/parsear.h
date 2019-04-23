#ifndef _parsear_h_
#define _parsear_h_
#include <stdio.h> // input/ouput
#include <string.h> //strtok
#include <unistd.h> //stat
#include <sys/stat.h> // stat
#include <sys/types.h> // stat
#include <sys/mman.h> //mmap
#include <mysql/my_global.h> 
#include <mysql/mysql.h> //mysql
#include "mysql_connect.h" //mysql

int parsear(int,char* ,char* );

#endif 

