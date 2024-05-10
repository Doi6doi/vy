#include <vy_implem.h>
#include "vy_caption.h"

extern VyRepr vyrString;

struct Caption {
   VyRepr repr;
};

VyRepr vyrCaption;

void vyDestroyCaption( VyPtr ) {
   vyThrow("stub vyDestroyCaption");
}

static void vyCaptionSet( Caption *, Caption ) {
   vyThrow("stub vyCaptionSet");
}

Shape vyCaptionCast( Caption ) {
   vyThrow("stub vyCaptionCast");
}

static Caption vyCaptionCreateCaption( String ) {
   vyThrow("stub vyCaptionCreateCaption");
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
}

