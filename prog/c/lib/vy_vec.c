#include "vy_vec.h"
#include <string.h>
#include <stdio.h>


#define MOVEOUT "move out of vector"
#define IDXOUT "index out of vector"

void vyVecMove( VyVec vec, unsigned src, unsigned dst, unsigned n );
void vyVecGrow( VyVec vec, unsigned n );


void vyVecInit( VyVec v, unsigned isize, unsigned step ) {
   vyMemInit( & v->mem, step * isize );
   v->isize = isize;
   v->step = step;
   v->count = 0;
}   
   
VyPtr vyVecAt( VyVec v, unsigned i ) {
   if ( v->count <= i )
      vyThrow( IDXOUT );	
   return ((char *)v->mem.data)+i*v->isize;
}

void vyVecMove( VyVec v, unsigned src, unsigned dst, unsigned n ) {
   if ( !n ) return;
   if ( v->count < src+n || v->count < dst+n )
      vyThrow( MOVEOUT );
   memmove( vyVecAt( v, dst ), vyVecAt( v, src ), n * v->isize );
} 

void vyVecResize( VyVec v, unsigned n, bool resizeMem ) {
   if ( n == v->count ) return;
   unsigned m = n;
   if ( ! resizeMem ) {
	  if ( n < v->count ) {
		 v->count = n;
		 return;
      }
	  if ( n < v->count + v->step )
	     m = v->count + v->step;
   }
   vyMemResize( & v->mem, m * v->isize );
   v->count = n;
}
