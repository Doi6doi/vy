#ifndef VYH
#define VYH

#include <stdbool.h>
#include <stdlib.h>

/// konstansoknál strlen
#define VY_LEN -1

extern char *VYNOMEM;

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

/// valamilyen objektum
typedef struct VyAny * VyAny;

/// környezet
typedef struct VyContext * VyContext;

/// implementáció argumentumok
typedef struct VyArgs * VyArgs;

/// új vy rendszer
Vy vyInit();
/// aktuális kontextus
VyContext vyContext( Vy );
/// natív típus reprezentációja
VyRepr vyNative( VyContext, VyCStr );
/// verzió érték
VyVer vyVer( unsigned );
/// egy objektum megsemmisítése
void vyFree( void * );

/// értékadás (refcount, managed)
void vySet( VyAny *, VyAny );

/// kivétel
void vyThrow( VyCStr );

/// implementációs argumentumok készítése
VyArgs vyArgs( VyCStr, VyVer );
/// egy típus megadása
void vyArgsType( VyArgs, VyCStr, VyRepr );
/// egy művelet nevének megadása
void vyArgsFunc( VyArgs, VyCStr );
/// reprezentáció lekérése
VyRepr vyGetRepr( VyArgs, VyCStr );
/// implementáció kérése
VyArgs vyGetImplem( VyContext ctx, VyArgs args, void * dest );


#endif // VYH
