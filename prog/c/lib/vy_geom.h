#ifndef VY_GEOMH
#define VY_GEOMH

#include "vy.h"
#include <stdint.h>

typedef uint32_t VyColor;

typedef enum VyViewCoord { VC_NONE, VC_LEFT, VC_TOP, VC_WIDTH, VC_HEIGHT, 
   VC_RIGHT, VC_BOTTOM, VC_CENTERX, VC_CENTERY } VyViewCoord;

typedef struct VyPoint2f {
   float x;
   float y;
} VyPoint2f;



#endif // VY_GEOMH
