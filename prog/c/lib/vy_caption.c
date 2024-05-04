#include <vy_implem.h>
#include "vy_geom_impl.h"
#include "vy_caption.h"

extern VyRepr vyrString;

struct Caption {
   struct Shape shape;
   String str;
};

VyRepr vyrCaption;

void destroyCaption( VyPtr ) {
   vyThrow("stub destroyCaption");
}

static Caption vyCaptionCreateCaption(String str) {
   Caption ret = vyAlloc( vyrCaption );
   ret->str = 
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
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyArgsImpl( args, "castShape", vyCaptionCastShape );
   vyAddImplem( ctx, args );
}

