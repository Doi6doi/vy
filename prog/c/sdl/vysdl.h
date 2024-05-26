#ifndef VYSDLH
#define VYSDLH

#include <SDL2/SDL.h>
#include "vy.h"

#define REALLOC( p, s ) realloc( p,s )

typedef struct View * View;

typedef struct Window * Window;

struct View {
   VyRefCount rc;
   Window wnd;
};

typedef struct VySdl {
   SDL_DisplayMode displayMode;
} VySdl;

extern VySdl vySdl;

void vySdlDirty( View );

void vySdlError( VyCStr );

void vySdlInitKey( VyContext );
void vySdlInitView( VyContext );
void vySdlInitWindow( VyContext );
void vySdlInitSprite( VyContext );

#endif // VYSDLH
