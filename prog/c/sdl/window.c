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

void vySdlDestroyWindow( VyPtr ) {
   vyThrow("stub vySdlDestroyWindow");
}

static Window vySdlWindowCreateWindow() {
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

static void vySdlWindowRemove( Window, View ) {
   vyThrow("stub vySdlWindowRemove");
}

static float vySdlWindowCoord( Window, VyViewCoord ) {
   vyThrow("stub vySdlWindowCoord");
}

static void vySdlWindowSetCoord( Window, VyViewCoord, float ) {
   vyThrow("stub vySdlWindowSetCoord");
}

void vySdlInitWindow( VyContext ctx ) {
   VYWINDOWARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsType( args, "Sub", vyrView );
   vyrWindow = vyRepr( "Window", sizeof(struct Window), vySetRef, vySdlDestroyWindow);
   vyArgsType( args, "Window", vyrWindow );
   vyArgsImpl( args, "createWindow", vySdlWindowCreateWindow );
   vyArgsImpl( args, "add", vySdlGroupAdd );
   vyArgsImpl( args, "remove", vySdlWindowRemove );
   vyArgsImpl( args, "coord", vySdlWindowCoord );
   vyArgsImpl( args, "setCoord", vySdlWindowSetCoord );
   vyAddImplem( ctx, args );
}

