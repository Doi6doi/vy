#include <vy.h>
#include <vy_implem.h>
#include "vysdl.h"

#include <vy_square.h>
#include <vy_circle.h>
#include <SDL2/SDL.h>

VySdl vySdl;
VectorFun vySdlVectors;

char vySdlBuf[VYSDLBUFSIZE];

void vySdlError( VyCStr msg ) {
   snprintf( vySdlBuf, VYSDLBUFSIZE, "%s: %s", msg, SDL_GetError() );
   vyThrow( vySdlBuf );
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
   vySdlInitEvent( ctx );
   vySdlInitEventQueue( ctx );
   vySdlInitKeyEvent( ctx );
   vySdlInitView( ctx );
   vySdlInitGroup( ctx );
   vySdlInitWindow( ctx );
   vySdlInitSprite( ctx );
}

static void vySdlUnionCoord( float am, float as, float bm, float bs,
   float * rm, float *rs )
{
   float ar = am + as;
   float br = bm + bs;
   *rm = ( am < bm ? am : bm );
   *rs = ( ar < br ? br : ar )-*rm;
}

void vySdlUnion( VySdlArea a, VySdlArea b, VySdlArea u ) {
   vySdlUnionCoord( a->left, a->width, b->left, b->width, &u->left, &u->width );
   vySdlUnionCoord( a->top, a->height, b->top, b->height, &u->top, &u->height );
}

float vySdlAreaArea( VySdlArea a ) {
   return a->width * a->height;
}

void vySdlAreaDump( VySdlArea a ) {
   printf( "[%.4g %.4g %.4g %.4g]\n", a->left, a->top, a->width, a->height );
}






