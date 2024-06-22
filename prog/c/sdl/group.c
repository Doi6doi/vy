#include <vy_implem.h>
#include <vy_ui.h>
#include <vy_view.h>
#include <vy_group.h>

#include "vysdl.h"
#include <stdio.h>

VyRepr vyrGroup;

extern VyRepr vyrView;

static int vySdlFind( Vector v, VyAny a ) {
   int n = vySdl.vectors.length(v);
   for ( int ret = 0; ret < n; ++ret ) {
      if ( vySdl.vectors.value(v,ret) == a )
         return ret;
   }
   return -1;
}
   

void vySdlGroupInit( Group g ) {
   vySdlViewInit( (View)g );
   g->items = vySdl.vectors.createVector();
   vyVecInit( & g->dirty, sizeof( struct VySdlArea ), 8 );
printf("groupinit %p %p\n", g, & g->dirty );
fflush( stdout );
}

void vySdlDestroyGroup( VyPtr ) {
   vyThrow("stub vySdlDestroyGroup");
}

void vySdlGroupAdd( Group g, View v ) {
   if ( ! v )
      return;
   if ( v->group == g )
      return;
   VectorFun * vectors = & vySdl.vectors;
printf( "groupadd %p %p %p\n", g, v, v->group );   
   if ( v->group ) {
// printf( "groupadd2 %p %p %p\n", g, v, v->group );   
      vySdlInvalidate( v );
      int i = vySdlFind( v->group->items, (VyAny)v );
      vectors->remove( v->group->items, i );
   }
   unsigned l = vectors->length( g->items );
// printf( "groupadd3 %p %p %p\n", g, v, v->group );   
   vectors->insert( g->items, l, (VyAny)v );
   v->group = g;
printf( "groupadd4 %p %p %p\n", g, v, v->group );   
   vySdlInvalidate( v );
}

static void vySdlGroupRemove( Group, View ) {
   vyThrow("stub vySdlGroupRemove");
}

void vySdlInitGroup( VyContext ctx ) {
   VYGROUPARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "ViewCoord", vyNative(ctx,"VyViewCoord") );
   vyArgsType( args, "Coord", vyNative(ctx,"float") );
   vyrGroup = vyRepr( "Group", sizeof(struct Group), vySetRef, vySdlDestroyGroup);
   vyArgsType( args, "Group", vyrGroup );
   vyArgsType( args, "Sub", vyrView );
   vyArgsImpl( args, "add", vySdlGroupAdd );
   vyArgsImpl( args, "remove", vySdlGroupRemove );
   vyArgsImpl( args, "coord", vySdlViewCoord );
   vyArgsImpl( args, "setCoord", vySdlViewSetCoord );
   vyAddImplem( ctx, args );
}

void vySdlInvalidateGroup( Group g, struct VySdlArea a ) {
   struct VySdlArea aa = a;
printf("invalGroup %p %p\n", g, &a );
fflush( stdout );
   for (int i=0; i < g->dirty.count; ++i) {
printf("i: %d\n", i );
fflush( stdout );
      VySdlArea di = (VySdlArea)vyVecGet( & g->dirty, i );
printf("di: %p %p\n", di, &aa );
fflush( stdout );
      if ( vySdlOverlaps( di, & aa, 1 )) {
         vySdlJoin( di, &a );
         return;
      }
   }
printf("add\n" );
fflush( stdout );
   vyVecAdd( & g->dirty, &a );
}

