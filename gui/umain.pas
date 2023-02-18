unit umain;

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ComCtrls, ActnList,
  uvconn, SSockets, uvs;

type

  { TFormMain }

  TFormMain = class(TForm)
    AcConnect: TAction;
    ActionList: TActionList;
    ImageList: TImageList;
    ToolBar: TToolBar;
    ToolButton1: TToolButton;
    procedure AcConnectExecute(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
  protected
     FVSys: TVSys;
     FConn: TVConn;
     FTCP: TInetSocket;
     function Connect( Host: String; Port: Word ): Boolean;
  public
  end;

var
  FormMain: TFormMain;

implementation

{$R *.lfm}

uses
   uconnect, ugui;

const
   DefTimeout = 30000;

{ TFormMain }

procedure TFormMain.AcConnectExecute(Sender: TObject);
begin
   if AcConnect.Checked then begin
      FConn.Stream := nil;
      FreeAndNil( FTCP );
      AcConnect.Checked := false;
   end else begin
      if mrOK <> FormConnect.ShowModal then Exit;
      if not Connect( FormConnect.Host, FormConnect.Port ) then Exit;
      FConn.Stream := FTCP;
      AcConnect.Checked := true;
   end;
end;

procedure TFormMain.FormCreate(Sender: TObject);
begin
   FVSys := TVSys.Create;
   FConn := TVConn.Create( FVSys );
   FTCP := nil;
end;

procedure TFormMain.FormDestroy(Sender: TObject);
begin
   FTCP.Free;
   FConn.Free;
   FVSys.Free;
end;

function TFormMain.Connect(Host: String; Port: Word): Boolean;
begin
   try
      FTCP := TInetSocket.Create( Host, Port, DefTimeout );
      FConn.Stream := FTCP;
      FConn.ReadInit;
      Result := true;
   except on E: Exception do begin
      TGui.Info( E.Message );
      Result := false;
   end; end;
end;

end.

