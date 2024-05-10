#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_sprite.h"

extern VyRepr vyrShape;

struct Sprite {
   VyRepr repr;
};

VyRepr vyrSprite;

void vyDestroySprite( VyPtr ) {
   vyThrow("stub vyDestroySprite");
}

static void vySpriteSet( Sprite *, Sprite ) {
   vyThrow("stub vySpriteSet");
}

View vySpriteCast( Sprite ) {
   vyThrow("stub vySpriteCast");
}

static Sprite vySpriteCreateSprite( Shape ) {
   vyThrow("stub vySpriteCreateSprite");
}

static void vySpriteMoveTo( Sprite, float x, float y ) {
   vyThrow("stub vySpriteMoveTo");
}

static void vySpriteSetShape( Sprite, Shape ) {
   vyThrow("stub vySpriteSetShape");
}

static Sprite vySpriteCreate(  ) {
   vyThrow("stub vySpriteCreate");
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
   vyArgsImpl( args, "create", vySpriteCreate );
   vyArgsImpl( args, "coord", vySpriteCoord );
   vyArgsImpl( args, "setCoord", vySpriteSetCoord );
   vyAddImplem( ctx, args );
}

