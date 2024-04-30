#include "vy_sm.h"
#include <string.h>

#define REALLOC(p,s) realloc( p, s)

#define NOSTR "Missing string for map"

void vySmInit( VySm * map ) {
   map->count = 0;
   map->strs = NULL;
   map->ptrs = NULL;
}

unsigned vySmAdd( VySm * map, VyCStr str, VyPtr ptr ) {
   if ( ! str )
      vyThrow( NOSTR ); 
   unsigned n = map->count+1;
   VyCStr * strs = REALLOC( map->strs, n*sizeof(VyCStr) );
   if ( ! strs ) vyThrow( VYNOMEM );
   VyPtr * ptrs = REALLOC( map->ptrs, n*sizeof(VyPtr) );
   if ( ! ptrs ) vyThrow( VYNOMEM );
   strs[n-1] = str;
   ptrs[n-1] = ptr;
   map->strs = strs;
   map->ptrs = ptrs;
   map->count = n;
   return n-1;
}

void vySmClear( VySm * map ) {
   map->strs = REALLOC( map->strs, 0 );
   map->ptrs = REALLOC( map->ptrs, 0 );
   map->count = 0;
}

int vySmFind( VySm * map, VyCStr str ) {
   if ( ! str ) return VY_NOTFOUND;
   for (int i=0; i<map->count; ++i) {
      if ( 0 == strcmp( str, map->strs[i] ))
         return i;
   }
   return VY_NOTFOUND;
}
