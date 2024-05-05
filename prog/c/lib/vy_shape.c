#include <vy_implem.h>
#include "vy_shape.h"

struct Shape {
   VyRepr repr;
};

VyRepr vyrShape;

void destroyShape( VyPtr ) {
   vyThrow("stub destroyShape");
}

static void vyShapeSet( Shape *, Shape ) {
   vyThrow("stub ShapeSet");
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyrShape = vyRepr( sizeof(struct Shape), false, destroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyArgsImpl( args, "set", vyShapeSet );
   vyAddImplem( ctx, args );
}

