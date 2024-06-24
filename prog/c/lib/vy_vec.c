#include "vy_vec.h"
#include <string.h>
#include <stdio.h>

void vyVecInit( VyVec v, unsigned isize, unsigned step ) {
   vyMemInit( & v->mem, step * isize );
   v->isize = isize;
   v->step = step;
   v->count = 0;
}   
   
VyPtr vyVecGet( VyVec v, unsigned i ) {
   return ((char *)v->mem.data)+i*v->isize;
}

void vyVecAdd( VyVec v, VyPtr item ) {
   if ( v->count * v->isize == v->mem.size )
      vyMemResize( & v->mem, v->isize * ( v->count+v->step ));
   memmove( vyVecGet( v, v->count ), item, v->isize );
   ++v->count;
}   
