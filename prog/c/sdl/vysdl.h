#ifndef VYSDLH
#define VYSDLH

#include <SDL2/SDL.h>
#include "vy.h"
#include "vy_vector.h"

#define REALLOC( p, s ) realloc( p,s )

typedef struct View * View;

typedef struct Group * Group;

struct View {
   VyRefCount rc;
   Group group;
};

struct Group {
   View view;
   Vector items;
};

typedef struct VySdl {
   SDL_DisplayMode displayMode;
} VySdl;

extern VySdl vySdl;
extern VectorFun vySdlVectors;

void vySdlDirty( View );

void vySdlError( VyCStr );

void vySdlInitKey( VyContext );
void vySdlInitView( VyContext );
void vySdlInitGroup( VyContext );
void vySdlInitWindow( VyContext );
void vySdlInitSprite( VyContext );

void vySdlGroupInit( Group );
void vySdlViewInit( View );

#endif // VYSDLH
