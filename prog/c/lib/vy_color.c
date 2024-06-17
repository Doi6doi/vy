#include <vy_implem.h>
#include "vy_geom.h"
#include "vy_color.h"

static VyColor vyColorConstHex( VyCStr, VySize ) {
   vyThrow("stub vyColorConstHex");
}

void vyInitColor( VyContext ctx ) {
   VYCOLORARGS( ctx, args );
   vyArgsType( args, "Color", vyNative(ctx,"VyColor") );
   vyArgsImpl( args, "constHex", vyColorConstHex );
   vyAddImplem( ctx, args );
}

