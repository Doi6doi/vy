#include <vy_implem.h>
#include "vy_geom.h"
#include "vy_filled.h"

struct Filled {
   VyRepr repr;
};

VyRepr vyrFilled;

extern VyRepr vyrShape;

void destroyFilled( VyPtr ) {
   vyThrow("stub destroyFilled");
}

static Filled vyFilledCreateFilled(Shape, VyColor ) {
   vyThrow("stub FilledCreateFilled");
}

void vyInitFilled( VyContext ctx ) {
   VYFILLEDARGS( ctx, args );
   vyrFilled = vyRepr( sizeof(struct Filled), false, destroyFilled);
   vyArgsType( args, "Filled", vyrFilled );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsImpl( args, "createFilled", vyFilledCreateFilled );
   vyAddImplem( ctx, args );
}

