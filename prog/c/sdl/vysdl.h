#ifndef VYSDLH
#define VYSDLH

#include <vy.h>
#include <SDL2/SDL_events.h>
#include <vy_vector.h>
#include <vy_event.h>
#include <vy_filled.h>
#include <vy_caption.h>
#include <vy_view.h>
#include <vy_sprite.h>
#include <vy_transformed.h>
#include <vy_vec.h>

#define VYSDLBUFSIZE 4096

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

struct Event {
   struct VyRefCount ref;
   SDL_Event sdl;
};

typedef struct VySdlArea {
   float top, left, width, height;
} * VySdlArea;

extern char vySdlBuf[VYSDLBUFSIZE] ;

extern char * dvs_mini_data;
extern unsigned dvs_mini_len;

extern void vySdlUnion( VySdlArea a, VySdlArea b, VySdlArea u );
extern float vySdlAreaArea( VySdlArea );
extern void vySdlAreaDump( VySdlArea );

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

extern void vySdlInvalidate( View );
extern void vySdlInvalidateGroup( Group, VySdlArea );
extern void vySdlRemove( Group g, View v );

extern VyEventKind vySdlEventKind( Event );

extern float vySdlFontHeight( Font );
extern float vySdlFontWidth( Font, String );

extern Font vySdlCaptionFont( Caption );
extern String vySdlCaptionText( Caption );

extern void vySdlGroupInit( Group );
extern void vySdlGroupAdd( Group, View );

extern void vySdlViewInit( View );
extern void vySdlViewArea( View, VySdlArea );
extern float vySdlViewCoord( View, VyViewCoord );
extern void vySdlViewSetCoord( View, VyViewCoord, float );

extern float vySdlSpriteCoord( Sprite, VyViewCoord );

#endif // VYSDLH
