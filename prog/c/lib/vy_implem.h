#ifndef VYIMPLEMH
#define VYIMPLEMH

#include <vy.h>

typedef struct VyRefCount {
   VyRepr repr;
   unsigned ref;
} * VyRefCount;

/// mutató
typedef void * VyPtr;

/// destruktor
typedef void (* VyDestr)( VyPtr );

/// beállító
typedef void (* VySetter)( VyAny *, VyAny );

/// modul inicializáló függvény
typedef void (* VyModuleInit)( VyContext );

/// vy modul betöltése
void vyLoadModule( VyContext, VyCStr );

/// saját reprezentáció
VyRepr vyRepr( size_t, VySetter, VyDestr );

/// objektum készítése
VyPtr vyAlloc( VyRepr );

/// refcount objektum készítése
VyPtr vyAllocRef( VyRepr );

void vySetter( VyAny *, VyAny );

/// egy implementáció megadása
void vyArgsImpl( VyArgs, VyCStr, VyPtr );

/// implementáció hozzáadása
void vyAddImplem( VyContext, VyArgs );

/// natív reprezentáció hozzáadása
VyRepr vyAddNative( VyContext, VyCStr name, size_t size );

extern char *VYNOMEM;

#endif // VYIMPLEMH
