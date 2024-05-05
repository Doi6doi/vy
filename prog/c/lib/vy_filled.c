#include <vy_implem.h>
#include "vy_geom.h"
#include "vy_filled.h"

struct Filled {
   VyRepr repr;
};

VyRepr vyrFilled;

void destroyFilled( VyPtr ) {
   vyThrow("stub destroyFilled");
}

extern VyRepr vyrShape;

static void vyFilledSet( Filled *, Filled ) {
   vyThrow("stub FilledSet");
}

static Filled vyFilledCreateFilled(Shape, VyColor ) {
   vyThrow("stub FilledCreateFilled");
}

static Shape vyFilledCastShape( Filled ) {
   vyThrow("stub FilledCastShape");
}

void vyInitFilled( VyContext ctx ) {
   VYFILLEDARGS( ctx, args );
   vyrFilled = vyRepr( sizeof(struct Filled), false, destroyFilled);
   vyArgsType( args, "Filled", vyrFilled );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsImpl( args, "set", vyFilledSet );
   vyArgsImpl( args, "createFilled", vyFilledCreateFilled );
   vyArgsImpl( args, "castShape", vyFilledCastShape );
   vyAddImplem( ctx, args );
}

