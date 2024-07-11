#include <vy_implem.h>
#include "vy_shape.h"

VyRepr vyrShape;

void vyShapeInit( Shape s ) {
   vyRefInit( (VyRefCount)s );
}

void vyDestroyShape( VyPtr p ) {
   p = p;
   vyThrow("stub vyDestroyShape");
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyrShape = vyRepr( "Shape", sizeof(struct Shape), vySetRef, vyDestroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyAddImplem( ctx, args );
}

