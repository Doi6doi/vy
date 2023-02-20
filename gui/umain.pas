unit umain;

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, ComCtrls, ActnList,
  ExtCtrls, uvconn, SSockets, uvs;

type

  { TFormMain }

  TFormMain = class(TForm)
    AcConnect: TAction;
    ActionList: TActionList;
    ImageList: TImageList;
    LVRemote: TListView;
    LVLocal: TListView;
    PaBottom: TPanel;
    ToolBar: TToolBar;
    ToolButton1: TToolButton;
    procedure AcConnectExecute(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure FormDestroy(Sender: TObject);
  protected
     FLocal: TVSys;
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
      FConn.Remote.Clear;
      AcConnect.Checked := false;
   end else begin
      if mrOK <> FormConnect.ShowModal then Exit;
      if not Connect( FormConnect.Host, FormConnect.Port ) then Exit;
      FConn.Stream := FTCP;
      FConn.UpdateObjs;
      AcConnect.Checked := true;
   end;
   TGui.Editable( LVRemote, AcConnect.Checked );
end;

procedure TFormMain.FormCreate(Sender: TObject);
begin
   FConn := TVConn.Create( FLocal );
   FTCP := nil;
end;


procedure TFormMain.FormDestroy(Sender: TObject);
begin
   FTCP.Free;
   FConn.Free;
   FLocal.Free;
end;


function TFormMain.Connect(Host: String; Port: Word): Boolean;
begin
   try
      FTCP := TInetSocket.Create( Host, Port, DefTimeout );
      FConn.Stream := FTCP;
      FConn.ReadHelo;
      Result := true;
   except on E: Exception do begin
      TGui.Info( E.Message );
      Result := false;
   end; end;
end;

end.

