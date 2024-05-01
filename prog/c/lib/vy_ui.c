#include "vy_ui.h"
#include "vy_implem.h"

void vyInitUi( VyContext ctx ) {
   vyAddNative( ctx, "VyViewCoord", sizeof( VyViewCoord ));
   vyAddNative( ctx, "VyKey", sizeof( VyKey ));
}
