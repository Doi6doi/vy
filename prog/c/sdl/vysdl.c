#include <vy.h>
#include <vy_implem.h>
#include "vysdl.h"

#include <SDL2/SDL.h>

#define BUFSIZE 2048

VySdl vySdl;
VectorFun vySdlVectors;

static char buf[BUFSIZE];

void vySdlError( VyCStr msg ) {
   snprintf( buf, BUFSIZE, "%s: %s", msg, SDL_GetError() );
   vyThrow( buf );
}

void vyModuleInit( VyContext ctx ) {
   if ( SDL_Init( SDL_INIT_VIDEO | SDL_INIT_EVENTS ))
      vySdlError( "SDL init error" );
   SDL_DisplayMode dm;
   if ( SDL_GetCurrentDisplayMode( 0, & dm ) )
      vySdlError( "SDL displayMode error" );
   vySdl.width = dm.w;
   vySdl.height = dm.h;
   float hdpi, vdpi;
   if ( SDL_GetDisplayDPI( 0, NULL, & hdpi, & vdpi ))
      vySdlError( "SDL dpi error" );
   vySdl.aspect = vdpi ? hdpi / vdpi : 0;
   VYVECTORARGS( ctx, vectorArgs );
   vyFree( vyGetImplem( ctx, vectorArgs, & vySdl.vectors ) );

   VYFILLEDARGS( ctx, filledArgs );
   vyGetImplem( ctx, filledArgs, & vySdl.filleds );
   vySdl.Filled = vyArgsRepr( filledArgs, "Filled" );
   vyFree( filledArgs );

   VYRECTARGS( ctx, rectArgs );
   vyGetImplem( ctx, rectArgs, & vySdl.rects );
   vySdl.Rect = vyArgsRepr( rectArgs, "Rect" );
   vyFree( rectArgs );
   
   VYCIRCLEARGS( ctx, circleArgs );
   vyGetImplem( ctx, circleArgs, & vySdl.circles );
   vySdl.Circle = vyArgsRepr( circleArgs, "Circle" );
   vyFree( circleArgs );
   
   vySdlInitKey( ctx );
   vySdlInitView( ctx );
   vySdlInitGroup( ctx );
   vySdlInitWindow( ctx );
   vySdlInitSprite( ctx );
}

