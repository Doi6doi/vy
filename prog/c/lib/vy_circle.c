#include <vy_implem.h>
#include "vy_circle.h"

struct Circle {
   VyRepr repr;
};

VyRepr vyrCircle;

void vyDestroyCircle( VyPtr ) {
   vyThrow("stub vyDestroyCircle");
}

static void vyCircleSet( Circle *, Circle ) {
   vyThrow("stub vyCircleSet");
}

Shape vyCircleCast( Circle ) {
   vyThrow("stub vyCircleCast");
}

static Circle vyCircleCreateCircle( float radius ) {
   vyThrow("stub vyCircleCreateCircle");
}

void vyInitCircle( VyContext ctx ) {
   VYCIRCLEARGS( ctx, args );
   vyrCircle = vyRepr( sizeof(struct Circle), false, vyDestroyCircle);
   vyArgsType( args, "Circle", vyrCircle );
   vyArgsImpl( args, "set", vyCircleSet );
   vyArgsImpl( args, "cast", vyCircleCast );
   vyArgsImpl( args, "createCircle", vyCircleCreateCircle );
   vyAddImplem( ctx, args );
}

