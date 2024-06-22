#ifndef VYSDLH
#define VYSDLH

#include <vy.h>
#include <vy_ui.h>
#include <vy_geom.h>
#include <vy_vector.h>
#include <vy_filled.h>
#include <vy_caption.h>
#include <vy_sprite.h>
#include <vy_transformed.h>
#include <vy_vec.h>

#define REALLOC( p, s ) realloc( p,s )

typedef struct VySdl {
   int width;
   int height;
   float aspect;
   VectorFun vectors;
   FilledFun filleds;
   TransformedFun transformeds;
   VyRepr Filled;
   VyRepr Square;
   VyRepr Circle;
   VyRepr Transformed;
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
   struct VyVec dirty;
};

typedef struct VySdlArea {
   float top, left, width, height;
} * VySdlArea;

extern char * dvs_mini_data;
extern unsigned dvs_mini_len;

extern void vySdlSetArea( VySdlArea area, float top, float left, float width, float height );
extern bool vySdlOverlaps( VySdlArea a, VySdlArea b, float percent );
extern void vySdlJoin( VySdlArea a, VySdlArea other );

extern VySdl vySdl;

extern void vySdlDirty( View );

extern void vySdlError( VyCStr );

extern void vySdlInitCaption( VyContext );
extern void vySdlInitColor( VyContext );
extern void vySdlInitFont( VyContext );
extern void vySdlInitKey( VyContext );
extern void vySdlInitView( VyContext );
extern void vySdlInitGroup( VyContext );
extern void vySdlInitWindow( VyContext );
extern void vySdlInitSprite( VyContext );

extern struct VySdlArea vySdlViewArea( View );
extern void vySdlInvalidate( View );
extern void vySdlInvalidateGroup( Group, struct VySdlArea );
extern void vySdlRemove( Group g, View v );

extern float vySdlFontHeight( Font );
extern float vySdlFontWidth( Font, String );

extern Font vySdlCaptionFont( Caption );
extern String vySdlCaptionText( Caption );

extern void vySdlGroupInit( Group );
extern void vySdlGroupAdd( Group, View );

extern void vySdlViewInit( View );
extern float vySdlViewCoord( View, VyViewCoord );
extern void vySdlViewSetCoord( View, VyViewCoord, float );

extern float vySdlSpriteCoord( Sprite, VyViewCoord );

#endif // VYSDLH
