#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_window.h"
#include "vysdl.h"
#include "vy_mem.h"
#include "vy_vector.h"

extern VyRepr vyrView;

extern VyRepr Views;

struct Window {
   SDL_Window * sdl;
   Vector views;
};

VyRepr vyrWindow;

VectorFun vectors;

void vyDestroyWindow( VyPtr ) {
   vyThrow("stub vyDestroyWindow");
}

static void vyWindowSet( Window *, Window ) {
   vyThrow("stub vyWindowSet");
}

static Window vyWindowCreateWindow(  ) {
   Window ret = vyAllocRef( vyrWindow );
   ret->sdl = SDL_CreateWindow( "",
      SDL_WINDOWPOS_CENTERED, SDL_WINDOWPOS_CENTERED,
      vySdl.displayMode.w, vySdl.displayMode.h,
      SDL_WINDOW_MAXIMIZED );
   if ( ! ret->sdl )
      vySdlError( "SDL Window create error" );
   ret->views = vectors.createVector( 0 );
   return ret;
}

static void vyWindowAdd( Window w , View v ) {
   unsigned l = vectors.length( w->views );
   vectors.insert( w->views, l, (VyAny)v );
   vySdlDirty( v );
}

static void vyWindowRemove( Window w, View v) {
   if ( v->wnd != w )
      return;
   Vector views = w->views;
   for ( unsigned i=0; i < vectors.length( views ); ++i ) {
      if ( (VyAny)v == vectors.value( views, i ) ) {
         vySdlDirty( v );
         v->wnd = NULL;
         vectors.remove( views, i );
         return;
      }
   }
}

static float vyWindowCoord( Window, VyViewCoord ) {
   vyThrow("stub vyWindowCoord");
}

static void vyWindowSetCoord( Window, VyViewCoord, float ) {
   vyThrow("stub vyWindowSetCoord");
}

void vySdlInitWindow( VyContext ctx ) {
   VYWINDOWARGS( ctx, args );
   vyArgsType( args, "Sub", vyrView );
   vyrWindow = vyRepr( sizeof(struct Window), false, vyDestroyWindow);
   vyArgsType( args, "Window", vyrWindow );
   vyArgsImpl( args, "set", vyWindowSet );
   vyArgsImpl( args, "createWindow", vyWindowCreateWindow );
   vyArgsImpl( args, "add", vyWindowAdd );
   vyArgsImpl( args, "remove", vyWindowRemove );
   vyArgsImpl( args, "coord", vyWindowCoord );
   vyArgsImpl( args, "setCoord", vyWindowSetCoord );
   vyAddImplem( ctx, args );
}

