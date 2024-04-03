#ifndef VY_TIMEH
#define VY_TIMEH

typedef struct Stamp * Stamp;

typedef struct Time {
   Stamp (* stamp)();
   Stamp (* addSecond)( Stamp, float );
   bool (*waitUntil)( Stamp );
} Time;

VyImplemArgs timeArgs() {
   VyImplemArgs ret = vyImplemArgs( "vy.time.Time", vyVer( 20240301 ));
   vyImplemArgsType( ret, "Stamp", NULL );
   vyImplemArgsType( ret, "Num", vyNative("float"));
   vyImplemArgsType( ret, "Bool", vyNative("bool"));
   vyImplemArgsFunc( ret, "stamp" );
   vyImplemArgsFunc( ret, "addSecond" );
   vyImplemArgsFunc( ret, "waitUntil" );
   return ret;
}


#endif // VY_TIMEH
