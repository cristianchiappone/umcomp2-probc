#include "mysql_connect.h"

MYSQL *init_connection() {
    MYSQL *con = mysql_init(NULL);
    if (con == NULL) {
        fprintf(stderr, "%s\n", mysql_error(con));
        exit(1);
    }

    if (mysql_real_connect(con, "sql175.main-hosting.eu", "u716960574_comp2", "Rk4Q7pZUC5m9", "u716960574_comp2", 0, NULL, 0) == NULL) {
        finish_with_error(con);
    }
    return con;
}

void finish_with_error(MYSQL *con) {
    fprintf(stderr, "%s\n", mysql_error(con));
    close_connection(con);
    exit(1);
}

void close_connection(MYSQL *con) { mysql_close(con); }

void mysql_insert(MYSQL *con, char *query) {
    if (mysql_query(con, query)) {
        finish_with_error(con);
    }
}