#include <vy_implem.h>
#include "vy_transformed.h"
#include "vy_shape.h"
#include "vy_shape.h"

extern VyRepr vyrTransform;

struct Transformed {
   struct Shape shape;
   Transform transform;
   Shape sub;
};

VyRepr vyrTransformed;

extern Transform vyTransformConstIdent();

extern VyRepr vyrShape;

void vyDestroyTransformed( VyPtr ) {
   vyThrow("stub vyDestroyTransformed");
}

static Transformed vyTransformedCreateTransformed( Shape sub ) {
   Transformed ret = vyAllocClear( vyrTransformed );
   vyShapeInit( (Shape)ret );
   vySet( (VyAny *)&ret->sub, sub );
   vySet( (VyAny *)&ret->transform, vyTransformConstIdent() );
   return ret;
}

static Transform vyTransformedTransform( Transformed td ) {
   return td->transform;
}

static Shape vyTransformedSub( Transformed td ) {
   return td->sub;
}

void vyInitTransformed( VyContext ctx ) {
   VYTRANSFORMEDARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Number", vyNative(ctx,"float") );
   vyArgsType( args, "Transform", vyrTransform );
   vyrTransformed = vyRepr( "Transformed", sizeof(struct Transformed), vySetRef, vyDestroyTransformed);
   vyArgsType( args, "Transformed", vyrTransformed );
   vyArgsType( args, "Sub", vyrShape );
   vyArgsImpl( args, "createTransformed", vyTransformedCreateTransformed );
   vyArgsImpl( args, "transform", vyTransformedTransform );
   vyArgsImpl( args, "sub", vyTransformedSub );
   vyAddImplem( ctx, args );
}

