#include <vy_implem.h>
#include <vy_event.h>
#include <vysdl.h>
#include <SDL2/SDL_events.h>

VyRepr vyrEvent = NULL;

#define UNEVENT "Unknown event: %d"

VyEventKind vySdlEventKind( Event e ) {
   switch ( e->sdl.type ) {
      case SDL_KEYDOWN: case SDL_KEYUP: return VE_KEY;
      default:
         snprintf( vySdlBuf, VYSDLBUFSIZE, UNEVENT, e->sdl.type );
         vyThrow( vySdlBuf );
   }
}

void vySdlEventInit( Event e ) {
   vyRefInit( (VyRefCount)e );
   memset( & e->sdl, 0, sizeof( SDL_Event ));
}

void vySdlInitEvent( VyContext ctx ) {
   VYEVENTARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "EventKind", vyNative(ctx,"VyEventKind") );
   vyrEvent = vyRepr( "Event", sizeof(struct Event), vySetRef, vyDestroyCustom);
   vyArgsType( args, "Event", vyrEvent );
   vyArgsImpl( args, "kind", vySdlEventKind );
   vyAddImplem( ctx, args );
}

