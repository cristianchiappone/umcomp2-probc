# CC = gcc
# CFLAGS = -g -Wall
# LDLIBS= `mysql_config --cflags --libs` -lrt

# TARGETS = test

# all: $(TARGETS)

# test: main.c queue.c mysql_connect.c arg.c parsear.c mosquitto.c 
# 	$(CC) $(CFLAGS) -o $@ $^ $(LDLIBS)

# clean:
# 	rm -f $(TARGETS) *.o *~


include ./config.mk
CC = gcc
CFLAGS = -g -Wall
LDLIBS= `mysql_config --cflags --libs` -lrt ./lib/libmosquitto.so.${SOVERSION}
.PHONY: all

# all : sub_single_juan test
all : publicador suscriptor
TARGETS = test

# sub_single_juan : single_juan.o
# 	${CROSS_COMPILE}${CC} $^ -o $@ ./lib/libmosquitto.so.${SOVERSION}

# single_juan.o : single_juan.c ./lib/libmosquitto.so.${SOVERSION}
# 	${CROSS_COMPILE}${CC} -c $< -o $@ -I./lib ${CFLAGS}

publicador: publicador.c queue.c arg.c
	$(CC) -I./lib $(CFLAGS) -o $@ $^ $(LDLIBS)

suscriptor: suscriptor.c queue.c arg.c parsear.c
	$(CC) -I./lib $(CFLAGS) -o $@ $^ $(LDLIBS)

# ./lib/libmosquitto.so.${SOVERSION} :
# 	$(MAKE) -C ./lib

clean : 
	# -rm -f *.o sub_single_juan
	rm -f $(TARGETS) *.o *~