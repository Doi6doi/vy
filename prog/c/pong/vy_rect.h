#ifndef VY_RECTH
#define VY_RECTH
#include <vy.h>

typedef struct Bool * Bool;

typedef struct Rect * Rect;

typedef struct RectFun {
   Rect (* createRect)(float left, float top, float width, float height);
   void (* setColor)(Rect, VyColor);
} RectFun;

#define VYRECTARGS( name ) \
   VyImplemArgs name = vyImplemArgs( "vy.geom.Rect", vyVer(20240301)); \
   vyImplemArgsType( name, "Bool", NULL ); \
   vyImplemArgsType( name, "Color", vyNative("VyColor") ); \
   vyImplemArgsType( name, "Coord", vyNative("float") ); \
   vyImplemArgsType( name, "Rect", NULL ); \
   vyImplemArgsFunc( name, "createRect"); \
   vyImplemArgsFunc( name, "setColor"); \


#endif // VY_RECTH
