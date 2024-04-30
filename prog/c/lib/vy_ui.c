#include "vy_ui.h"
#include "vy_implem.h"
#include "vy_key.h"
#include "vy_view.h"
#include "vy_window.h"
#include "vy_sprite.h"

void vyInitUi( VyContext ctx ) {
   vyAddNative( ctx, "VyViewCoord", sizeof( VyViewCoord ));
   vyAddNative( ctx, "VyKey", sizeof( VyKey ));
   vyInitKey( ctx );
   vyInitView( ctx );
   vyInitWindow( ctx );
   vyInitSprite( ctx );
}
