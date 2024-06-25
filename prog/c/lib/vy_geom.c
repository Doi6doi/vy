#include "vy_color.h"
#include "vy_shape.h"
#include "vy_filled.h"
#include "vy_caption.h"

void vyInitGeom( VyContext ctx ) {
   vyAddNative( ctx, "VyColor", sizeof( VyColor ) );
   vyInitShape( ctx );
   vyInitFilled( ctx );
}
