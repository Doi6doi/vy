#ifndef VYSDLH
#define VYSDLH

#include <vy.h>
#include <vy_ui.h>
#include <vy_geom.h>
#include <vy_vector.h>
#include <vy_filled.h>
#include <vy_rect.h>
#include <vy_circle.h>
#include <vy_sprite.h>

#define REALLOC( p, s ) realloc( p,s )

typedef struct VySdl {
   int width;
   int height;
   float aspect;
   VectorFun vectors;
   FilledFun filleds;
   RectFun rects;
   CircleFun circles;
   VyRepr Filled;
   VyRepr Rect;  
   VyRepr Circle;
} VySdl;

typedef struct View * View;

typedef struct Group * Group;

struct View {
   VyRefCount rc;
   Group group;
   float x;
   float y;
};

struct Group {
   View view;
   Vector items;
};

typedef struct VySdlArea {
   VyRefCount ref;
   float top, left, width, height;
} * VySdlArea;

extern VySdlArea vySdlArea( float top, float left, float width, float height );

extern VySdl vySdl;

extern void vySdlDirty( View );

extern void vySdlError( VyCStr );

extern void vySdlInitKey( VyContext );
extern void vySdlInitView( VyContext );
extern void vySdlInitGroup( VyContext );
extern void vySdlInitWindow( VyContext );
extern void vySdlInitSprite( VyContext );

extern void vySdlInvalidate( View );
extern void vySdlInvalidateGroup( Group, VySdlArea );
extern VySdlArea vySdlViewArea( View );
extern void vySdlRemove( Group g, View v );

extern void vySdlGroupInit( Group );
extern void vySdlGroupAdd( Group, View );

extern void vySdlViewInit( View );
extern float vySdlViewCoord( View, VyViewCoord );
extern void vySdlViewSetCoord( View, VyViewCoord, float );

extern float vySdlSpriteCoord( Sprite, VyViewCoord );

#endif // VYSDLH
