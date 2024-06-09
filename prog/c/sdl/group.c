#include <vy_implem.h>
#include <vy_ui.h>
#include <vy_view.h>
#include <vy_group.h>

#include "vysdl.h"

VyRepr vyrGroup;

extern VyRepr vyrView;

void vySdlGroupInit( Group g ) {
   vySdlViewInit( (View)g );
   g->items = vySdlVectors.createVector();
}

void vyDestroyGroup( VyPtr ) {
   vyThrow("stub vyDestroyGroup");
}

static void vyGroupAdd( Group, View ) {
   vyThrow("stub vyGroupAdd");
}

static void vyGroupRemove( Group, View ) {
   vyThrow("stub vyGroupRemove");
}

static float vyGroupCoord( Group, VyViewCoord ) {
   vyThrow("stub vyGroupCoord");
}

static void vyGroupSetCoord( Group, VyViewCoord, float ) {
   vyThrow("stub vyGroupSetCoord");
}

void vySdlInitGroup( VyContext ctx ) {
   VYGROUPARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyrGroup = vyRepr( sizeof(struct Group), vySetRef, vyDestroyGroup);
   vyArgsType( args, "Group", vyrGroup );
   vyArgsType( args, "Sub", vyrView );
   vyArgsImpl( args, "add", vyGroupAdd );
   vyArgsImpl( args, "remove", vyGroupRemove );
   vyArgsImpl( args, "coord", vyGroupCoord );
   vyArgsImpl( args, "setCoord", vyGroupSetCoord );
   vyAddImplem( ctx, args );
}

