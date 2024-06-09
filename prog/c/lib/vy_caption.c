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

Shape vyCaptionCast( Caption x ) { return (Shape)x; }

static Caption vyCaptionCreateCaption( String text ) {
   Caption ret = vyAlloc( vyrCaption );
   vyShapeInit( (Shape)ret );
   ret->text = NULL;
   vySet( & ret->text, text );
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Char", vyNative(ctx,"wchar_t") );
   vyArgsType( args, "String", vyrString );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyrCaption = vyRepr( sizeof(struct Caption), vySetRef, vyDestroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "cast", vyCaptionCast );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyAddImplem( ctx, args );
}

