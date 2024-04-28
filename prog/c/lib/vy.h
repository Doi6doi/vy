#ifndef VYH
#define VYH

#include <stdbool.h>
#include <stdlib.h>

/// konstansoknál strlen
#define VY_COUNT -1

/// méret
typedef size_t VySize;

/// konstans szöveg
typedef char * VyCStr;

/// vy modul
typedef struct Vy * Vy;

/// verzió
typedef unsigned VyVer;

/// reprezentáció
typedef struct VyRepr * VyRepr;

/// környezet
typedef struct VyContext * VyContext;

/// implementáció argumentumok
typedef struct VyImplemArgs * VyImplemArgs;

/// új vy rendszer
Vy vyInit();
/// aktuális kontextus
VyContext vyContext( Vy );
/// natív típus reprezentációja
VyRepr vyNative( VyCStr );
/// verzió érték
VyVer vyVer( unsigned );
/// egy objektum megsemmisítése
void vyFree( void * );

/// kivétel
void vyThrow( VyCStr );

/// implementációs argumentumok készítése
VyImplemArgs vyImplemArgs( VyCStr, VyVer );
/// egy típus megadása
void vyImplemArgsType( VyImplemArgs, VyCStr, VyRepr );
/// egy művelet nevének megadása
void vyImplemArgsFunc( VyImplemArgs, VyCStr );
/// reprezentáció lekérése
VyRepr vyGetImplemRepr( VyImplemArgs, VyCStr );
/// implementáció kérése
VyImplemArgs vyGetImplem( VyContext ctx, VyImplemArgs args, void * dest );


#endif // VYH
