#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <Hash.h>
#include <ESPAsyncTCP.h>
#include <ESPAsyncWebServer.h>
#include <Adafruit_Sensor.h>
#include <DHT.h>


const char* ssid = "OnePlus Nord 2 5G";
const char* password = "vouligaranet";

/*String serverName = "http://192.168.148.222/telemetry_insert.php";
/String serverActuator = "http://192.168.148.222/actuator_get_state.php";*/

String serverName = "http://sdmfctms.atwebpages.com/iot/telemetry_insert.php";
String serverActuator = "http://sdmfctms.atwebpages.com/iot/actuator_get_state.php";
String systemStateServer = "http://sdmfctms.atwebpages.com/iot/system_get_state.php";


#define DHTPIN 5 //D1
#define BUTTONPIN 14 //D5
#define ACPIN 4 //D2
#define DEHUMIPIN 12 //D6

#define TEMPERATUREMAX 30
#define TEMPERATUREMIN 20
#define HUMIDITYMAX 75
#define HUMIDITYMIN 50

int systemState = LOW;
int lastButtonState = HIGH;
int buttonState = HIGH;
bool toggleState = false;

// Uncomment the type of sensor in use:
#define DHTTYPE    DHT11     // DHT 11
//#define DHTTYPE    DHT22     // DHT 22 (AM2302)
//#define DHTTYPE    DHT21     // DHT 21 (AM2301)

DHT dht(DHTPIN, DHTTYPE);

// current temperature & humidity, updated in loop()
float t = 0.0;
float h = 0.0;

unsigned long lastTime = 0;
unsigned long lastACTime = 0;
unsigned long lastDehumiTime = 0;
unsigned long lastSystemTime = 0;
unsigned long timerDelay = 5000;    // 1 measurement per minute
unsigned long acDelay = 5000;
unsigned long dehumiDelay = 5000;
unsigned long systemDelay = 5000;

void setACState(){
  
    if((millis() - lastACTime) > acDelay){
        if(WiFi.status()==WL_CONNECTED){
            WiFiClient client;
            HTTPClient http;

            String serverPath = serverActuator + "?actuator_id=" + 1;
            http.begin(client, serverPath.c_str());

            int httpResponseCode = http.GET();

            if(httpResponseCode>0){
                //Serial.print("HTTP Response code: ");
                //Serial.print("httpResponseCode");
                String payload = http.getString();
                Serial.println("setting the AC to state: " + payload);
                if(payload == "on"){
                    digitalWrite(ACPIN, HIGH);
                } else if(payload == "off"){
                    digitalWrite(ACPIN, LOW);
                }
            } else{
                Serial.print("Error code:");
                Serial.println("httpResponseCode");
            }
            // Free Resources
            http.end();
            lastACTime = millis();
        }
    }
}

void setDehumiState(){
  
    if((millis() - lastDehumiTime) > dehumiDelay){
        if(WiFi.status()==WL_CONNECTED){
            WiFiClient client;
            HTTPClient http;

            String serverPath = serverActuator + "?actuator_id=" + 2;
            http.begin(client, serverPath.c_str());

            int httpResponseCode = http.GET();

            if(httpResponseCode>0){
                //Serial.print("HTTP Response code: ");
                //Serial.print("httpResponseCode");
                String payload = http.getString();
                Serial.println("setting the DEHUMIDIFIER to state: " + payload);
                if(payload == "on"){
                    digitalWrite(DEHUMIPIN, HIGH);
                } else if(payload == "off"){
                    digitalWrite(DEHUMIPIN, LOW);
                }
            } else{
                Serial.print("Error code:");
                Serial.println("httpResponseCode");
            }
            // Free Resources
            http.end();
            lastDehumiTime = millis();
        }
    }
}

void controlACState(int temperature){
  
    if((millis() - lastACTime) > acDelay){
        if(WiFi.status()==WL_CONNECTED){
            WiFiClient client;
            HTTPClient http;

            String serverPath = serverActuator + "?actuator_id=" + 1;
            http.begin(client, serverPath.c_str());

            int httpResponseCode = http.GET();

            if(httpResponseCode>0){
                //Serial.print("HTTP Response code: ");
                //Serial.print("httpResponseCode");
                String payload = http.getString();
                Serial.println("setting the AC to state: " + payload);
                if(temperature > TEMPERATUREMAX){
                    digitalWrite(ACPIN, LOW);
                }
                else if(temperature < TEMPERATUREMIN){
                    digitalWrite(ACPIN, HIGH);
                }
            } else{
                Serial.print("Error code:");
                Serial.println("httpResponseCode");
            }
            // Free Resources
            http.end();
            lastACTime = millis();
        }
    }
}

void controlDehumidifierState(int humidity){
  
    if((millis() - lastDehumiTime) > dehumiDelay){
        if(WiFi.status()==WL_CONNECTED){
            WiFiClient client;
            HTTPClient http;

            String serverPath = serverActuator + "?actuator_id=" + 2;
            http.begin(client, serverPath.c_str());

            int httpResponseCode = http.GET();

            if(httpResponseCode>0){
                //Serial.print("HTTP Response code: ");
                //Serial.print("httpResponseCode");
                String payload = http.getString();
                Serial.println("setting the DEHUMIDIFIER to state: " + payload);
                if(humidity > HUMIDITYMAX){
                    digitalWrite(DEHUMIPIN, HIGH);
                } else if(humidity < HUMIDITYMIN){
                    digitalWrite(DEHUMIPIN, LOW);
                }
            } else{
                Serial.print("Error code:");
                Serial.println("httpResponseCode");
            }
            // Free Resources
            http.end();
            lastDehumiTime = millis();
        }
    }
}

void setSystemState(){
  
    if((millis() - lastSystemTime) > systemDelay){
        if(WiFi.status()==WL_CONNECTED){
            WiFiClient client;
            HTTPClient http;

            String serverPath = systemStateServer + "?system_id=" + 1;
            http.begin(client, serverPath.c_str());

            int httpResponseCode = http.GET();

            if(httpResponseCode>0){
                //Serial.print("HTTP Response code: ");
                //Serial.print("httpResponseCode");
                String payload = http.getString();
                Serial.println("setting the System Power to : " + payload);
                if(payload == "on"){
                    systemState = HIGH;
                } else if(payload == "off"){
                    systemState = LOW;
                }
            } else{
                Serial.print("Error code:");
                Serial.println("httpResponseCode");
            }
            // Free Resources
            http.end();
            lastSystemTime = millis();
        }
    }
}

void setup() {
    Serial.begin(115200);

    // initialize the pushbutton pin as an input
    pinMode(BUTTONPIN, INPUT);
    // initialize the LED pin as an output
    pinMode(ACPIN, OUTPUT);
    // initialize the SENSOR pin as an output
    pinMode(DHTPIN, INPUT);

    WiFi.begin(ssid, password);
    Serial.println("Connecting");
    while(WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("");
    Serial.print("Connected to WiFi network with IP Address: ");
    Serial.println(WiFi.localIP());

    Serial.println("Timer set to 5 seconds (timerDelay variable).");
}

void loop() {
  
    setSystemState();
        
    if (systemState == HIGH) {
        if ((millis() - lastTime) > timerDelay) {
            if(WiFi.status()== WL_CONNECTED){
                WiFiClient client;
                HTTPClient http;

                // * ----- generate temp values -----

                // Read temps as Celsius by default
                float newT = dht.readTemperature();

                if (isnan(newT)) {
                    Serial.println("Failed to read from DHT sensor!");
                } else {
                    t = newT;
                    Serial.println(t);
                }

                // * ----- generate humidity values -----

                // Read humidity
                float newH = dht.readHumidity();
                if (isnan(newH)) {
                    Serial.println("Failed to read from DHT sensor!");
                } else {
                    h = newH;
                    Serial.println(h);
                }

                controlACState(t);
                controlDehumidifierState(h); // TODO função por fazer para controlar o desumidificador
                
                String serverPathTemp = serverName + "?temperature=" + t;
                http.begin(client, serverPathTemp.c_str());

                int httpResponseCode = http.GET();

                if (httpResponseCode>0) {
                    Serial.print("HTTP Response code: ");
                    Serial.println(httpResponseCode);
                    String payload = http.getString();
                    Serial.println(payload);
                } else {
                    Serial.print("Error code: ");
                    Serial.println(httpResponseCode);
                }
                // Free resources
                http.end();

                String serverPathHum = serverName + "?humidity=" + h;
                http.begin(client, serverPathHum.c_str());

                // Send HTTP GET request
                httpResponseCode = http.GET();

                if (httpResponseCode>0) {
                    Serial.print("HTTP Response code: ");
                    Serial.println(httpResponseCode);
                    String payload = http.getString();
                    Serial.println(payload);
                } else {
                    Serial.print("Error code: ");
                    Serial.println(httpResponseCode);
                }
                // Free resources
                http.end();
            }
            else
                Serial.println("WiFi Disconnected");
            
            lastTime = millis();
        }
    } else {
            setACState();
            setDehumiState();
    }
}
