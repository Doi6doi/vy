#include <vy_implem.h>
#include "vysdl.h"
#include "vy_geom.h"
#include "vy_ui.h"
#include "vy_sprite.h"
#include "vy_view.h"

#include <stdio.h>

extern VyRepr vyrShape;

struct Sprite {
   struct View view;
   Shape shape;
};

VyRepr vyrSprite;

extern VyRepr vyrView;

void vyDestroySprite( VyPtr ) {
   vyThrow("stub vyDestroySprite");
}

static Sprite vySdlSpriteCreateSprite( Shape shape ) {
   Sprite ret = vyAlloc( vyrSprite );
   vyShapeInit( (Shape)ret );
   ret->shape = NULL;
   vySet( (VyAny *)&ret->shape, (VyAny)shape );
   return ret;
}

static void vySdlSpriteMoveTo( Sprite, float x, float y ) {
   vyThrow("stub vySpriteMoveTo");
}

static void vySdlSpriteSetShape( Sprite, Shape ) {
   vyThrow("stub vySpriteSetShape");
}

static float vySdlShapeWidth( Shape s ) {
   VyRepr r = vyGetRepr( s );
   if ( vySdl.Rect == r )
      return vySdl.rects.width( (Rect)s );
   else if ( vySdl.Circle == r )
      return 2*vySdl.circles.radius( (Circle)s );
   else if ( vySdl.Filled == r )
      return vySdlShapeWidth( vySdl.filleds.shape( (Filled)s ) );
   vyDumpRepr( r );
   vyThrow("Unknown shape width");
}

static float vySdlShapeHeight( Shape s ) {
   VyRepr r = vyGetRepr( s );
   if ( vySdl.Rect == r )
      return vySdl.rects.height( (Rect)s );
   else if ( vySdl.Circle == r )
      return 2*vySdl.circles.radius( (Circle)s );
   else if ( vySdl.Filled == r )
      return vySdlShapeWidth( vySdl.filleds.shape( (Filled)s ) );
   vyDumpRepr( r );
   vyThrow("Unknown shape height");
}

float vySdlSpriteCoord( Sprite s, VyViewCoord c ) {
   float f;
   switch ( c ) {
      case VC_CENTERX: return s->view.x;
      case VC_CENTERY: return s->view.y;
      case VC_LEFT: case VC_RIGHT: case VC_WIDTH:
         f = vySdlShapeWidth( s->shape );
      break;
      case VC_TOP: case VC_BOTTOM: case VC_HEIGHT:
         f = vySdlShapeHeight( s->shape );
      break;
      default:
         vyThrow("Unknown coordinate");
   }
   switch (c) {
      case VC_LEFT: return s->view.x - f/2;
      case VC_RIGHT: return s->view.x + f/2;
      case VC_WIDTH: return f;
      case VC_TOP: return s->view.y - f/2;
      case VC_BOTTOM: return s->view.y + f/2;
      case VC_HEIGHT: return f;
      default:
         vyThrow("Unknown coordinate");
   }
}      

static void vySdlSpriteSetCoord( Sprite, VyViewCoord, float ) {
   vyThrow("stub vySpriteSetCoord");
}

void vySdlInitSprite( VyContext ctx ) {
   VYSPRITEARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyrSprite = vyRepr( "Sprite", sizeof(struct Sprite), vySetRef, vyDestroySprite);
   vyArgsType( args, "Shape", vyrShape );
   vyArgsType( args, "Sprite", vyrSprite );
   vyArgsImpl( args, "createSprite", vySdlSpriteCreateSprite );
   vyArgsImpl( args, "moveTo", vySdlSpriteMoveTo );
   vyArgsImpl( args, "setShape", vySdlSpriteSetShape );
   vyArgsImpl( args, "coord", vySdlSpriteCoord );
   vyArgsImpl( args, "setCoord", vySdlSpriteSetCoord );
   vyAddImplem( ctx, args );
}

