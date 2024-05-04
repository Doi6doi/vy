#include <vy_implem.h>
#include "vy_circle.h"

struct Circle {
   VyRepr repr;
};

VyRepr vyrCircle;

void destroyCircle( VyPtr ) {
   vyThrow("stub destroyCircle");
}

static Circle vyCircleCreateCircle(float radius ) {
   vyThrow("stub CircleCreateCircle");
}

static Shape vyCircleCastShape( Circle ) {
   vyThrow("stub CircleCastShape");
}

void vyInitCircle( VyContext ctx ) {
   VYCIRCLEARGS( ctx, args );
   vyrCircle = vyRepr( sizeof(struct Circle), false, destroyCircle);
   vyArgsType( args, "Circle", vyrCircle );
   vyArgsImpl( args, "createCircle", vyCircleCreateCircle );
   vyArgsImpl( args, "castShape", vyCircleCastShape );
   vyAddImplem( ctx, args );
}

