#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_sprite.h"

extern VyRepr vyrShape;

struct Sprite {
   VyRepr repr;
};

VyRepr vyrSprite;

void destroySprite( VyPtr ) {
   vyThrow("stub destroySprite");
}

static void vySpriteSet( Sprite *, Sprite ) {
   vyThrow("stub SpriteSet");
}

static Sprite vySpriteCreateSprite(Shape ) {
   vyThrow("stub SpriteCreateSprite");
}

static void vySpriteMoveTo(Sprite, float x, float y ) {
   vyThrow("stub SpriteMoveTo");
}

static void vySpriteSetShape(Sprite, Shape ) {
   vyThrow("stub SpriteSetShape");
}

static Sprite vySpriteCreate( ) {
   vyThrow("stub SpriteCreate");
}

static float vySpriteCoord(Sprite, VyViewCoord ) {
   vyThrow("stub SpriteCoord");
}

static void vySpriteSetCoord(Sprite, VyViewCoord, float ) {
   vyThrow("stub SpriteSetCoord");
}

static View vySpriteCastView( Sprite ) {
   vyThrow("stub SpriteCastView");
}

void vySdlInitSprite( VyContext ctx ) {
   VYSPRITEARGS( ctx, args );
   vyArgsType( args, "Shape", vyrShape );
   vyrSprite = vyRepr( sizeof(struct Sprite), false, destroySprite);
   vyArgsType( args, "Sprite", vyrSprite );
   vyArgsImpl( args, "set", vySpriteSet );
   vyArgsImpl( args, "createSprite", vySpriteCreateSprite );
   vyArgsImpl( args, "moveTo", vySpriteMoveTo );
   vyArgsImpl( args, "setShape", vySpriteSetShape );
   vyArgsImpl( args, "create", vySpriteCreate );
   vyArgsImpl( args, "coord", vySpriteCoord );
   vyArgsImpl( args, "setCoord", vySpriteSetCoord );
   vyArgsImpl( args, "castView", vySpriteCastView );
   vyAddImplem( ctx, args );
}

