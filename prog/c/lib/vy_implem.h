#ifndef VYIMPLEMH
#define VYIMPLEMH

#include <vy.h>

typedef struct VyAny {
   VyRepr repr;
} * VyAny;

typedef struct VyRefCount {
   struct VyAny any;
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
VyRepr vyRepr( VyCStr, size_t, VySetter, VyDestr );

/// beállító függvény bármire
void vySet( VyAny *, VyAny );
/// beállító függvény refcount-ra
void vySetRef( VyAny *, VyAny );
/// beállító függvény alap
void vySetCustom( VyAny *, VyAny );

/// objektum készítése
VyPtr vyAlloc( VyRepr );

/// refcount objektum inicializálás
void vyRefInit( VyRefCount );

/// egy implementáció megadása
void vyArgsImpl( VyArgs, VyCStr, VyPtr );

/// implementáció hozzáadása
void vyAddImplem( VyContext, VyArgs );

/// natív reprezentáció hozzáadása
VyRepr vyAddNative( VyContext, VyCStr name, size_t size );

/// reprezentáció dump
void vyDumpRepr( VyRepr );

extern char *VYNOMEM;

#endif // VYIMPLEMH
