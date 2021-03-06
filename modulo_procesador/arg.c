#include "arg.h"
#include "ctype.h"

char **get_opt_pub(int argc, char **argv) {
    static char *options[] = {"localhost", shared_topic_default, "1883","","",""};
    int opt;
    static char shared_topic[100];
    memset(shared_topic, 0, sizeof(shared_topic));
    char *shared = "$shared/sensors/";
    strncpy(shared_topic, shared, strlen(shared));
    while ((opt = getopt(argc, argv, "h:t:p:u:c:d:")) != -1) {
        switch (opt) {
            case 'h':
                options[0] = optarg;
                break;

            case 't':
                if (strcmp(optarg, "umcomp2") == 0) {
                    strcat(shared_topic, optarg);
                    options[1] = shared_topic;
                } else {
                    options[1] = optarg;
                }
                break;

            case 'p':
                options[2] = optarg;
                break;

            case 'u':
                options[3] = optarg;
                break;

            case 'c':
                options[4] = optarg;
                break;

            case 'd':
                options[5] = optarg;
                break;
            default:/* '?' */
                fprintf(stderr,"Usage: %s [-h host] [-t topic] [-p port] [-u usuario] [-c contraseña] [-d dispositivo_id]\n", argv[0]);
                exit(EXIT_FAILURE);
        }
    }

    if (strcmp(options[3],"") == 0) {
       printf("-u [usuario] es un parametro obligatorio!\n");
       exit(EXIT_FAILURE);
    }
    if (strcmp(options[4],"") == 0) {
       printf("-c [contraseña] es un parametro obligatorio!\n");
       exit(EXIT_FAILURE);
    }
    if (strcmp(options[1],"$shared/sensors/umcomp2") != 0 && strcmp(options[5],"") == 0) {
       printf("-d [dispositivo_id] es un parametro obligatorio!\n");
       exit(EXIT_FAILURE);
    }

    return options;
}

char **get_opt_sub(int argc, char **argv) {
    static char *options[] = {"localhost", "$shared/sensors/interno", "1883"};
    int opt;
    static char shared_topic[100];
    memset(shared_topic, 0, sizeof(shared_topic));
    char *shared = "$shared/sensors/";
    strncpy(shared_topic, shared, strlen(shared));
    while ((opt = getopt(argc, argv, "h:t:p:")) != -1) {
        switch (opt) {
            case 'h':
                options[0] = optarg;
                break;

            case 't':
                strcat(shared_topic, optarg);
                options[1] = shared_topic;
                break;

            case 'p':
                options[2] = optarg;
                break;

            default: /* '?' */
                fprintf(stderr, "Usage: %s [-h host] [-t topic] [-p port]\n",
                        argv[0]);
                exit(EXIT_FAILURE);
        }
    }
    return options;
}

int get_opt_server(int argc, char **argv) {
    int pvalue;

    int opt;
    opterr = 0;

    while ((opt = getopt(argc, argv, "p:")) != -1) {
        switch (opt) {
            case 'p':
                pvalue = atoi(optarg);
                break;

            case '?':
                if (optopt == 'p')
                    fprintf(stderr, "La opcion -%o requiere un argumento\n",
                            optopt);
                else if (isprint(optopt))  // checkea que un caracter pueda ser
                                           // impreso
                    fprintf(stderr, "Opcion desconocida -%c.\n", optopt);
                break;

            default:
                abort();
        }
    }
    return pvalue;
}