#include <vy_implem.h>
#include "vy_time.h"
#include "vy_arch.h"

static VyStamp vyTimeStamp( ) {
   return vyaTimeStamp();
}

static VyStamp vyTimeAddSecond(VyStamp s, float i) {
   return vyaTimeAddSecond( s, i );
}

static bool vyTimeWaitUntil(VyStamp s) {
   return vyaTimeWaitUntil( s );
}

void vyInitTime( VyContext ctx ) {
   vyAddNative( ctx, "VyStamp", sizeof( VyStamp ));
   VYTIMEARGS( ctx, args );
   vyArgsImpl( args, "stamp", vyTimeStamp );
   vyArgsImpl( args, "addSecond", vyTimeAddSecond );
   vyArgsImpl( args, "waitUntil", vyTimeWaitUntil );
   vyAddImplem( ctx, args );
}

