#include "vy_mem.h"
#include "vy.h"
#include <stdlib.h>

#define REALLOC(p,s) realloc( p, s )

void vyMemInit( VyMem mem, VySize size ) {
   mem->data = REALLOC( NULL, size );
   mem->size  = size;
}

void vyMemResize( VyMem mem, VySize size ) {
   void * ndata = REALLOC( mem->data, size );
   if ( size && ! ndata )
      vyThrow( VYNOMEM );
   mem->data = ndata;
   mem->size = size;
}

