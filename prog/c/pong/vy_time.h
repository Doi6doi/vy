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
   VyArgs name = vyArgs( "vy.time.Time", vyVer(20240301)); \
   vyArgsType( name, "Bool", vyNative("bool") ); \
   vyArgsType( name, "Number", vyNative("float") ); \
   vyArgsType( name, "Stamp", NULL ); \
   vyArgsFunc( name, "stamp"); \
   vyArgsFunc( name, "addSecond"); \
   vyArgsFunc( name, "waitUntil"); \

void vyInitTime( VyContext );


#endif // VY_TIMEH
