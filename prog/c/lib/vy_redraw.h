#ifndef VY_REDRAWH
#define VY_REDRAWH

#include <vy.h>

typedef struct Redraw * Redraw;

typedef struct RedrawFun {
   void (* redraw)( Redraw );
   void (* refesh)( Redraw );
} RedrawFun;

#define VYREDRAWARGS( ctx, name ) \
   VyArgs name = vyArgs( "vy.ui.Redraw", vyVer(24)); \
   vyArgsType( name, "Redraw", NULL ); \
   vyArgsFunc( name, "redraw"); \
   vyArgsFunc( name, "refesh"); \

#define VYIMPORTREDRAW( ctx, var ) \
   VYREDRAWARGS( ctx, var ## Args ); \
   vyFree( vyGetImplem( ctx, var ## Args, & var ));

void vyInitRedraw( VyContext );

#endif // VY_REDRAWH
