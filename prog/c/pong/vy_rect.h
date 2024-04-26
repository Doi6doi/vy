#ifndef VY_RECTH
#define VY_RECTH
#include <vy.h>

typedef struct Bool * Bool;

typedef struct RectFun {
} * RectFun;

#define VYRECTARGS( name ) \
   VyImplemArgs name = vyImplemArgs( ".Rect", vyVer(20240301)); \
   vyImplemArgsType( name, "Bool", NULL ); \
   vyImplemArgsType( name, "Coord", vyNative("float") ); \


#endif // VY_RECTH
