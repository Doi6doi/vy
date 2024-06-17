#include <vy_implem.h>
#include "vy_transformed.h"

#include "vy_shape.h"

extern VyRepr vyrTransform;

struct Transformed {
   struct Shape shape;
   Transform transform;
   Shape sub;
};

VyRepr vyrTransformed;

extern VyRepr vyrShape;

extern VyRepr vyrShape;

void vyDestroyTransformed( VyPtr ) {
   vyThrow("stub vyDestroyTransformed");
}

static Transformed vyTransformedCreateTransformed( Shape ) {
   vyThrow("stub vyTransformedCreateTransformed");
}

static Transform vyTransformedTransform( Transformed ) {
   vyThrow("stub vyTransformedTransform");
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
   vyAddImplem( ctx, args );
}

