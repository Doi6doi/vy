#ifndef VYH
#define VYH

/// vy modul
typedef struct Vy * Vy;

/// tetszőleges típus
typedef struct {} * VyType;

/// konstans szöveg
typedef char * VyCStr;

/// verzió
typedef int VyVer;

/// függvény típus
typedef VyType (* VyFunc0)();
typedef VyType (* VyFunc1)(VyType);

/// natív típus
typedef enum VyNative { VN_UNKNOWN, VN_BOOL, VN_INT, VN_FLOAT, VN_FUNC } VyNative;

/// reprezentáció
typedef struct VyRepr * VyRepr;

/// implementációs argumanetumok
typedef struct VyImplemArgs {
   // az implementáció neve
   VyCStr name;
   // legkésőbbi verzió
   VyVer ver;
   // megadott típusok száma
   unsigned ntypes;
   // megadott típusok
   VyCStr * types;
   // megadott reprezentációk
   VyRepr * reprs;
   /// megadott föggvények száma
   unsigned nfuncs;
   // megadott függyvények
   VyCStr * funcs;
} VyImplemArgs;

/// új vy rendszer
Vy vyCreate();
/// vy rendszer vége
void vyDestroy( Vy );
/// natív típus reprezentációja
VyRepr vyNative( Vy, VyNative );
/// verzió érték
VyVer vyVer( unsigned );
/// implementáció kérése
void vyGetImplem( Vy, VyImplemArgs *, void * );

#endif // VYH
