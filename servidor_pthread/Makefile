CC = gcc
CFLAGS = -g -Wall
LDLIBS=-pthread
LDFLAGS=

TARGETS = ser_sock

all: $(TARGETS)

ser_sock: server.c arg.c 
	$(CC) $(CFLAGS) -o $@ $^ $(LDLIBS) 

clean:
	rm -f $(TARGETS) *.o *~

