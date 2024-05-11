#include <vy_implem.h>
#include "vy_shape.h"

struct Shape {
   VyRefCount ref;
};

VyRepr vyrShape;

void vyDestroyShape( VyPtr ) {
   vyThrow("stub vyDestroyShape");
}

static void vyShapeSet( Shape * dst , Shape val ) {
   vySetter( (VyAny *)dst, (VyAny)val );
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyrShape = vyRepr( sizeof(struct Shape), false, vyDestroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyArgsImpl( args, "set", vyShapeSet );
   vyAddImplem( ctx, args );
}

