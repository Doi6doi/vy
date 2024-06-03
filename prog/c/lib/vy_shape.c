#include <vy_implem.h>
#include "vy_shape.h"

VyRepr vyrShape;

void vyDestroyShape( VyPtr ) {
   vyThrow("stub vyDestroyShape");
}

static void vyShapeSet( Shape * dest, Shape val ) {
   vySetter( (VyAny *)dest, (VyAny)val );
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyrShape = vyRepr( sizeof(struct Shape), false, vyDestroyShape);
   vyArgsImpl( args, "set", vyShapeSet );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyAddImplem( ctx, args );
}

