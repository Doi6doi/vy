#ifndef VY_TIMEH
#define VY_TIMEH
#include <vy.h>

typedef struct Stamp * Stamp;

typedef struct TimeFun {
   Stamp (* stamp)();
   Stamp (* addSecond)(Stamp, float);
   bool (* waitUntil)(Stamp);
} TimeFun;

#define VYTIMEARGS( name ) \
   VyImplemArgs name = vyImplemArgs( "vy.time.Time", vyVer(20240301)); \
   vyImplemArgsType( name, "Bool", vyNative("bool") ); \
   vyImplemArgsType( name, "Number", vyNative("float") ); \
   vyImplemArgsType( name, "Stamp", NULL ); \
   vyImplemArgsFunc( name, "stamp"); \
   vyImplemArgsFunc( name, "addSecond"); \
   vyImplemArgsFunc( name, "waitUntil"); \


#endif // VY_TIMEH
