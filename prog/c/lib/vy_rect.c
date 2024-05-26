#include <vy_implem.h>
#include "vy_rect.h"

#include "vy_shape.h"

struct Rect {
   struct Shape shape;
};

extern VyRepr vyrShape;

VyRepr vyrRect;

Shape vyRectCast( Rect ) {
   vyThrow("stub vyRectCast");
}

static Rect vyRectCreateRect( float width, float height ) {
   vyThrow("stub vyRectCreateRect");
}

static float vyRectWidth( Rect ) {
   vyThrow("stub vyRectWidth");
}

static float vyRectHeight( Rect ) {
   vyThrow("stub vyRectHeight");
}

void vyInitRect( VyContext ctx ) {
   VYRECTARGS( ctx, args );
   vyArgsType( args, "Rect", vyrShape );
   vyArgsImpl( args, "cast", vyRectCast );
   vyArgsImpl( args, "createRect", vyRectCreateRect );
   vyArgsImpl( args, "width", vyRectWidth );
   vyArgsImpl( args, "height", vyRectHeight );
   vyAddImplem( ctx, args );
}

