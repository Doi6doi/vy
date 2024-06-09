#ifndef VY_GROUPH
#define VY_GROUPH

#include <vy.h>

typedef struct Group * Group;

typedef struct View * View;

typedef struct GroupFun {
   void (* add)( Group, View );
   void (* remove)( Group, View );
   float (* coord)( Group, VyViewCoord );
   void (* setCoord)( Group, VyViewCoord, float );
} GroupFun;

#define VYGROUPARGS( ctx, name ) \
   VyArgs name = vyArgs( "vy.ui.Group", vyVer(20240301)); \
   vyArgsType( name, "Bool", vyNative( ctx, "bool" ) ); \
   vyArgsType( name, "ViewCoord", vyNative( ctx, "VyViewCoord" ) ); \
   vyArgsType( name, "Coord", vyNative( ctx, "float" ) ); \
   vyArgsType( name, "Group", NULL ); \
   vyArgsType( name, "Sub", NULL ); \
   vyArgsFunc( name, "add"); \
   vyArgsFunc( name, "remove"); \
   vyArgsFunc( name, "coord"); \
   vyArgsFunc( name, "setCoord"); \

#define VYIMPORTGROUP( ctx, var ) \
   VYGROUPARGS( ctx, var ## Args ); \
   vyFree( vyGetImplem( ctx, var ## Args, & var ));

void vyInitGroup( VyContext );

#endif // VY_GROUPH
