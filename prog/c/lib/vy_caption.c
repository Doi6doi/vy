#include <vy_implem.h>
#include "vy_caption.h"

extern VyRepr vyrString;

struct Caption {
   VyRepr repr;
};

VyRepr vyrCaption;

void destroyCaption( VyPtr ) {
   vyThrow("stub destroyCaption");
}

static void vyCaptionSet( Caption *, Caption ) {
   vyThrow("stub CaptionSet");
}

static Caption vyCaptionCreateCaption(String ) {
   vyThrow("stub CaptionCreateCaption");
}

static Shape vyCaptionCastShape( Caption ) {
   vyThrow("stub CaptionCastShape");
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "String", vyrString );
   vyrCaption = vyRepr( sizeof(struct Caption), false, destroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "set", vyCaptionSet );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyArgsImpl( args, "castShape", vyCaptionCastShape );
   vyAddImplem( ctx, args );
}

