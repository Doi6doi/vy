#include <vy_implem.h>
#include <vy_event.h>
#include <vysdl.h>
#include <SDL2/SDL_events.h>

VyRepr vyrEvent = NULL;

#define UNEVENT "Unknown event: %d"

static VyEventKind vyEventKind( Event e ) {
   switch ( e->sdl.type ) {
      case SDL_KEYDOWN: case SDL_KEYUP: return VE_KEY;
      default: 
         snprintf( vySdlBuf, VYSDLBUFSIZE, UNEVENT, e->sdl.type );
         vyThrow( vySdlBuf );
   }
}

void vySdlInitEvent( VyContext ctx ) {
   VYEVENTARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "EventKind", vyNative(ctx,"VyEventKind") );
   vyArgsType( args, "Event", vyrEvent );
   vyArgsImpl( args, "kind", vyEventKind );
   vyAddImplem( ctx, args );
}

