#include "vy.h"
#include "vy_implem.h"
#include "vysdl.h"
#include "vy_ui.h"
#include "vy_key.h"
#include "vy_window.h"
#include "vy_view.h"
#include "vy_sprite.h"


void vyModuleInit( VyContext ctx ) {
   vySdlInitKey( ctx );
   vySdlInitView( ctx );
   vySdlInitWindow( ctx );
   vySdlInitSprite( ctx );
}

