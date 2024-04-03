#ifndef VY_DISPLAYH
#define VY_DISPLAYH

typedef struct Display * Display;
typedef struct Pen * Pen;
typedef struct Brush * Brush;
typedef struct Rect * Rect;

typedef struct Displays {
   Display (* create)();
   void (* circle)( Display, float x, float y, float radius, Pen, Brush );
   void (* rect)( Display, Rect r, Pen, Brush );
} Displays;

VyImplemArgs displaysArgs() {
   VyImplemArgs ret = vyImplemArgs( "vy.random.Random", vyVer(20240301) );
   vyImplemArgsType(ret, "Display",NULL);
   vyImplemArgsType(ret, "Coord", vyNative("float"));
   vyImplemArgsType(ret, "Pen", NULL );
   vyImplemArgsType(ret, "Brush", NULL );
   vyImplemArgsFunc(ret, "create");
   vyImplemArgsFunc(ret, "destroy");
   vyImplemArgsFunc(ret, "rect");
   return ret;
}

#endif // VY_DISPLAYH
