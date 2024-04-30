#ifndef VY_GEOMH
#define VY_GEOMH

#include "vy.h"
#include <stdint.h>

typedef uint32_t VyColor;

typedef struct VyPoint2f {
   float x;
   float y;
} VyPoint2f;

void vyInitGeom( VyContext );

#endif // VY_GEOMH
