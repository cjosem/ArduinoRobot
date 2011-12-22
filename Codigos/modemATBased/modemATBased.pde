//#define debug_ModemATBased

#include <ModemATBased.h>

const String numero       =  "99268744";
const String mensagem     =  "Ola mundo. Esta e uma nova mensagem de teste feita para ser transmitida pelo robo.";

const String host         =  "kemper.com.br";
const String hostPort     =  String ( 80, DEC );
const String queryString  =  "/modem/modem.php?contador=";

unsigned int contadorLoop          =  0;

void setup ()
{
  Serial.begin ( 19200 );
  
  while (true)
  {
    if (Serial.available())
      if ((unsigned char)Serial.read()=='c')
        break;
  }
  ModemATBased::setSerial ( SerialPort::Port1, 19200 );
  ModemATBased::internetConnect ( &internetConnect );
  

  //ModemATBased::sendTextSms ( &numero, &mensagem, &pisca );
}

void internetConnect ()
{
    ModemATBased::internetConnectToHost ( &host, &hostPort, &sendDataLoop );
}

void sendDataLoop ()
{
  /*String query=  "/modem/modem.php?contador=0";
  String data =  query.concat( String ( contadorLoop, DEC ) );
  const String * p;
  p = &data;*/
  
  
 /* const String * a = &queryString;
  String lixo;
  String b = "100";
  String c = "/modem/modem.php?contador=";//*a;
  String d = c.concat( b );
  const String * e = &c;
  */
  ModemATBased::QueryString =  "/modem/modem.php?contador=";
  ModemATBased::QueryString.concat ( String ( contadorLoop, DEC ) );
  ModemATBased::internetDataSendByGET ( &host, &hostPort, &sendDataLoop );
  contadorLoop ++;
}

void internetSendData ()
{
  pisca ();
}

void pisca ()
{
  while ( true )
  {
    pinMode ( 13, OUTPUT );
    digitalWrite ( 13, HIGH );
    delay ( 300 );
    digitalWrite ( 13, LOW );
    delay ( 300 );
  }
}

void loop ()
{
  ModemATBased::getDataModem ();
}
