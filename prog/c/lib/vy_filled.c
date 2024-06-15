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

static Shape vyFilledShape( Filled ) {
   vyThrow("stub vyFilledShape");
}

static VyColor vyFilledBrush( Filled ) {
   vyThrow("stub vyFilledBrush");
}

void vyInitFilled( VyContext ctx ) {
   VYFILLEDARGS( ctx, args );
   vyrFilled = vyRepr( "Filled", sizeof(struct Filled), false, vyDestroyFilled);
   vyArgsType( args, "Filled", vyrFilled );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsImpl( args, "createFilled", vyFilledCreateFilled );
   vyArgsImpl( args, "shape", vyFilledShape );
   vyArgsImpl( args, "brush", vyFilledBrush );
   vyAddImplem( ctx, args );
}

