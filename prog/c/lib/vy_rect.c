#include <vy_implem.h>
#include "vy_rect.h"

struct Bool {
   VyRepr repr;
};

VyRepr vyrBool;

void destroyBool( VyPtr ) {
   vyThrow("stub destroyBool");
}

struct Rect {
   VyRepr repr;
};

VyRepr vyrRect;

void destroyRect( VyPtr ) {
   vyThrow("stub destroyRect");
}

static Rect vyRectCreateRect(float left, float top, float width, float height ) {
   vyThrow("stub RectCreateRect");
}

void vyInitRect( VyContext ctx ) {
   VYRECTARGS( ctx, args );
   vyrBool = vyRepr( sizeof(struct Bool), false, destroyBool);
   vyArgsType( args, "Bool", vyrBool );
   vyrRect = vyRepr( sizeof(struct Rect), false, destroyRect);
   vyArgsType( args, "Rect", vyrRect );
   vyArgsImpl( args, "createRect", vyRectCreateRect );
   vyAddImplem( ctx, args );
}

