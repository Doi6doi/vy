#ifndef VYVECH
#define VYVECH

#include "vy.h"
#include "vy_mem.h"

typedef struct VyVec {
   struct VyMem mem;
   unsigned isize;
   unsigned step;
   unsigned count;
} * VyVec;

void vyVecInit( VyVec vec, unsigned isize, unsigned step );
VyPtr vyVecAt( VyVec vec, unsigned i );
void vyVecMove( VyVec vec, unsigned src, unsigned dst, unsigned n );
void vyVecResize( VyVec vec, unsigned n, bool resizeMem );

#endif // VYVECH

