#ifndef VY_RECTH
#define VY_RECTH
#include <vy.h>

typedef struct Bool * Bool;

typedef struct Rect * Rect;

typedef struct RectFun {
   Rect (* createRect)(float left, float top, float width, float height);
} RectFun;

#define VYRECTARGS( name ) \
   VyArgs name = vyArgs( "vy.geom.Rect", vyVer(20240301)); \
   vyArgsType( name, "Bool", NULL ); \
   vyArgsType( name, "Coord", vyNative("float") ); \
   vyArgsType( name, "Rect", NULL ); \
   vyArgsFunc( name, "createRect"); \

void vyInitRect( VyContext );


#endif // VY_RECTH
