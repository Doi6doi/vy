#include "vy_geom.h"
#include "vy_color.h"
#include "vy_shape.h"
#include "vy_rect.h"
#include "vy_circle.h"
#include "vy_filled.h"
#include "vy_caption.h"

void vyInitGeom( VyContext ctx ) {
   vyInitColor( ctx );
   vyInitShape( ctx );
   vyInitRect( ctx );
   vyInitCircle( ctx );
   vyInitCaption( ctx );
   vyInitFilled( ctx );
}
