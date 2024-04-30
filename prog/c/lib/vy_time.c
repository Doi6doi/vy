#include <vy_implem.h>
#include "vy_time.h"

struct Stamp {
   VyRepr repr;
};

VyRepr vyrStamp;

void destroyStamp( VyPtr ) {
   vyThrow("stub destroyStamp");
}

static Stamp vyTimeStamp( ) {
   vyThrow("stub TimeStamp");
}

static Stamp vyTimeAddSecond(Stamp, float ) {
   vyThrow("stub TimeAddSecond");
}

static bool vyTimeWaitUntil(Stamp ) {
   vyThrow("stub TimeWaitUntil");
}

void vyInitTime( VyContext ctx ) {
   VYTIMEARGS( ctx, args );
   vyrStamp = vyRepr( sizeof(struct Stamp), false, destroyStamp);
   vyArgsType( args, "Stamp", vyrStamp );
   vyArgsImpl( args, "stamp", vyTimeStamp );
   vyArgsImpl( args, "addSecond", vyTimeAddSecond );
   vyArgsImpl( args, "waitUntil", vyTimeWaitUntil );
   vyAddImplem( ctx, args );
}

