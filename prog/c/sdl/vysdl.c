#include "vy.h"
#include "vy_implem.h"
#include "vysdl.h"

#include <SDL2/SDL.h>

#define BUFSIZE 2048

VySdl vySdl;

static char buf[BUFSIZE];

void vySdlError( VyCStr msg ) {
   snprintf( buf, BUFSIZE, "%s: %s", msg, SDL_GetError() );
   vyThrow( buf );
}

void vyModuleInit( VyContext ctx ) {
   if ( SDL_Init( SDL_INIT_VIDEO | SDL_INIT_EVENTS ))
      vySdlError( "SDL init error" );
   if ( SDL_GetCurrentDisplayMode( 0, &vySdl.displayMode ))
      vySdlError( "SDL displayMode error" );
   vySdlInitKey( ctx );
   vySdlInitView( ctx );
   vySdlInitWindow( ctx );
   vySdlInitSprite( ctx );
}

