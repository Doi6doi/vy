#include <vy_implem.h>
#include "vy_geom.h"
#include "vy_color.h"

static VyColor vyColorConstHex(VyCStr, VySize ) {
   vyThrow("stub ColorConstHex");
}

void vyInitColor( VyContext ctx ) {
   vyAddNative( ctx, "VyColor", sizeof( VyColor ));
   VYCOLORARGS( ctx, args );
   vyArgsImpl( args, "constHex", vyColorConstHex );
   vyAddImplem( ctx, args );
}

