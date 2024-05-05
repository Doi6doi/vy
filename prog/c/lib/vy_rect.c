#include <vy_implem.h>
#include "vy_rect.h"

struct Rect {
   VyRepr repr;
};

VyRepr vyrRect;

void destroyRect( VyPtr ) {
   vyThrow("stub destroyRect");
}

static void vyRectSet( Rect *, Rect ) {
   vyThrow("stub RectSet");
}

static Rect vyRectCreateRect(float left, float top, float width, float height ) {
   vyThrow("stub RectCreateRect");
}

static Shape vyRectCastShape( Rect ) {
   vyThrow("stub RectCastShape");
}

void vyInitRect( VyContext ctx ) {
   VYRECTARGS( ctx, args );
   vyrRect = vyRepr( sizeof(struct Rect), false, destroyRect);
   vyArgsType( args, "Rect", vyrRect );
   vyArgsImpl( args, "set", vyRectSet );
   vyArgsImpl( args, "createRect", vyRectCreateRect );
   vyArgsImpl( args, "castShape", vyRectCastShape );
   vyAddImplem( ctx, args );
}

