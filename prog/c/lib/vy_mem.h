#ifndef VYMEMH
#define VYMEMH

#include "vy.h"

typedef struct VyMem {
   VySize size;
   void * data;
} * VyMem;

void vyMemInit( VyMem mem, VySize size );
void vyMemResize( VyMem mem, VySize size );

#endif // VYMEMH

