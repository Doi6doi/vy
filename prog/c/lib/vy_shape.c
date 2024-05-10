#include <vy_implem.h>
#include "vy_shape.h"

struct Shape {
   VyRefCount ref;
};

VyRepr vyrShape;

void destroyShape( VyPtr ) {
   vyThrow("stub destroyShape");
}

static void vyShapeSet( Shape * dest, Shape val ) {
   vySetRef( (VyRefCount *)dest, val->ref );
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyrShape = vyRepr( sizeof(struct Shape), false, destroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyArgsImpl( args, "set", vyShapeSet );
   vyAddImplem( ctx, args );
}

