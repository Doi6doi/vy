unit uconnect;

{$mode objfpc}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, StdCtrls;

type

  { TFormConnect }

  TFormConnect = class(TForm)
    BConnect: TButton;
    EHost: TEdit;
    EPort: TEdit;
    LHost: TLabel;
    LPort: TLabel;
    procedure BConnectClick(Sender: TObject);
  protected
    function GetHost: String;
    function GetPort: Word;
  public
    property Host: String read GetHost;
    property Port: Word read GetPort;
  end;

var
  FormConnect: TFormConnect;

implementation

{$R *.lfm}

uses
   ugui;

{ TFormConnect }

procedure TFormConnect.BConnectClick(Sender: TObject);
begin
   EHost.Text := Trim( EHost.Text );
   EPort.Text := Trim( EPort.Text );
   if '' = Host then
      TGui.ControlErr( EHost, 'Please set host' )
   else if 0 = Port then
      TGui.ControlErr( EPort, 'Please set port' )
   else
      ModalResult := mrOK;
end;

function TFormConnect.GetHost: String;
begin
   Result := EHost.Text;
end;

function TFormConnect.GetPort: Word;
var
   I: Integer;
begin
   if TryStrToInt( EPort.Text, I )
      then Result := I
      else Result := 0;
end;

end.

