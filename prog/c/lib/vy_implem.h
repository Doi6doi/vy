#ifndef VYIMPLEMH
#define VYIMPLEMH

#include <vy.h>

/// mutató
typedef void * VyPtr;

/// destruktor
typedef void (* VyDestr)( VyPtr );

/// modul inicializáló függvény
typedef void (* VyModuleInit)( VyContext );

/// vy modul betöltése
void vyLoadModule( VyContext, VyCStr );

/// saját reprezentáció
VyRepr vyRepr( size_t size, bool , VyDestr );

/// objektum készítése
VyPtr vyAlloc( VyRepr r );

/// egy implementáció megadása
void vyArgsImpl( VyArgs, VyCStr, VyPtr );

/// implementáció hozzáadása
void vyAddImplem( VyContext, VyArgs );

/// natív reprezentáció hozzáadása
VyRepr vyAddNative( VyContext, VyCStr name, size_t size );

extern char *VYNOMEM;

#endif // VYIMPLEMH
