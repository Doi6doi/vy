#include <vy_implem.h>
#include <vy_ui.h>
#include <vy_view.h>

#include "vysdl.h"

VyRepr vyrView;

void vySdlViewInit( View ) {
}

void vyDestroyView( VyPtr ) {
   vyThrow("stub vyDestroyView");
}

static float vyViewCoord( View, VyViewCoord ) {
   vyThrow("stub vyViewCoord");
}

static void vyViewSetCoord( View, VyViewCoord, float ) {
   vyThrow("stub vyViewSetCoord");
}

void vySdlInitView( VyContext ctx ) {
   VYVIEWARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyrView = vyRepr( sizeof(struct View), vySetRef, vyDestroyView);
   vyArgsType( args, "View", vyrView );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyArgsImpl( args, "coord", vyViewCoord );
   vyArgsImpl( args, "setCoord", vyViewSetCoord );
   vyAddImplem( ctx, args );
}

