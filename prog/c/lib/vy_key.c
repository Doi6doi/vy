#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_key.h"

static VyKey vyKeyConstUtf(VyCStr, VySize ) {
   vyThrow("stub KeyConstUtf");
}

static bool vyKeyPressed(VyKey ) {
   vyThrow("stub KeyPressed");
}

static bool vyKeyEqual(VyKey, VyKey ) {
   vyThrow("stub KeyEqual");
}

static bool vyKeyNoteq(VyKey, VyKey ) {
   vyThrow("stub KeyNoteq");
}

void vyInitKey( VyContext ctx ) {
   VYKEYARGS( ctx, args );
   vyArgsImpl( args, "constUtf", vyKeyConstUtf );
   vyArgsImpl( args, "pressed", vyKeyPressed );
   vyArgsImpl( args, "equal", vyKeyEqual );
   vyArgsImpl( args, "noteq", vyKeyNoteq );
   vyAddImplem( ctx, args );
}

