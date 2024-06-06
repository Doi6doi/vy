#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_window.h"
#include "vysdl.h"
#include "vy_vector.h"

extern VyRepr vyrView;

struct Window {
   struct Group group;
};

VyRepr vyrWindow;

extern VyRepr vyrGroup;

void vyDestroyWindow( VyPtr ) {
   vyThrow("stub vyDestroyWindow");
}

Group vyWindowCast( Window x ) { return (Group)x; }

static Window vyWindowCreateWindow(  ) {
   vyThrow("stub vyWindowCreateWindow");
}

static void vyWindowAdd( Window, View ) {
   vyThrow("stub vyWindowAdd");
}

static void vyWindowRemove( Window, View ) {
   vyThrow("stub vyWindowRemove");
}

static float vyWindowCoord( Window, VyViewCoord ) {
   vyThrow("stub vyWindowCoord");
}

static void vyWindowSetCoord( Window, VyViewCoord, float ) {
   vyThrow("stub vyWindowSetCoord");
}

void vyInitWindow( VyContext ctx ) {
   VYWINDOWARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyrWindow = vyRepr( sizeof(struct Window), vySetRef, vyDestroyWindow);
   vyArgsImpl( args, "cast", vyWindowCast );
   vyArgsImpl( args, "createWindow", vyWindowCreateWindow );
   vyArgsImpl( args, "add", vyWindowAdd );
   vyArgsImpl( args, "remove", vyWindowRemove );
   vyArgsImpl( args, "coord", vyWindowCoord );
   vyArgsImpl( args, "setCoord", vyWindowSetCoord );
   vyAddImplem( ctx, args );
}

