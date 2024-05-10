#include <vy_implem.h>
#include "vy_caption.h"
#include "vy_geom_impl.h"

#include "vy_string.h"

extern VyRepr vyrString;

struct Caption {
   Shape shape;
   String text;
};

static StringFun strings;

VyRepr vyrCaption;

void vyDestroyCaption( VyPtr ) {
   vyThrow("stub vyDestroyCaption");
}

static void vyCaptionSet( Caption *, Caption ) {
   vyThrow("stub vyCaptionSet");
}

Shape vyCaptionCast( Caption x ) {
   return (Shape)x;
}

static Caption vyCaptionCreateCaption( String s ) {
   Caption ret = vyAlloc( vyrCaption );
   ret->text = NULL;
   strings.set( &ret->text, s );
   return ret;
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "String", vyrString );
   vyrCaption = vyRepr( sizeof(struct Caption), false, vyDestroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "set", vyCaptionSet );
   vyArgsImpl( args, "cast", vyCaptionCast );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyAddImplem( ctx, args );
   VYIMPORTSTRING( ctx, strings );
}

