#include <vy_implem.h>
#include <vy_ui.h>
#include <vy_view.h>
#include "vysdl.h"

#include <stdio.h>


VyRepr vyrView;

extern VyRepr vyrSprite;

void vySdlViewInit( View v ) {
   vyRefInit( (VyRefCount)v );   
   v->group = NULL;
   v->x = 0;
   v->y = 0;
}

void vySdlInvalidate( View v ) {
   if ( ! v->group ) return;
   vySdlInvalidateGroup( v->group, vySdlViewArea( v ));
}

VySdlArea vySdlViewArea( View v ) {
   return vySdlArea( 
      vySdlViewCoord( v, VC_LEFT ),
      vySdlViewCoord( v, VC_TOP ),
      vySdlViewCoord( v, VC_WIDTH ),
      vySdlViewCoord( v, VC_HEIGHT )
   );
}

static void vySdlDestroyView( VyPtr ) {
   vyThrow("stub vySdlDestroyView");
}

float vySdlViewCoord( View v, VyViewCoord c ) {
   if ( vyGetRepr( v ) == vyrSprite )
      return vySdlSpriteCoord( (Sprite)v, c );
   vyThrow( "Unknown view coord" );
}

void vySdlViewSetCoord( View, VyViewCoord, float ) {
   vyThrow("stub vySdlViewSetCoord");
}

void vySdlInitView( VyContext ctx ) {
   VYVIEWARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyrView = vyRepr( "View", sizeof(struct View), vySetRef, vySdlDestroyView);
   vyArgsType( args, "View", vyrView );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsImpl( args, "coord", vySdlViewCoord );
   vyArgsImpl( args, "setCoord", vySdlViewSetCoord );
   vyAddImplem( ctx, args );
}

