#include <vy_implem.h>
#include "vy_square.h"

#include "vy_shape.h"

struct Square {
   struct Shape shape;
};

VyRepr vyrSquare;

extern VyRepr vyrShape;

void vyDestroySquare( VyPtr ) {
   vyThrow("stub vyDestroySquare");
}

static Square vySquareConstSquare(  ) {
   vyThrow("stub vySquareConstSquare");
}

void vyInitSquare( VyContext ctx ) {
   VYSQUAREARGS( ctx, args );
   vyrSquare = vyRepr( "Square", sizeof(struct Square), vySetRef, vyDestroySquare);
   vyArgsType( args, "Square", vyrSquare );
   vyArgsImpl( args, "constSquare", vySquareConstSquare );
   vyAddImplem( ctx, args );
}

