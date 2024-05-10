#include <vy_implem.h>
#include "vy_ui.h"
#include "vy_view.h"

struct View {
   VyRepr repr;
};

VyRepr vyrView;

void vyDestroyView( VyPtr ) {
   vyThrow("stub vyDestroyView");
}

static void vyViewSet( View *, View ) {
   vyThrow("stub vyViewSet");
}

static float vyViewCoord( View, VyViewCoord ) {
   vyThrow("stub vyViewCoord");
}

static void vyViewSetCoord( View, VyViewCoord, float ) {
   vyThrow("stub vyViewSetCoord");
}

void vySdlInitView( VyContext ctx ) {
   VYVIEWARGS( ctx, args );
   vyrView = vyRepr( sizeof(struct View), false, vyDestroyView);
   vyArgsType( args, "View", vyrView );
   vyArgsImpl( args, "set", vyViewSet );
   vyArgsImpl( args, "coord", vyViewCoord );
   vyArgsImpl( args, "setCoord", vyViewSetCoord );
   vyAddImplem( ctx, args );
}

