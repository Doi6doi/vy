#ifndef VY_SPRITEH
#define VY_SPRITEH

typedef float Coord;
typedef VyType Sprite;
typedef VyType Scene;
typedef VyType Canvas;

typedef struct Sprites {
   Scene (* createScene)();
   void (* destroyScene)( Scene );
   Sprite (* createSprite)( Scene );
   void (* destroySprite)( Sprite );
   void (* callbacks)( Sprite s, VyFunc1 onBounds, VyProc1 onDraw );
   void (* move)( Sprite s, float x, float y );
   void (* draw)( Scene s, Canvas d );
} Sprites;

VyImplemArgs spritesArgs( VyRepr d ) {
   VyImplemArgs ret = vyImplemArgs("vy.ui.Sprite", vyVer(20240301) );
   vyImplemArgsType(ret, "C",NULL);
   vyImplemArgsType(ret, "S",NULL);
   vyImplemArgsType(ret, "F",vyNative("function"));
   vyImplemArgsType(ret, "N",vyNative("float"));
   vyImplemArgsType(ret, "V",d);
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
