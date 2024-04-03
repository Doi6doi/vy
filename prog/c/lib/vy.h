#ifndef VYH
#define VYH

#include <stdlib.h>

/// konstans szöveg
typedef char * VyCStr;

/// vy modul
typedef struct Vy * Vy;

/// verzió
typedef int VyVer;

/// reprezentáció
typedef struct VyRepr * VyRepr;

/// környezet
typedef struct VyContext * VyContext;

/// implementáció argumentumok
typedef struct VyImplemArgs * VyImplemArgs;

/// új vy rendszer
Vy vyInit();
/// vy rendszer vége
void vyDone( Vy );
/// aktuális kontextus
VyContext vyContext( Vy );
/// natív típus reprezentációja
VyRepr vyNative( VyCStr );
/// verzió érték
VyVer vyVer( unsigned );
/// egy objektum megsemmisítése
void vyDestroy( void * );
/// implementációs argumentumok készítése
VyImplemArgs vyImplemArgs( VyCStr, VyVer );
/// egy típus megadása
void vyImplemArgsType( VyImplemArgs, VyCStr, VyRepr );
/// egy művelet nevének megadása
void vyImplemArgsFunc( VyImplemArgs, VyCStr );
/// reprezentáció lekérése
VyRepr vyGetImplemRepr( VyImplemArgs, VyCStr );
/// implementáció kérése
void vyGetImplem( VyContext, VyImplemArgs, void * );

#endif // VYH
