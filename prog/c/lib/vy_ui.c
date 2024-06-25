#include "vy_implem.h"
#include "vy_event.h"
#include "vy_view.h"
#include "vy_key.h"

void vyInitUi( VyContext ctx ) {
   vyAddNative( ctx, "VyEventKind", sizeof( VyEventKind ));
   vyAddNative( ctx, "VyViewCoord", sizeof( VyViewCoord ));
   vyAddNative( ctx, "VyKey", sizeof( VyKey ));
}
