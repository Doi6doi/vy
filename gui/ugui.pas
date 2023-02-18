unit ugui;

interface

uses
  Classes, SysUtils, Controls, Forms;

type

   { TGui }

   TGui = class
      /// hibaüzenet vezérlőben
      class procedure ControlErr( C: TWinControl; Err: String );
      /// info üzenet
      class procedure Info( Msg: String );
   end;

implementation

{ TGui }

class procedure TGui.ControlErr(C: TWinControl; Err: String);
begin
   C.SetFocus;
   Info( Err );
end;

class procedure TGui.Info(Msg: String);
begin
   Application.MessageBox( PChar(Msg), 'Information' );
end;

end.

