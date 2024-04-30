#ifndef VYUIH
#define VYUIH

#include <stdint.h>

#include <vy.h>

typedef uint32_t VyKey;

typedef enum VyViewCoord { VC_NONE, VC_LEFT, VC_TOP, VC_WIDTH, VC_HEIGHT,
   VC_RIGHT, VC_BOTTOM, VC_CENTERX, VC_CENTERY } VyViewCoord;

void vyInitUi( VyContext );

#endif // VYUIH
