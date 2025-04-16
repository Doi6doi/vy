class vyi.game.pong.Score @24 {

   extend {
      vy.ui.Sprite;
   }

   import {
      vy.geom.Filled;
      vy.num.Uint;
      vy.core.Cast { Cast=Uint, To=String };
   }

   field {
      caption: Caption;
      score: Uint;
   }

   method {
      Score( score: Uint, color: Color ) {
         super( Filled( caption, color ) );
         caption.text := score.cast;
      }
   }

}

