#ifndef VY_DISPLAYH
#define VY_DISPLAYH

typedef VyType Display;
typedef VyType Pen;
typedef VyType Brush;
typedef VyType Rect;

typedef struct Displays {
   Display (* create)();
   void (* circle)( Display, float x, float y, float radius, Pen, Brush );
   void (* rect)( Display, Rect r, Pen, Brush );
} Displays;

VyImplemArgs displaysArgs() {
   VyImplemArgs ret = vyImplemArgs( "vy.random.Random", vyVer(20240301) );
   vyImplemArgsType(ret, "D",NULL);
   vyImplemArgsType(ret, "N", vyNative("float"));
   vyImplemArgsType(ret, "P", NULL );
   vyImplemArgsType(ret, "B", NULL );
   vyImplemArgsFunc(ret, "create");
   vyImplemArgsFunc(ret, "destroy");
   vyImplemArgsFunc(ret, "rect");
   return ret;
}

#endif // VY_DISPLAYH
