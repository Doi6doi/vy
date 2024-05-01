#ifndef VY_CAPTIONH
#define VY_CAPTIONH
#include <vy.h>

typedef struct String * String;

typedef struct Caption * Caption;

typedef struct CaptionFun {
   Caption (* createCaption)(String);
} CaptionFun;

#define VYCAPTIONARGS( ctx, name ) \
   VyArgs name = vyArgs( "vy.geom.Caption", vyVer(20240301)); \
   vyArgsType( name, "Bool", vyNative( ctx, "bool" ) ); \
   vyArgsType( name, "Char", vyNative( ctx, "wchar_t" ) ); \
   vyArgsType( name, "String", NULL ); \
   vyArgsType( name, "Coord", vyNative( ctx, "float" ) ); \
   vyArgsType( name, "Caption", NULL ); \
   vyArgsFunc( name, "createCaption"); \

#define VYIMPORTCAPTION( ctx, var ) \
   VYCAPTIONARGS( ctx, var ## Args ); \
   vyFree( vyGetImplem( ctx, var ## Args, & var )); \

void vyInitCaption( VyContext );


#endif // VY_CAPTIONH
