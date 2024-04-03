#ifndef VY_RECTH
#define VY_RECTH

typedef struct Rect * Rect;

typedef struct Rects {
   Rect (* create)( float left, float top, float width, float height );
   void (*move)( Rect r, float dx, float dy );
   float (* width)( Rect r );
   float (* height)( Rect r );
} Rects;

VyImplemArgs rectsArgs() {
   VyImplemArgs ret = vyImplemArgs( "vy.graphic.Rect", vyVer( 20240301 ));
   vyImplemArgsType(ret,"Coord",vyNative("float"));
   return ret;
}

#endif // VY_RECTH
