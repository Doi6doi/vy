#include <vy_implem.h>
#include "vy_caption.h"

struct  {
   VyRepr repr;
};

VyRepr vyr;

struct  {
   VyRepr repr;
};

VyRepr vyr;

struct  {
   VyRepr repr;
};

VyRepr vyr;

struct  {
   VyRepr repr;
};

VyRepr vyr;

struct  {
   VyRepr repr;
};

VyRepr vyr;

void vyDestroy( VyPtr ) {
   vyThrow("stub vyDestroy");
}

static void vyCaptionSet( String * dest, String val) {
   vySetter( (VyAny *)dest, (VyAny)val );
}

 vyCaption:Shape.ShapeCast( Caption:Shape.Shape ) {
   vyThrow("stub vyCaption:Shape.ShapeCast");
}

static CaptionvyCaptionCreateCaption( String ) {
   vyThrow("stub vyCaptionCreateCaption");
}

void vyInitCaption( VyContext ctx ) {
   VYCAPTIONARGS( ctx, args );
   vyArgsType( args, "Bool", vyr );
   vyArgsType( args, "Char", vyr );
   vyr = vyRepr( sizeof(struct ), false, vyDestroy);
   vyArgsType( args, "String", vyr );
   vyArgsImpl( args, "set", vyCaptionSet );
   vyArgsType( args, "Coord", vyr );
   vyArgsType( args, "Caption", vyr );
   vyArgsImpl( args, "cast", vyCaption:Shape.ShapeCast );
   vyArgsImpl( args, "createCaption", vyCaptionCreateCaption );
   vyAddImplem( ctx, args );
}

