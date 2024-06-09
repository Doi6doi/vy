#include <vy_implem.h>
#include "vy_rect.h"

#include "vy_shape.h"

struct Rect {
   struct Shape shape;
   float width;
   float height;
};

VyRepr vyrRect;

extern VyRepr vyrShape;

void vyDestroyRect( VyPtr ) {
   vyThrow("stub vyDestroyRect");
}

Shape vyRectCast( Rect x ) { return (Shape)x; }

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
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyrRect = vyRepr( sizeof(struct Rect), vySetRef, vyDestroyRect);
   vyArgsType( args, "Rect", vyrRect );
   vyArgsImpl( args, "createRect", vyRectCreateRect );
   vyArgsImpl( args, "width", vyRectWidth );
   vyArgsImpl( args, "height", vyRectHeight );
   vyAddImplem( ctx, args );
}

