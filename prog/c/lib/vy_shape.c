#include <vy_implem.h>
#include "vy_shape.h"

VyRepr vyrShape;

void vyDestroyShape( VyPtr ) {
   vyThrow("stub vyDestroyShape");
}

void vyInitShape( VyContext ctx ) {
   VYSHAPEARGS( ctx, args );
   vyrShape = vyRepr( "Shape", sizeof(struct Shape), vySetRef, vyDestroyShape);
   vyArgsType( args, "Shape", vyrShape );
   vyAddImplem( ctx, args );
}

