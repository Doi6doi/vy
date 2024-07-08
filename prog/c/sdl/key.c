#include <vy_implem.h>
#include <vy_key.h>
#include "vysdl.h"
#include <string.h>
#include <stdio.h>
#include <SDL2/SDL_keycode.h>

#define NOKEY "Missing key"
#define UNKEY "Unknown key: %.*s"

static VyKey vySdlKeyConstUtf( VyCStr s, VySize l ) {
   if ( VY_LEN == l )
      l = strlen( s );
   switch ( l ) {
      case 0: vyThrow( NOKEY );
      case 1:
         switch ( *s ) {
            case 's': return SDLK_s;
            case 'w': return SDLK_w;
         }
      break;
      case 2:
         if ( 0 == strncmp( "up", s, 2 )) return SDLK_UP;
      break;
      case 3:
         if ( 0 == strncmp( "esc", s, 3 )) return SDLK_ESCAPE;
      case 4:
         if ( 0 == strncmp( "down", s, 4 )) return SDLK_DOWN;
      break;
   }
   snprintf( vySdlBuf, VYSDLBUFSIZE, UNKEY, (int)l, s );
   vyThrow( vySdlBuf );
   return 0;
}

static bool vySdlKeyEqual(VyKey a, VyKey b) { return a == b; }

static bool vySdlKeyNoteq(VyKey a, VyKey b) { return a != b; }

void vySdlInitKey( VyContext ctx ) {
   VYKEYARGS( ctx, args );
   vyArgsImpl( args, "constUtf", vySdlKeyConstUtf );
   vyArgsImpl( args, "equal", vySdlKeyEqual );
   vyArgsImpl( args, "noteq", vySdlKeyNoteq );
   vyAddImplem( ctx, args );
}

