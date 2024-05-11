VYSDL=vysdl
IDIR=../lib
LDIR=../lib
VDIR=../../..
VYC=php ../../php/vyc/vyc.php
LIBS=SDL2 vy
ITEMS=key window view sprite
CC=gcc -g $(IDIR:%=-I%) $(LDIR:%=-L%)
RM=rm -f

build: lib$(VYSDL).so

clean:
	$(RM) *.o

head:
	$(MAKE) build 2>&1 | head -n 20

lib$(VYSDL).so: vysdl.o $(ITEMS:%=%.o)
	$(CC) -shared -o $@ $^ $(LIBS:%=-l%)

libvy:
	$(MAKE) -C ../lib

pong:
	$(MAKE) -C ../pong

vysdl.o: vysdl.c vysdl.h

%.o: %.c
	$(CC) -fPIC -c -o $@ $<

key.c: $(VDIR)/vy/ui/Key@20240301.vy
	$(VYC) -i vy.ui.Key@=20240301 -r $(VDIR) \
	-t 'Bool=&bool;Key=&VyKey' -o $@

window.c: $(VDIR)/vy/ui/Window@20240301.vy
	$(VYC) -i vy.ui.Window@=20240301 -r $(VDIR) \
	-t 'Bool=&bool;Coord=&float;ViewCoord=&VyViewCoord;Sub=^View' -o $@

view.c: $(VDIR)/vy/ui/View@20240301.vy
	$(VYC) -i vy.ui.View@=20240301 -r $(VDIR) \
	-t 'Bool=&bool;Coord=&float;ViewCoord=&VyViewCoord;' -o $@

sprite.c: $(VDIR)/vy/ui/Sprite@20240301.vy
	$(VYC) -f -i vy.ui.Sprite@=20240301 -r $(VDIR) \
	-t 'Bool=&bool;Coord=&float;ViewCoord=&VyViewCoord;Shape=^Shape;Bounds=^Bounds' \
	-s 'Sprite=View' -o $@

