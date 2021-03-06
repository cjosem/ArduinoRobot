//Source code sample showing how to make a call (use headphones and mic to talk),
//and how to control the General Purpose I/O pins and PWM
 
#include <NewSoftSerial.h>
 
NewSoftSerial mySerial(7, 8);
 
void setup()
{
  delay(10000);
  mySerial.begin(19200);               // the GPRS baud rate   
  Serial.begin(19200);               // the GPRS baud rate   
  delay(2000);
}
 
void loop()
{
  int count=0;
 
  mySerial.println("ATD99268744;"); // xxxxxxxxx is the number you want to dial.
  delay(2000); 
     while(1)
     {
       mySerial.println("AT+SPWM=2,63,100");// set PWM 2 PIN
       delay(100);  
       mySerial.println("AT+SPWM=1,63,100");
       delay(100);       
       mySerial.println("AT+SGPIO=0,1,1,1");// set GPIO 1 PIN to 1
       delay(100);
       mySerial.println("AT+SGPIO=0,2,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,3,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,4,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,5,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,6,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,7,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,8,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,9,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,10,1,1");
       delay(100);       
       mySerial.println("AT+SGPIO=0,11,1,1");
       delay(100);
       mySerial.println("AT+SGPIO=0,12,1,1");
 
       delay(500);
 
       mySerial.println("AT+SPWM=1,63,0");
       delay(100);  
       mySerial.println("AT+SPWM=2,63,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,1,1,0"); // set GPIO 1 PIN to 0
       delay(100);
       mySerial.println("AT+SGPIO=0,2,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,3,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,4,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,5,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,6,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,7,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,8,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,9,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,10,1,0");
       delay(100);       
       mySerial.println("AT+SGPIO=0,11,1,0");
       delay(100);
       mySerial.println("AT+SGPIO=0,12,1,0");
       delay(500);
 
       count++;
 
       if(count==5)
       {
         mySerial.println("ATH"); //end the call.
         if(mySerial.available())
        {
           Serial.print((unsigned char)mySerial.read());
 
         }  
       }  
     }  
 
}
