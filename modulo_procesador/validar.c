#include "validar.h"
#include "cJSON.h"
#include "mysql_connect.h"

#define query_select_id "SELECT * FROM dispositivo WHERE id = %s"
#define query_select_tipo_medicion                                        \
    "SELECT tm.id as tipo_medicion_id,tm.codigo FROM dispositivo d join " \
    "dispositivo_tipo_medicion dtm ON dtm.dispositivo_id = d.id join "    \
    "tipo_medicion tm ON tm.id = dtm.tipo_medicion_id where d.id = %s"
#define query_insert_medicion "INSERT INTO medicion VALUES(NULL,'%s','%s')"
#define query_insert_medicion_valor \
    "INSERT INTO medicion_valor VALUES(NULL,'%d','%s',%f)"

bool validar_dispositivo(char *payload) {
    cJSON *json = cJSON_Parse(payload);
    if (json == NULL) {
        const char *error_ptr = cJSON_GetErrorPtr();
        if (error_ptr != NULL) {
            fprintf(stderr, "Error before: %s\n", error_ptr);
        }
        return false;
    }
    cJSON *device_id = cJSON_GetObjectItemCaseSensitive(json, "device_id");
    if (device_id->type == cJSON_String || device_id->type == cJSON_Raw) {
        MYSQL *con = init_connection();
        MYSQL_RES *res;
        MYSQL_ROW row;
        MYSQL_FIELD *field;
        char query[150];
        memset(query, 0, sizeof query);
        snprintf(query, sizeof query, query_select_id, device_id->valuestring);

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

bool validar_payload(char *payload) {
    cJSON *json = cJSON_Parse(payload);
    if (json == NULL) {
        const char *error_ptr = cJSON_GetErrorPtr();
        if (error_ptr != NULL) {
            fprintf(stderr, "Error before: %s\n", error_ptr);
        }
        return false;
    }
    cJSON *device_id = cJSON_GetObjectItemCaseSensitive(json, "device_id");
    if (device_id->type == cJSON_String || device_id->type == cJSON_Raw) {
        MYSQL *con = init_connection();
        MYSQL_RES *res;
        MYSQL_ROW row;
        MYSQL_FIELD *field;
        char query[300];
        memset(query, 0, sizeof query);
        snprintf(query, sizeof query, query_select_tipo_medicion,
                 device_id->valuestring);

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
        }

        cJSON *sensors_data = cJSON_GetObjectItemCaseSensitive(json, "sensors");
        cJSON *value = NULL;
        char *sensor_name[10];
        float sensor_value[10];
        char datetime[20];
        memset(datetime, 0, sizeof datetime);
        int idx = 0;
        cJSON_ArrayForEach(value, sensors_data) {
            if (strcmp("datetime", value->string) == 0) {
                sensor_name[idx] = value->string;
                sensor_value[idx] = 0;
                strcpy(datetime, value->valuestring);
            } else {
                sensor_name[idx] = value->string;
                sensor_value[idx] = value->valuedouble;
            }
            idx++;
        }
        char query_medicion[100];
        memset(query_medicion, 0, sizeof query_medicion);
        if (datetime != NULL) {
            snprintf(query_medicion, sizeof query_medicion,
                     query_insert_medicion, datetime, device_id->valuestring);
        }
        if (mysql_query(con, query_medicion)) {
            printf("No se logro insertar medicion.\n");
            close_connection(con);
            if (json != NULL) {
                cJSON_Delete(json);
            }
            return false;
        }
        int medicion_id = mysql_insert_id(con);
        if (medicion_id == 0) {
            printf("Registro de medicion_id incorrecto.\n");
            close_connection(con);
            if (json != NULL) {
                cJSON_Delete(json);
            }
            return false;
        }

        char query_medicion_valor[100];
        int num_fields = mysql_num_fields(res);
        while ((row = mysql_fetch_row(res))) {
            for (int i = 0; i < num_fields; i++) {
                if (row[i] == NULL) {
                    continue;
                }
                field = mysql_fetch_field_direct(res, i);
                if (strcmp(field->name, "codigo") != 0) {
                    continue;
                }
                for (int j = 0; j < idx; j++) {
                    if (strcmp(row[1], sensor_name[j]) != 0) {
                        continue;
                    }
                    memset(query_medicion_valor, 0,
                           sizeof query_medicion_valor);
                    snprintf(query_medicion_valor, sizeof query_medicion_valor,
                             query_insert_medicion_valor, medicion_id, row[0],
                             sensor_value[j]);
                    if (mysql_query(con, query_medicion_valor)) {
                        printf(
                            "No se logro insertar "
                            "medicion_valor.\n");
                        close_connection(con);
                        if (json != NULL) {
                            cJSON_Delete(json);
                        }
                        return false;
                    }
                }
            }
        }
        printf("medicion_id -> %d  insertada correctamente\n", medicion_id);
        if (res != NULL) mysql_free_result(res);
        close_connection(con);
        if (json != NULL) {
            cJSON_Delete(json);
        }
        return true;
    } else {
        if (json != NULL) {
            cJSON_Delete(json);
        }
        return false;
    }
}