CC = gcc
CFLAGS = -g -Wall
LDLIBS=-pthread
LDFLAGS=

TARGETS = ser_sock

all: $(TARGETS)

ser_sock: server.c arg.c service.c http_parser.c
	$(CC) $(CFLAGS) -o $@ $^ $(LDLIBS) -lmysqlclient

clean:
	rm -f $(TARGETS) *.o *~

