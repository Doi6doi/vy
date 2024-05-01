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

static Caption vyCaptionCreateCaption(String ) {
   vyThrow("stub CaptionCreateCaption");
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "String", vyrString );
   vyrCaption = vyRepr( sizeof(struct Caption), false, destroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyAddImplem( ctx, args );
}

