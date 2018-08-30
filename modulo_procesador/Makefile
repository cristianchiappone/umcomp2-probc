CC = gcc
CFLAGS = -g -Wall
LDLIBS= `mysql_config --cflags --libs`

TARGETS = test

all: $(TARGETS)

test: parsear.h arg.h mysql_connect.h main.c mysql_connect.c arg.c parsear.c 
	$(CC) $(CFLAGS) -o $@ $^ $(LDLIBS)

clean:
	rm -f $(TARGETS) *.o *~