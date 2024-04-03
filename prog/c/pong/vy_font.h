#ifndef VY_FONTH
#define VY_FONTH

typedef struct Display * Display;
typedef struct Rect * Rect;
typedef struct String * String;
typedef struct Font * Font;

typedef struct Fonts {
   Rect (* textBounds)( Font, String );
   void (* draw)( Font, Display, float x, float y, String );
} Fonts;

VyImplemArgs fontsArgs( VyRepr display, VyRepr rect, VyRepr string ) {
   VyImplemArgs ret = vyImplemArgs( "vy.graphic.Font", vyVer( 20240301 ));
   vyImplemArgsType(ret,"Canvas",display);
   vyImplemArgsType(ret,"Rect",rect);
   vyImplemArgsType(ret,"String",string);
   vyImplemArgsType(ret,"Num",vyNative("float"));
   vyImplemArgsFunc(ret,"textBounds");
   vyImplemArgsFunc(ret,"draw");
   return ret;
}

#endif // VY_FONTH
