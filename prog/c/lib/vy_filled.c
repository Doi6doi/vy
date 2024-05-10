#include <vy_implem.h>
#include "vy_geom.h"
#include "vy_filled.h"

struct Filled {
   VyRepr repr;
};

VyRepr vyrFilled;

extern VyRepr vyrShape;

void vyDestroyFilled( VyPtr ) {
   vyThrow("stub vyDestroyFilled");
}

static void vyFilledSet( Filled *, Filled ) {
   vyThrow("stub vyFilledSet");
}

Shape vyFilledCast( Filled ) {
   vyThrow("stub vyFilledCast");
}

static Filled vyFilledCreateFilled( Shape, VyColor ) {
   vyThrow("stub vyFilledCreateFilled");
}

void vyInitFilled( VyContext ctx ) {
   VYFILLEDARGS( ctx, args );
   vyrFilled = vyRepr( sizeof(struct Filled), false, vyDestroyFilled);
   vyArgsType( args, "Filled", vyrFilled );
   vyArgsImpl( args, "set", vyFilledSet );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsImpl( args, "cast", vyFilledCast );
   vyArgsImpl( args, "createFilled", vyFilledCreateFilled );
}

