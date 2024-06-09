#include "vy_mem.h"
#include <stdlib.h>

#define REALLOC(p,s) realloc( p, s )

void vyMemInit( VyMem mem, unsigned size ) {
   mem->data = REALLOC( NULL, size );
   mem->size  = size;
}

