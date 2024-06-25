#include <vy_implem.h>
#include "vy_filled.h"
#include "vy_shape.h"

struct Filled {
   struct Shape shape;
   VyColor color;
   Shape sub;
};

VyRepr vyrFilled;

extern VyRepr vyrShape;

extern VyRepr vyrShape;

void vyDestroyFilled( VyPtr ) {
   vyThrow("stub vyDestroyFilled");
}

static Filled vyFilledCreateFilled( Shape sub, VyColor color) {
   Filled ret = vyAllocClear( vyrFilled );
   vyShapeInit( (Shape)ret );
   vySet( (VyAny *)&ret->sub, sub );
   ret->color = color;
   return ret;
}

static Shape vyFilledShape( Filled f ) {
   return f->sub;
}

static VyColor vyFilledBrush( Filled ) {
   vyThrow("stub vyFilledBrush");
}

void vyInitFilled( VyContext ctx ) {
   VYFILLEDARGS( ctx, args );
   vyrFilled = vyRepr( "Filled", sizeof(struct Filled), vySetRef, vyDestroyFilled);
   vyArgsType( args, "Filled", vyrFilled );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsType( args, "Brush", vyNative(ctx,"VyColor") );
   vyArgsImpl( args, "createFilled", vyFilledCreateFilled );
   vyArgsImpl( args, "shape", vyFilledShape );
   vyArgsImpl( args, "brush", vyFilledBrush );
   vyAddImplem( ctx, args );
}

