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
VyPtr vyVecGet( VyVec vec, unsigned i );
void vyVecAdd( VyVec vec, VyPtr item );

#endif // VYVECH

