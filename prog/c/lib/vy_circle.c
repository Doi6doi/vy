#include <vy_implem.h>
#include "vy_circle.h"

#include "vy_shape.h"

struct Circle {
   struct Shape shape;
};

VyRepr vyrCircle;

extern VyRepr vyrShape;

void vyDestroyCircle( VyPtr ) {
   vyThrow("stub vyDestroyCircle");
}

static Circle vyCircleConstCircle(  ) {
   vyThrow("stub vyCircleConstCircle");
}

void vyInitCircle( VyContext ctx ) {
   VYCIRCLEARGS( ctx, args );
   vyrCircle = vyRepr( "Circle", sizeof(struct Circle), vySetRef, vyDestroyCircle);
   vyArgsType( args, "Circle", vyrCircle );
   vyArgsImpl( args, "constCircle", vyCircleConstCircle );
   vyAddImplem( ctx, args );
}

