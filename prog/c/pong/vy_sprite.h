#ifndef VY_SPRITEH
#define VY_SPRITEH

typedef struct Rect * Rect;
typedef struct Sprite * Sprite;
typedef struct Scene * Scene;
typedef struct Display * Display;

typedef Rect (* OnBounds)( Sprite );
typedef void (* OnDraw)( Sprite );

typedef struct Sprites {
   Scene (* createScene)();
   void (* destroyScene)( Scene );
   Sprite (* createSprite)( Scene );
   void (* destroySprite)( Sprite );
   void (* callbacks)( Sprite, OnBounds, OnDraw );
   void (* move)( Sprite s, float x, float y );
   void (* draw)( Scene s, Display d );
} Sprites;

VyImplemArgs spritesArgs( VyRepr canvas ) {
   VyImplemArgs ret = vyImplemArgs("vy.ui.Sprite", vyVer(20240301) );
   vyImplemArgsType(ret, "Scene",NULL);
   vyImplemArgsType(ret, "Sprite",NULL);
   vyImplemArgsType(ret, "Callback",vyNative("function"));
   vyImplemArgsType(ret, "Coord",vyNative("float"));
   vyImplemArgsType(ret, "Canvas",canvas);
   vyImplemArgsFunc(ret, "createScene" );
   vyImplemArgsFunc(ret, "destroyScene" );
   vyImplemArgsFunc(ret, "createSprite" );
   vyImplemArgsFunc(ret, "destroySprite" );
   vyImplemArgsFunc(ret, "callbacks" );
   vyImplemArgsFunc(ret, "move" );
   vyImplemArgsFunc(ret, "draw" );
   return ret;
}

#endif // VY_SPRITEH
