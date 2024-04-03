#ifndef VYH
#define VYH

#include <stdlib.h>

/// konstans szöveg
typedef char * VyCStr;

/// vy modul
typedef struct Vy * Vy;

/// tetszőleges típus
typedef struct {} * VyType;

/// verzió
typedef int VyVer;

/// függvény típus
typedef void (* VyProc)();
typedef void (* VyProc1)(VyType);
typedef VyType (* VyFunc1)(VyType);

/// reprezentáció
typedef struct VyRepr * VyRepr;
/// környezet
typedef struct VyContext * VyContext;
/// implementáció argumentumok
typedef struct VyImplemArgs * VyImplemArgs;

/// új vy rendszer
Vy vyCreate();
/// vy rendszer vége
void vyDestroy( Vy );
/// aktuális kontextus
VyContext vyContext( Vy );
/// natív típus reprezentációja
VyRepr vyNative( VyCStr );
/// verzió érték
VyVer vyVer( unsigned );
/// egy objektum megsemmisítése
void vyFree( VyType );
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
