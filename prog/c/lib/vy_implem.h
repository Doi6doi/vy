#ifndef VYIMPLEMH
#define VYIMPLEMH

#include "vy.h"

typedef struct VyAny {
   VyRepr repr;
} * VyAny;

typedef struct VyRefCount {
   struct VyAny any;
   unsigned ref;
} * VyRefCount;

/// destruktor
typedef void (* VyDestr)( VyPtr );

/// beállító
typedef void (* VySetter)( VyAny *, VyPtr );

/// modul inicializáló függvény
typedef void (* VyModuleInit)( VyContext );

/// vy modul betöltése
void vyLoadModule( VyContext, VyCStr );

/// saját reprezentáció
VyRepr vyRepr( VyCStr, size_t, VySetter, VyDestr );

/// beállító függvény refcount-ra
void vySetRef( VyAny *, VyPtr );
/// beállító függvény alap
void vySetCustom( VyAny *, VyPtr );

/// objektum készítése
VyPtr vyAlloc( VyRepr );

/// objektum készítése és 0-val feltöltése
VyPtr vyAllocClear( VyRepr );

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
