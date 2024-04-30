#include "vy_ui.h"
#include "vy_implem.h"
#include "vy_key.h"
#include "vy_window.h"

void vyInitUi( VyContext ctx ) {
   vyAddNative( ctx, "VyViewCoord", sizeof( VyViewCoord ));
   vyAddNative( ctx, "VyKey", sizeof( VyKey ));
   vyInitKey( ctx );
   vyInitWindow( ctx );
}
