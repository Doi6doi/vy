#include <vy_implem.h>
#include <vy_view.h>
#include <vy_group.h>

#include "vysdl.h"
#include <stdio.h>

VyRepr vyrGroup;

#define AREA( a ) ((a)->width * (a)->height)

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
   if ( v->group ) {
      vySdlInvalidate( v );
      int i = vySdlFind( v->group->items, (VyAny)v );
      vectors->remove( v->group->items, i );
   }
   unsigned l = vectors->length( g->items );
   vectors->insert( g->items, l, (VyAny)v );
   v->group = g;
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

void vySdlInvalidateGroup( Group g, VySdlArea a ) {
   float aa = vySdlAreaArea( a );
   struct VySdlArea u;
   for (int i=0; i < g->dirty.count; ++i) {
      VySdlArea di = (VySdlArea)vyVecGet( & g->dirty, i );
      vySdlUnion( di, a, & u );
      if ( vySdlAreaArea( & u ) <= aa + vySdlAreaArea( di )) {
         (*di) = u;
         return;
      }
   }
   vyVecAdd( & g->dirty, a );
}

