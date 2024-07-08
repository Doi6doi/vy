#include <vy_implem.h>
#include <vy_keyevent.h>
#include "vysdl.h"

#define UNEVTYPE "Unknown event type"

VyRepr vyrKeyEvent = NULL;

struct KeyEvent {
   struct Event event;
};

void vySdlKeyEventInit( KeyEvent e ) {
   vySdlEventInit( (Event)e );
}

static VyKeyEventKind vySdlKeyEventKeyKind( KeyEvent e ) {
   switch ( e->event.sdl.type ) {
      case SDL_KEYDOWN: return VKK_DOWN;
      case SDL_KEYUP: return VKK_UP;
      default:
         vyThrow( UNEVTYPE );
         return 0;
   }
}

static VyKey vySdlKeyEventKey( KeyEvent e ) {
   return e->event.sdl.key.keysym.sym;
}

void vySdlInitKeyEvent( VyContext ctx ) {
   VYKEYEVENTARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Key", vyNative(ctx,"VyKey") );
   vyArgsType( args, "KeyEventKind", vyNative(ctx,"VyKeyEventKind") );
   vyArgsType( args, "EventKind", vyNative(ctx,"VyEventKind") );
   vyrKeyEvent = vyRepr( "KeyEvent", sizeof(struct KeyEvent), vySetRef, vyDestroyCustom);
   vyArgsType( args, "KeyEvent", vyrKeyEvent );
   vyArgsImpl( args, "keyKind", vySdlKeyEventKeyKind );
   vyArgsImpl( args, "key", vySdlKeyEventKey );
   vyArgsImpl( args, "kind", vySdlEventKind );
   vyAddImplem( ctx, args );
}

