#include <vy_implem.h>
#include <vy_keyevent.h>

VyRepr vyrKeyEvent = NULL;

static VyKeyEventKind vyKeyEventKeyKind( KeyEvent ) {
   vyThrow("stub vyKeyEventKeyKind");
}

static VyKey vyKeyEventKey( KeyEvent ) {
   vyThrow("stub vyKeyEventKey");
}

static VyEventKind vyKeyEventKind( KeyEvent ) {
   vyThrow("stub vyKeyEventKind");
}

void vySdlInitKeyEvent( VyContext ctx ) {
   VYKEYEVENTARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Key", vyNative(ctx,"VyKey") );
   vyArgsType( args, "KeyEventKind", vyNative(ctx,"VyKeyEventKind") );
   vyArgsType( args, "EventKind", vyNative(ctx,"VyEventKind") );
   vyArgsType( args, "KeyEvent", vyrKeyEvent );
   vyArgsImpl( args, "keyKind", vyKeyEventKeyKind );
   vyArgsImpl( args, "key", vyKeyEventKey );
   vyArgsImpl( args, "kind", vyKeyEventKind );
   vyAddImplem( ctx, args );
}

