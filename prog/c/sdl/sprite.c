#include <vy_implem.h>
#include "vysdl.h"
#include "vy_ui.h"
#include "vy_sprite.h"
#include "vy_shape.h"

extern VyRepr vyrShape;

struct Sprite {
   View view;
   Shape shape;
};

VyRepr vyrSprite;

ShapeFun shapes;

void vyDestroySprite( VyPtr ) {
   vyThrow("stub vyDestroySprite");
}

static void vySpriteSet( Sprite *, Sprite ) {
   vyThrow("stub vySpriteSet");
}

View vySpriteCast( Sprite x ) {
   return (View)x;
}

static Sprite vySpriteCreateSprite( Shape shape ) {
   Sprite ret = vyAlloc( vyrSprite );
   shapes.set( &(ret->shape), shape );
   return ret;
}

static void vySpriteMoveTo( Sprite, float x, float y ) {
   vyThrow("stub vySpriteMoveTo");
}

static void vySpriteSetShape( Sprite, Shape ) {
   vyThrow("stub vySpriteSetShape");
}

static float vySpriteCoord( Sprite, VyViewCoord ) {
   vyThrow("stub vySpriteCoord");
}

static void vySpriteSetCoord( Sprite, VyViewCoord, float ) {
   vyThrow("stub vySpriteSetCoord");
}

void vySdlInitSprite( VyContext ctx ) {
   VYSPRITEARGS( ctx, args );
   vyArgsType( args, "Shape", vyrShape );
   vyrSprite = vyRepr( sizeof(struct Sprite), false, vyDestroySprite);
   vyArgsType( args, "Sprite", vyrSprite );
   vyArgsImpl( args, "set", vySpriteSet );
   vyArgsImpl( args, "cast", vySpriteCast );
   vyArgsImpl( args, "createSprite", vySpriteCreateSprite );
   vyArgsImpl( args, "moveTo", vySpriteMoveTo );
   vyArgsImpl( args, "setShape", vySpriteSetShape );
   vyArgsImpl( args, "coord", vySpriteCoord );
   vyArgsImpl( args, "setCoord", vySpriteSetCoord );
   vyAddImplem( ctx, args );
   VYIMPORTSHAPE( ctx, shapes );
}

