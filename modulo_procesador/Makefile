CC = gcc
CFLAGS = -g -Wall
LDLIBS= `mysql_config --cflags --libs`

TARGETS = test

all: $(TARGETS)

test: main.c mysql_connect.c arg.c parsear.c 
	$(CC) $(CFLAGS) -o $@ $^ $(LDLIBS)

clean:
	rm -f $(TARGETS) *.o *~