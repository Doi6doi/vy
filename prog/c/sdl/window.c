#include <vy_implem.h>
#include <vy_ui.h>
#include <vy_window.h>
#include <vy_group.h>
#include <SDL2/SDL.h>

#include "vysdl.h"

extern VyRepr vyrView;

struct Window {
   struct Group group;
   SDL_Window * sdl;
};

VyRepr vyrWindow;

extern VyRepr vyrGroup;

void vyDestroyWindow( VyPtr ) {
   vyThrow("stub vyDestroyWindow");
}

static Window vyWindowCreateWindow() {
   Window ret = vyAlloc( vyrWindow );
   vySdlGroupInit( (Group)ret );
   ret->sdl = SDL_CreateWindow( "",
      SDL_WINDOWPOS_CENTERED, SDL_WINDOWPOS_CENTERED,
      vySdl.displayMode.w, vySdl.displayMode.h,
      SDL_WINDOW_MAXIMIZED );
   if ( ! ret->sdl )
      vySdlError( "SDL Window create error" );
   return ret;
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

void vySdlInitWindow( VyContext ctx ) {
   VYWINDOWARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsType( args, "Sub", vyrView );
   vyrWindow = vyRepr( sizeof(struct Window), vySetRef, vyDestroyWindow);
   vyArgsType( args, "Window", vyrWindow );
   vyArgsImpl( args, "createWindow", vyWindowCreateWindow );
   vyArgsImpl( args, "add", vyWindowAdd );
   vyArgsImpl( args, "remove", vyWindowRemove );
   vyArgsImpl( args, "coord", vyWindowCoord );
   vyArgsImpl( args, "setCoord", vyWindowSetCoord );
   vyAddImplem( ctx, args );
}

