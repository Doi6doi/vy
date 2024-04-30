#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_window.h"

struct Window {
   VyRepr repr;
};

extern VyRepr vyrView;

VyRepr vyrWindow;

void destroyWindow( VyPtr ) {
   vyThrow("stub destroyWindow");
}

static void vyWindowAdd(Window, View ) {
   vyThrow("stub WindowAdd");
}

static void vyWindowRemove(Window, View ) {
   vyThrow("stub WindowRemove");
}

static Window vyWindowCreate( ) {
   vyThrow("stub WindowCreate");
}

static float vyWindowCoord(Window, VyViewCoord ) {
   vyThrow("stub WindowCoord");
}

static void vyWindowSetCoord(Window, VyViewCoord, float ) {
   vyThrow("stub WindowSetCoord");
}

void vyInitWindow( VyContext ctx ) {
   VYWINDOWARGS( ctx, args );
   vyrWindow = vyRepr( sizeof(struct Window), false, destroyWindow);
   vyArgsType( args, "Window", vyrWindow );
   vyArgsType( args, "Sub", vyrView );
   vyArgsImpl( args, "add", vyWindowAdd );
   vyArgsImpl( args, "remove", vyWindowRemove );
   vyArgsImpl( args, "create", vyWindowCreate );
   vyArgsImpl( args, "coord", vyWindowCoord );
   vyArgsImpl( args, "setCoord", vyWindowSetCoord );
   vyAddImplem( ctx, args );
}

