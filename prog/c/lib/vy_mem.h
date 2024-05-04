#ifndef VYMEMH
#define VYMEMH

typedef struct VyMem {
   unsigned size;
   void * data;
} VyMem;

void vyMemInit( VyMem * mem, unsigned size );


#endif // VYMEMH

