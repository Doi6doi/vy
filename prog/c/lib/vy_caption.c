#include <vy_implem.h>
#include "vy_caption.h"

#include "vy_shape.h"

extern VyRepr vyrString;

struct Caption {
   struct Shape shape;
   String text;
};

VyRepr vyrCaption;

extern VyRepr vyrShape;

void vyDestroyCaption( VyPtr ) {
   vyThrow("stub vyDestroyCaption");
}

static void vyCaptionSet( Caption * dest, Caption val ) {
   vySetter( (VyAny *)dest, (VyAny)val );
}

Shape vyCaptionCast( Caption x ) { return (Shape)x; }

static Caption vyCaptionCreateCaption( String ) {
   vyThrow("stub vyCaptionCreateCaption");
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Char", vyNative(ctx,"wchar_t") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyrCaption = vyRepr( sizeof(struct Caption), false, vyDestroyCaption);
   vyArgsImpl( args, "set", vyCaptionSet );
   vyArgsImpl( args, "cast", vyCaptionCast );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyAddImplem( ctx, args );
}

