#include <vy_implem.h>
#include "vysdl.h"
#include "vy_geom.h"
#include "vy_ui.h"
#include "vy_sprite.h"
#include "vy_view.h"

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

static float vySdlSpriteCoord( Sprite, VyViewCoord ) {
   vyThrow("stub vySpriteCoord");
}

static void vySdlSpriteSetCoord( Sprite, VyViewCoord, float ) {
   vyThrow("stub vySpriteSetCoord");
}

void vySdlInitSprite( VyContext ctx ) {
   VYSPRITEARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyrSprite = vyRepr( "Sprite", sizeof(struct Sprite), false, vyDestroySprite);
   vyArgsType( args, "Shape", vyrShape );
   vyArgsType( args, "Sprite", vyrSprite );
   vyArgsImpl( args, "createSprite", vySdlSpriteCreateSprite );
   vyArgsImpl( args, "moveTo", vySdlSpriteMoveTo );
   vyArgsImpl( args, "setShape", vySdlSpriteSetShape );
   vyArgsImpl( args, "coord", vySdlSpriteCoord );
   vyArgsImpl( args, "setCoord", vySdlSpriteSetCoord );
   vyAddImplem( ctx, args );
}

