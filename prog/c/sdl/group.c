#include <vy_implem.h>
#include <vy_ui.h>
#include <vy_view.h>
#include <vy_group.h>

#include "vysdl.h"

VyRepr vyrGroup;

extern VyRepr vyrView;

static int vySdlFind( Vector v, VyAny a ) {
   int n = vySdlVectors.length(v);
   for ( int ret = 0; ret < n; ++ret ) {
      if ( vySdlVectors.value(v,ret) == a )
         return ret;
   }
   return -1;
}
   

void vySdlGroupInit( Group g ) {
   vySdlViewInit( (View)g );
   g->items = vySdlVectors.createVector();
}

void vySdlDestroyGroup( VyPtr ) {
   vyThrow("stub vySdlDestroyGroup");
}

void vySdlGroupAdd( Group g, View v ) {
   if ( ! v )
      return;
   if ( v->group == g )
      return;
   if ( v->group ) {
      vySdlInvalidate( v );
      int i = vySdlFind( v->group->items, (VyAny)v );
      vySdlVectors.remove( v->group->items, i );
   }
   unsigned l = vySdlVectors.length( g->items );
   vySdlVectors.insert( g->items, l, (VyAny)v );
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

