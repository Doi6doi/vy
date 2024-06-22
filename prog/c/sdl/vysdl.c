#include <vy.h>
#include <vy_implem.h>
#include "vysdl.h"

#include <vy_square.h>
#include <vy_circle.h>
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

   VYTRANSFORMEDARGS( ctx, transformedArgs );
   vyGetImplem( ctx, transformedArgs, & vySdl.transformeds );
   vySdl.Transformed = vyArgsRepr( transformedArgs, "Transformed" );
   vyFree( transformedArgs );

   VYSQUAREARGS( ctx, squareArgs );
   SquareFun squares;
   vyGetImplem( ctx, squareArgs, & squares );
   vySdl.Square = vyArgsRepr( squareArgs, "Square" );
   vyFree( squareArgs );

   VYCIRCLEARGS( ctx, circleArgs );
   CircleFun circles;
   vyGetImplem( ctx, circleArgs, & circles );
   vySdl.Circle = vyArgsRepr( circleArgs, "Circle" );
   vyFree( circleArgs );

   vySdlInitColor( ctx );
   vySdlInitFont( ctx );
   vySdlInitCaption( ctx );
   vySdlInitKey( ctx );
   vySdlInitView( ctx );
   vySdlInitGroup( ctx );
   vySdlInitWindow( ctx );
   vySdlInitSprite( ctx );
}

void vySdlSetArea( VySdlArea a, float top, float left, float width, float height ) {
   a->top = top;
   a->left = left;
   a->width = width;
   a->height = height;
}


