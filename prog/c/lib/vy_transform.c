#include <vy_implem.h>
#include "vy_transform.h"

extern VyRepr vyrTransform;

static Transform vyTransformConstIdent(  ) {
   vyThrow("stub vyTransformConstIdent");
}

static float vyTransformConstRotFull(  ) {
   vyThrow("stub vyTransformConstRotFull");
}

static Transform vyTransformCreateIdent(  ) {
   vyThrow("stub vyTransformCreateIdent");
}

static void vyTransformScale( Transform, float, float ) {
   vyThrow("stub vyTransformScale");
}

static void vyTransformRotate( Transform, float ) {
   vyThrow("stub vyTransformRotate");
}

static void vyTransformMove( Transform, float, float ) {
   vyThrow("stub vyTransformMove");
}

void vyInitTransform( VyContext ctx ) {
   VYTRANSFORMARGS( ctx, args );
   vyArgsType( args, "Bool", vyNative(ctx,"bool") );
   vyArgsType( args, "Number", vyNative(ctx,"float") );
   vyArgsType( args, "Transform", vyrTransform );
   vyArgsImpl( args, "constIdent", vyTransformConstIdent );
   vyArgsImpl( args, "constRotFull", vyTransformConstRotFull );
   vyArgsImpl( args, "createIdent", vyTransformCreateIdent );
   vyArgsImpl( args, "scale", vyTransformScale );
   vyArgsImpl( args, "rotate", vyTransformRotate );
   vyArgsImpl( args, "move", vyTransformMove );
   vyAddImplem( ctx, args );
}

