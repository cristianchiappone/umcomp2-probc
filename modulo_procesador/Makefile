CC = gcc
CFLAGS = -g -Wall
LDLIBS= `mysql_config --cflags --libs`

TARGETS = test

all: $(TARGETS)

test: contar.h mysql_connect.h main.c mysql_connect.c contar.c 
	$(CC) $(CFLAGS) -o $@ $^ $(LDLIBS)

clean:
	rm -f $(TARGETS) *.o *~