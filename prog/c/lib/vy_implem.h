#ifndef VYIMPLEMH
#define VYIMPLEMH

#include <vy.h>

/// mutató
typedef void * VyPtr;

/// destruktor
typedef void (* VyDestr)( VyPtr );

/// saját reprezentáció
VyRepr vyRepr( size_t, bool, VyDestr );

/// objektum készítése
VyPtr vyAlloc( VyRepr r );

/// egy implementáció megadása
void vyImplemArgsImpl( VyImplemArgs, VyCStr, VyPtr );

/// implementáció hozzáadása
void vyAddImplem( VyContext, VyImplemArgs );

extern char *VYNOMEM;

#endif // VYIMPLEMH
