#ifndef VYSDLH
#define VYSDLH

#include <SDL2/SDL.h>
#include "vy.h"

typedef struct VySdl {
   SDL_DisplayMode displayMode;
} VySdl;

extern VySdl vySdl;

void vySdlError( VyCStr );

void vySdlInitKey( VyContext );
void vySdlInitView( VyContext );
void vySdlInitWindow( VyContext );
void vySdlInitSprite( VyContext );

#endif // VYSDLH
