unit ugui;

interface

uses
  Classes, SysUtils, Controls, Forms, Graphics;

type

   { TGui }

   TGui = class
   protected
      /// szerkeszthető szín
      class function EditableColor( C: TControl; Fore: Boolean; Editable: Boolean ): TColor;
   public
      /// hibaüzenet vezérlőben
      class procedure ControlErr( C: TWinControl; Err: String );
      /// info üzenet
      class procedure Info( Msg: String );
      /// szerkeszthetőség állítás
      class procedure Editable( C: TControl; Editable: Boolean );
   end;

implementation

class procedure TGui.ControlErr(C: TWinControl; Err: String);
begin
   C.SetFocus;
   Info( Err );
end;

class procedure TGui.Info(Msg: String);
begin
   Application.MessageBox( PChar(Msg), 'Information' );
end;


class procedure TGui.Editable( C: TControl; Editable: Boolean );
begin
   C.Enabled := Editable;
   C.Color := EditableColor( C, false, Editable );
   C.Font.Color := EditableColor( C, true, Editable );
end;


class function TGui.EditableColor( C: TControl;
     Fore: Boolean; Editable: Boolean ): TColor;
begin
   if Fore then begin
      if Editable
         then Result := clDefault
         else Result := clBtnShadow;
   end else begin
      if Editable
         then Result := clDefault
         else Result := clBtnFace;
   end;
end;

end.

