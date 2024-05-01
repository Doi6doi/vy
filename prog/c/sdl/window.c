#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_window.h"
#include "vysdl.h"

extern VyRepr vyrView;

struct Window {
   VyRepr repr;
   SDL_Window * sdl;
};

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
   Window ret = vyAlloc( vyrWindow );
   ret->sdl = SDL_CreateWindow( "",
      SDL_WINDOWPOS_CENTERED, SDL_WINDOWPOS_CENTERED,
      vySdl.displayMode.w, vySdl.displayMode.h,
      SDL_WINDOW_MAXIMIZED );
   if ( ! ret->sdl )
      vySdlError( "SDL Window create error" );
   return ret;
}

static float vyWindowCoord(Window, VyViewCoord ) {
   vyThrow("stub WindowCoord");
}

static void vyWindowSetCoord(Window, VyViewCoord, float ) {
   vyThrow("stub WindowSetCoord");
}

void vySdlInitWindow( VyContext ctx ) {
   VYWINDOWARGS( ctx, args );
   vyArgsType( args, "Sub", vyrView );
   vyrWindow = vyRepr( sizeof(struct Window), false, destroyWindow);
   vyArgsType( args, "Window", vyrWindow );
   vyArgsImpl( args, "add", vyWindowAdd );
   vyArgsImpl( args, "remove", vyWindowRemove );
   vyArgsImpl( args, "create", vyWindowCreate );
   vyArgsImpl( args, "coord", vyWindowCoord );
   vyArgsImpl( args, "setCoord", vyWindowSetCoord );
   vyAddImplem( ctx, args );
}

