include "error.h"

const char* modulo_strerror(int id_errno) {
    switch (id_errno) {
        case MOD_ERR_PUB_SERVER_UP:
            return "No se pudo crear la cola de mensajes -> "
                   "publicador.c";
        case MOD_ERR_PUB_FORK_SUB:
            return "No se pudo crear el proceso suscroiptor -> "
                   "publicador.c";
        case MOD_ERR_PUB_EXEC_SUB:
            return "No se pudo realizar la llamada exec -> publicador.c";
        case MOD_ERR_PUB_CREATE_CHILD_SEND:
            return "No se pudo crear el proceso hijo publicador -> "
                   "publicador.c";
        default:
            return "Error desconocido.";
    }
}