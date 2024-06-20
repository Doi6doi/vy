#include <vy_implem.h>
#include <vy_geom.h>
#include <vy_caption.h>
#include <vy_shape.h>
#include <stdio.h>

extern VyRepr vyrString;

extern VyRepr vyrFont;

struct Caption {
   struct Shape shape;
   String text;
   Font font;
};

VyRepr vyrCaption;

extern VyRepr vyrShape;

void vySdlDestroyCaption( VyPtr ) {
   vyThrow("stub vySdlDestroyCaption");
}

static Caption vySdlCaptionCreateCaption( String text, Font font ) {
   Caption ret = vyAllocClear( vyrCaption );
   vyShapeInit( (Shape)ret );
   vySet( (VyAny *)&ret->text, text );
   vySet( (VyAny *)&ret->font, font );
   return ret;
}

String vySdlCaptionText( Caption c ) {
   return c->text;
}

Font vySdlCaptionFont( Caption c ) {
   return c->font;
}

void vySdlInitCaption( VyContext ctx ) {
printf("initcaption\n");   
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Char", vyNative(ctx,"wchar_t") );
   vyArgsType( args, "String", vyrString );
   vyArgsType( args, "Font", vyrFont );
   vyrCaption = vyRepr( "Caption", sizeof(struct Caption), vySetRef, vySdlDestroyCaption);
   vyArgsType( args, "Caption", vyrCaption );
   vyArgsImpl( args, "createCaption", vySdlCaptionCreateCaption );
   vyArgsImpl( args, "text", vySdlCaptionText );
   vyArgsImpl( args, "font", vySdlCaptionFont );
   vyAddImplem( ctx, args );
}

