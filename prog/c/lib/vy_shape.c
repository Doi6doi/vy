#include <vy_implem.h>
#include "vy_shape.h"

VyRepr vyrShape;

void vyShapeInit( Shape s ) {
   vyRefInit( (VyRefCount)s );
}

void vyDestroyShape( VyPtr ) {
   vyThrow("stub vyDestroyShape");
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyrShape = vyRepr( sizeof(struct Shape), vySetRef, vyDestroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyAddImplem( ctx, args );
}

