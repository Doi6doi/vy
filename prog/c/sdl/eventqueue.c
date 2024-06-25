#include <vy_implem.h>
#include <vy_eventqueue.h>
#include <vysdl.h>

extern VyRepr vyrEvent;
extern VyRepr vyrKeyEvent;

#define NOEVENT "Cannot poll event"
#define UNEVENT "Unknown event type: %d"

static bool vySdlEventQueueEmpty() {
   return ! SDL_PollEvent( NULL );
}

static Event vySdlEventQueuePoll() {
   SDL_Event e;
   Event ret;
   if ( ! SDL_PollEvent( &e ))
      vyThrow( NOEVENT );
   switch ( e.type ) {
      case SDL_KEYUP: case SDL_KEYDOWN:
         ret = vyAlloc( vyrKeyEvent );
         vySdlKeyEventInit( ret );
      break;
      default:
         snprintf( vySdlBuf, VYSDLBUFSIZE, UNEVENT, e.type );
         vyThrow( vySdlBuf );
   }
   ret->sdl = e;
   return ret;
}

int vySdlFilterEvent( void *, SDL_Event * e ) {
   switch ( e->type ) {
      case SDL_KEYUP: case SDL_KEYDOWN: return 1;
      default: return 0;
   }
}

void vySdlInitEventQueue( VyContext ctx ) {
   SDL_SetEventFilter( vySdlFilterEvent, NULL );
   VYEVENTQUEUEARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "EventKind", vyNative(ctx,"VyEventKind") );
   vyArgsType( args, "Event", vyrEvent );
   vyArgsImpl( args, "empty", vySdlEventQueueEmpty );
   vyArgsImpl( args, "poll", vySdlEventQueuePoll );
   vyAddImplem( ctx, args );
}

