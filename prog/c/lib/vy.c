#include "vy.h"
#include <stdlib.h>

#define VYMEM( p, t, n ) ((t *)realloc( p, sizeof(t)*n ))

struct Vy {
};

Vy vyCreate() {
   Vy ret = VYMEM( NULL, struct Vy, 1 );
}

void vyDestroy( Vy vy ) {
   vy = VYMEM( vy, struct Vy, 0 );
}
