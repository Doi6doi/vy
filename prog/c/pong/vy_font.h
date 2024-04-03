#ifndef VY_FONTH
#define VY_FONTH

typedef VyType Canvas;
typedef VyType Font;
typedef VyType Rect;
typedef VyType String;


typedef struct Fonts {
   Rect (* textBounds)( Font, String );
   void (* draw)( Font, Canvas, float x, float y );
} Fonts;


VyImplemArgs fontsArgs() {
   VyImplemArgs ret = vyImplemArgs( "vy.graphic.Font", vyVer( 20240301 ));
   vyImplemArgsType(ret,"N",vyNative("float"));
   vyImplemArgsFunc(ret,"textBounds");
   vyImplemArgsFunc(ret,"draw");
   return ret;
}

#endif // VY_FONTH
