#include <vy_implem.h>
#include "vy_transform.h"

VyRepr vyrTransform;

void vyDestroyTransform( VyPtr ) {
   vyThrow("stub vyDestroyTransform");
}

Transform vyTransformConstIdent() {
   Transform ret = vyAllocClear( vyrTransform );
   vyRefInit( (VyRefCount)ret );
   ret->sx = ret->sy = 1;
   return ret;
}

static float vyTransformConstRotFull(  ) {
   vyThrow("stub vyTransformConstRotFull");
}

static Transform vyTransformCreateIdent(  ) {
   vyThrow("stub vyTransformCreateIdent");
}

static void vyTransformScale( Transform t, float sx, float sy) {
   t->sx *= sx;
   t->sy *= sy;
}

static void vyTransformRotate( Transform, float ) {
   vyThrow("stub vyTransformRotate");
}

static void vyTransformMove( Transform t, float dx, float dy ) {
   t->mx += dx;
   t->my += dy;
}

void vyInitTransform( VyContext ctx ) {
   VYTRANSFORMARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Number", vyNative(ctx,"float") );
   vyrTransform = vyRepr( "Transform", sizeof(struct Transform), vySetRef, vyDestroyTransform);
   vyArgsType( args, "Transform", vyrTransform );
   vyArgsImpl( args, "constIdent", vyTransformConstIdent );
   vyArgsImpl( args, "constRotFull", vyTransformConstRotFull );
   vyArgsImpl( args, "createIdent", vyTransformCreateIdent );
   vyArgsImpl( args, "scale", vyTransformScale );
   vyArgsImpl( args, "rotate", vyTransformRotate );
   vyArgsImpl( args, "move", vyTransformMove );
   vyAddImplem( ctx, args );
}

