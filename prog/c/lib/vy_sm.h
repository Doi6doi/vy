#ifndef VYSTRMAPH
#define VYSTRMAPH

/// kereshető string map

#include "vy.h"
#include "vy_implem.h"

#define VY_NOTFOUND -1

typedef struct VySm {
   unsigned count;
   VyCStr * strs;
   VyPtr * ptrs;
} * VySm;

/// map inicializálás
void vySmInit( VySm map );

/// map hozzadás
unsigned vySmAdd( VySm map, VyCStr str, VyPtr ptr );

/// map keresés
int vySmFind( VySm map, VyCStr str );

/// map kiürítés
void vySmClear( VySm map );
	
#endif // VYSTRMAPH
