#include "vy_implem.h"
#include "vy_keyevent.h"
#include "vy_view.h"

void vyInitUi( VyContext ctx ) {
   vyAddNative( ctx, "VyKeyEventKind", sizeof( VyKeyEventKind ));
   vyAddNative( ctx, "VyEventKind", sizeof( VyEventKind ));
   vyAddNative( ctx, "VyViewCoord", sizeof( VyViewCoord ));
   vyAddNative( ctx, "VyKey", sizeof( VyKey ));
}
