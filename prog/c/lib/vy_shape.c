#include <vy_implem.h>
#include "vy_shape.h"

struct Shape {
   VyRepr repr;
};

VyRepr vyrShape;

void destroyShape( VyPtr ) {
   vyThrow("stub destroyShape");
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyrShape = vyRepr( sizeof(struct Shape), false, destroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyAddImplem( ctx, args );
}

