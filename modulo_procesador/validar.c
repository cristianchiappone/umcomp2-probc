#include "validar.h"
#include "cJSON.h"
#include "mysql_connect.h"

bool validar_dispositivo(char *payload) {
    cJSON *json = cJSON_Parse(payload);
    cJSON *items = cJSON_GetObjectItem(json, "device_id");
    if (items->type == cJSON_String || items->type == cJSON_Raw) {
        MYSQL *con = init_connection();
        MYSQL_RES *res;
        MYSQL_ROW row;
        MYSQL_FIELD *field;
        char query[150];
        memset(query, 0, sizeof query);
        strcpy(query, "SELECT * FROM dispositivo WHERE id = '");
        strcat(query, items->valuestring);
        strcat(query, "'");
        if (mysql_query(con, query)) {
            finish_with_error(con);
        }
        res = mysql_store_result(con);
        if (res->row_count == 0) {
            printf("No se encontraron resultados.\n");
            close_connection(con);
            if (json != NULL) {
                cJSON_Delete(json);
            }
            return false;
        } else {
            int num_fields = mysql_num_fields(res);
            while ((row = mysql_fetch_row(res))) {
                for (int i = 0; i < num_fields; i++) {
                    if (row[i] != NULL) {
                        field = mysql_fetch_field_direct(res, i);
                        printf("%s: %s, ", field->name, row[i]);
                    }
                }
                printf("\n");
            }
            if (res != NULL) mysql_free_result(res);
            close_connection(con);
            if (json != NULL) {
                cJSON_Delete(json);
            }
            return true;
        }
    } else {
        if (json != NULL) {
            cJSON_Delete(json);
        }
        return false;
    }
}