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

static float vyCircleRadius( Circle ) {
   vyThrow("stub vyCircleRadius");
}

void vyInitCircle( VyContext ctx ) {
   VYCIRCLEARGS( ctx, args );
   vyrCircle = vyRepr( "Circle", sizeof(struct Circle), false, vyDestroyCircle);
   vyArgsType( args, "Circle", vyrCircle );
   vyArgsImpl( args, "createCircle", vyCircleCreateCircle );
   vyArgsImpl( args, "radius", vyCircleRadius );
   vyAddImplem( ctx, args );
}

