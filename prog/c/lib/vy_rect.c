#include <vy_implem.h>
#include "vy_rect.h"

struct Rect {
   VyRepr repr;
};

VyRepr vyrRect;

void vyDestroyRect( VyPtr ) {
   vyThrow("stub vyDestroyRect");
}

static void vyRectSet( Rect * dest, Rect val) {
   vySetter( (VyAny *)dest, (VyAny)val );
}

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
   vyrRect = vyRepr( sizeof(struct Rect), false, vyDestroyRect);
   vyArgsType( args, "Rect", vyrRect );
   vyArgsImpl( args, "set", vyRectSet );
   vyArgsImpl( args, "cast", vyRectCast );
   vyArgsImpl( args, "createRect", vyRectCreateRect );
   vyArgsImpl( args, "width", vyRectWidth );
   vyArgsImpl( args, "height", vyRectHeight );
   vyAddImplem( ctx, args );
}

