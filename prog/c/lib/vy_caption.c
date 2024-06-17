#include <vy_implem.h>
#include "vy_caption.h"

#include "vy_shape.h"

extern VyRepr vyrString;

extern VyRepr vyrFont;

struct Caption {
   struct Shape shape;
   String text;
};

VyRepr vyrCaption;

extern VyRepr vyrShape;

void vyDestroyCaption( VyPtr ) {
   vyThrow("stub vyDestroyCaption");
}

static Caption vyCaptionCreateCaption( String text, Font font ) {
   vyThrow("stub vyCaptionCreateCaption");
}

static String vyCaptionText( Caption ) {
   vyThrow("stub vyCaptionText");
}

static Font vyCaptionFont( Caption ) {
   vyThrow("stub vyCaptionFont");
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Char", vyNative(ctx,"wchar_t") );
   vyArgsType( args, "String", vyrString );
   vyArgsType( args, "Font", vyrFont );
   vyrCaption = vyRepr( "Caption", sizeof(struct Caption), vySetRef, vyDestroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyArgsImpl( args, "text", vyCaptionText );
   vyArgsImpl( args, "font", vyCaptionFont );
   vyAddImplem( ctx, args );
}

