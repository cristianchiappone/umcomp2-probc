include ./config.mk
CC = gcc
CFLAGS = -g -Wall -Wextra
LDLIBS= `mysql_config --cflags --libs` -lcjson -lrt -pthread ./lib/libmosquitto.so.${SOVERSION}
.PHONY: all

all : interface suscriptor servidor
TARGETS = test

interface: interface.c arg.c validar.c cJSON.c mysql_connect.c publicador.c
	$(CC) -I./lib $(CFLAGS) -o $@ $^ $(LDLIBS)

suscriptor: suscriptor.c arg.c validar.c cJSON.c mysql_connect.c publicador.c
	$(CC) -I./lib $(CFLAGS) -o $@ $^ $(LDLIBS)

servidor:server.c arg.c service.c publicador.c
	$(CC) -I./lib $(CFLAGS) -o $@ $^ $(LDLIBS)
	
clean : 
	rm -f $(TARGETS) *.o *~