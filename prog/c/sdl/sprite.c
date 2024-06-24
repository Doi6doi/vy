#include <vy_implem.h>
#include "vysdl.h"
#include <vy_geom.h>
#include <vy_ui.h>
#include <vy_shape.h>
#include <vy_sprite.h>
#include <vy_view.h>
#include <vy_caption.h>
#include <vy_transform.h>
#include <vy_transformed.h>
#include <stdio.h>
#include <math.h>

extern VyRepr vyrShape;
extern VyRepr vyrCaption;

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
   vySdlViewInit( (View)ret );
   ret->shape = NULL;
   vySet( (VyAny *)&ret->shape, shape );
   return ret;
}

static void vySdlSpriteMoveTo( Sprite, float x, float y ) {
   vyThrow("stub vySpriteMoveTo");
}

static void vySdlSpriteSetShape( Sprite, Shape ) {
   vyThrow("stub vySpriteSetShape");
}

static float vySdlShapeDim( Shape s, bool height );

static float vySdlTransformedDim( Transformed td, bool height ) {
   Shape sub = vySdl.transformeds.sub(td);
   Transform t = vySdl.transformeds.transform(td);
   float w = vySdlShapeDim( sub, false );
   float h = vySdlShapeDim( sub, true );
   float ret = height ? w*t->ry + h*t->sy : w*t->sx + h*t->rx;
   return fabs( ret );
}

static float vySdlCaptionDim( Caption c, bool height ) {
   Font f = vySdlCaptionFont( c );
   if ( height )
      return vySdlFontHeight( f );
   String s = vySdlCaptionText( c );
   return vySdlFontWidth( f, s );
}

static float vySdlShapeDim( Shape s, bool height ) {
   VyRepr r = vyGetRepr( s );
   if ( vySdl.Transformed == r )
      return vySdlTransformedDim( (Transformed)s, height );
   else if ( vySdl.Square == r )
      return 1;
   else if ( vySdl.Circle == r )
      return 1;
   else if ( vyrCaption == r )
      return vySdlCaptionDim( (Caption)s, height );
   else if ( vySdl.Filled == r )
      return vySdlShapeDim( vySdl.filleds.shape( (Filled)s ), height );
   vyDumpRepr( r );
   vyThrow("Unknown shape dim");
   return 0;
}

float vySdlSpriteCoord( Sprite s, VyViewCoord c ) {
   float f;
   switch ( c ) {
      case VC_CENTERX: return s->view.x;
      case VC_CENTERY: return s->view.y;
      case VC_LEFT: case VC_RIGHT: case VC_WIDTH:
         f = vySdlShapeDim( s->shape, false );
      break;
      case VC_TOP: case VC_BOTTOM: case VC_HEIGHT:
         f = vySdlShapeDim( s->shape, true );
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
   return 0;
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

