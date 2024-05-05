#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_view.h"

struct View {
   VyRepr repr;
};

VyRepr vyrView;

void destroyView( VyPtr ) {
   vyThrow("stub destroyView");
}

static void vyViewSet( View *, View ) {
   vyThrow("stub ViewSet");
}

static View vyViewCreate( ) {
   vyThrow("stub ViewCreate");
}

static float vyViewCoord(View, VyViewCoord ) {
   vyThrow("stub ViewCoord");
}

static void vyViewSetCoord(View, VyViewCoord, float ) {
   vyThrow("stub ViewSetCoord");
}

void vySdlInitView( VyContext ctx ) {
   VYVIEWARGS( ctx, args );
   vyrView = vyRepr( sizeof(struct View), false, destroyView);
   vyArgsType( args, "View", vyrView );
   vyArgsImpl( args, "set", vyViewSet );
   vyArgsImpl( args, "create", vyViewCreate );
   vyArgsImpl( args, "coord", vyViewCoord );
   vyArgsImpl( args, "setCoord", vyViewSetCoord );
   vyAddImplem( ctx, args );
}

