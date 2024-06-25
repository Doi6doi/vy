#include <vy_implem.h>
#include "vy_circle.h"

#include "vy_shape.h"

struct Circle {
   struct Shape shape;
};

VyRepr vyrCircle;

Circle vycCircle = NULL;

extern VyRepr vyrShape;

void vyDestroyCircle( VyPtr ) {
   vyThrow("stub vyDestroyCircle");
}

static Circle vyCircleConstCircle() {
   if ( ! vycCircle ) {
      Circle ret = vyAlloc( vyrCircle );
      vyShapeInit( (Shape)ret );
      vySet( (VyAny *) & vycCircle, ret );
   }
   return vycCircle;
}


void vyInitCircle( VyContext ctx ) {
   VYCIRCLEARGS( ctx, args );
   vyrCircle = vyRepr( "Circle", sizeof(struct Circle), vySetRef, vyDestroyCircle);
   vyArgsType( args, "Circle", vyrCircle );
   vyArgsImpl( args, "constCircle", vyCircleConstCircle );
   vyAddImplem( ctx, args );
}

