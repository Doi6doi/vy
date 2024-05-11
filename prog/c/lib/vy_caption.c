#include <vy_implem.h>
#include "vy_caption.h"
#include "vy_string.h"

extern VyRepr vyrString;
static StringFun strings;

struct Caption {
   VyRepr repr;
   String text;
};

VyRepr vyrCaption;

void vyDestroyCaption( VyPtr ) {
   vyThrow("stub vyDestroyCaption");
}

static void vyCaptionSet( Caption *, Caption ) {
   vyThrow("stub vyCaptionSet");
}

Shape vyCaptionCast( Caption c ) { return (Shape)c; }

static Caption vyCaptionCreateCaption( String s ) {
   Caption ret = vyAlloc( vyrCaption );
   ret->text = NULL;
   strings.set( &ret->text, s );
   return ret;
}

void vyInitCaption( VyContext ctx ) {
   VYIMPORTSTRING( ctx, strings );
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "String", vyrString );
   vyrCaption = vyRepr( sizeof(struct Caption), false, vyDestroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "set", vyCaptionSet );
   vyArgsImpl( args, "cast", vyCaptionCast );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyAddImplem( ctx, args );
}

