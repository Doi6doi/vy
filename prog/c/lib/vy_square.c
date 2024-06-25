#include <vy_implem.h>
#include "vy_square.h"

#include "vy_shape.h"

struct Square {
   struct Shape shape;
};

VyRepr vyrSquare;

Square vycSquare = NULL;

extern VyRepr vyrShape;

void vyDestroySquare( VyPtr ) {
   vyThrow("stub vyDestroySquare");
}

static Square vySquareConstSquare() {
   if ( ! vycSquare ) {
      Square ret = vyAlloc( vyrSquare );
      vyShapeInit( (Shape)ret );
      vySet( (VyAny *) & vycSquare, ret );
   }
   return vycSquare;
}

void vyInitSquare( VyContext ctx ) {
   VYSQUAREARGS( ctx, args );
   vyrSquare = vyRepr( "Square", sizeof(struct Square), vySetRef, vyDestroySquare);
   vyArgsType( args, "Square", vyrSquare );
   vyArgsImpl( args, "constSquare", vySquareConstSquare );
   vyAddImplem( ctx, args );
}

