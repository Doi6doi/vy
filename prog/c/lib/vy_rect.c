#include <vy_implem.h>
#include "vy_rect.h"

struct Rect {
   VyRepr repr;
};

VyRepr vyrRect;

void vyDestroyRect( VyPtr ) {
   vyThrow("stub vyDestroyRect");
}

static void vyRectSet( Rect *, Rect ) {
   vyThrow("stub vyRectSet");
}

Shape vyRectCast( Rect ) {
   vyThrow("stub vyRectCast");
}

static Rect vyRectCreateRect( float left, float top, float width, float height ) {
   vyThrow("stub vyRectCreateRect");
}

void vyInitRect( VyContext ctx ) {
   VYRECTARGS( ctx, args );
   vyrRect = vyRepr( sizeof(struct Rect), false, vyDestroyRect);
   vyArgsType( args, "Rect", vyrRect );
   vyArgsImpl( args, "set", vyRectSet );
   vyArgsImpl( args, "cast", vyRectCast );
   vyArgsImpl( args, "createRect", vyRectCreateRect );
   vyAddImplem( ctx, args );
}

