#include "arg.h"
#include "ctype.h"

char **get_opt_pub(int argc, char **argv) {
    // usar malloc
    static char *options[] = {"localhost", "$shared/sensors/test", "1883"};
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

char **get_opt_sub(int argc, char **argv) {
    static char *options[] = {"/pax"};
    int opt;

    while ((opt = getopt(argc, argv, "q:")) != -1) {
        switch (opt) {
            case 'q':
                options[0] = optarg;
                break;

            default: /* '?' */
                fprintf(stderr, "Usage: %s [-q queue_name]\n", argv[0]);
                exit(EXIT_FAILURE);
        }
    }
    return options;
}