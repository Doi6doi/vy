#include <vy_implem.h>
#include "vysdl.h"
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

View vySpriteCast( Sprite x ) { return (View)x; }

static Sprite vySpriteCreateSprite( Shape ) {
   vyThrow("stub vySpriteCreateSprite");
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

void vyInitSprite( VyContext ctx ) {
   VYSPRITEARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyrSprite = vyRepr( sizeof(struct Sprite), false, vyDestroySprite);
   vyArgsImpl( args, "cast", vySpriteCast );
   vyArgsImpl( args, "createSprite", vySpriteCreateSprite );
   vyArgsImpl( args, "moveTo", vySpriteMoveTo );
   vyArgsImpl( args, "setShape", vySpriteSetShape );
   vyArgsImpl( args, "coord", vySpriteCoord );
   vyArgsImpl( args, "setCoord", vySpriteSetCoord );
   vyAddImplem( ctx, args );
}

