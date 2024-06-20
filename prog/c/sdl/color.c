#include <vy_implem.h>
#include <vy_geom.h>
#include <vy_color.h>

#include <SDL2/SDL.h>

#include <stdio.h>
#define UNCOL "Unknown color constant"

static VyColor vySdlColorConstHex( VyCStr s, VySize l ) {
   switch ( l ) {
      case 3: return (uint32_t)(s[0] << 24)
         | (uint32_t)(s[1] << 16)
         | (uint32_t)(s[2] << 8 )
         | 0xff;
      case 4: return (uint32_t)(s[0] << 24)
         | (uint32_t)(s[1] << 16)
         | (uint32_t)(s[2] << 8 )
         | (uint32_t)s[3];
      default:
         vyThrow( UNCOL );
   }
}

void vySdlInitColor( VyContext ctx ) {
   VYCOLORARGS( ctx, args );
   vyArgsType( args, "Color", vyNative(ctx,"VyColor") );
   vyArgsImpl( args, "constHex", vySdlColorConstHex );
   vyAddImplem( ctx, args );
}

