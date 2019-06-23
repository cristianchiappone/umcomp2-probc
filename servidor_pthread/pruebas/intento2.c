#include <pthread.h>
#include <stdio.h>
#include <sys/types.h>
#include <unistd.h>

char logs[50][100];

pthread_mutex_t mut;
void* agregar() {
    int i;
    for (i = 0; i < 50; i++) {
        pthread_mutex_lock(&mut);
        sprintf(logs[i], "Se agrego esto en la posición %d", i);
        pthread_mutex_unlock(&mut);
    }
    pthread_exit(NULL);
}

int main() {
    pthread_mutex_init(&mut, NULL);
    pthread_mutex_unlock(&mut);
    pthread_t hilo;
    pthread_create(&hilo, NULL, agregar, NULL);
    pthread_join(hilo, NULL);
    for (int j = 0; j < 50; j++) {
        printf("Array %d - %s\n", j, logs[j]);
    }
    pthread_exit(NULL);
}