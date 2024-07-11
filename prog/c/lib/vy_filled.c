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

void vyDestroyFilled( VyPtr p ) {
   p = p;
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

static VyColor vyFilledBrush( Filled f ) {
   f = f;
   vyThrow("stub vyFilledBrush");
   return 0;
}

void vyInitFilled( VyContext ctx ) {
   VYFILLEDARGS( ctx, args );
   vyrFilled = vyRepr( "Filled", sizeof(struct Filled), vySetRef, vyDestroyFilled);
   vyArgsType( args, "Filled", vyrFilled );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsType( args, "Brush", vyNative(ctx,"VyColor") );
   vyArgsImpl( args, "createFilled", (VyPtr)vyFilledCreateFilled );
   vyArgsImpl( args, "shape", (VyPtr)vyFilledShape );
   vyArgsImpl( args, "brush", (VyPtr)vyFilledBrush );
   vyAddImplem( ctx, args );
}

