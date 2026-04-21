<?php
require 'vendor/autoload.php';
header("Access-Control-Allow-Origin: *");
// Permite los métodos HTTP que deseas aceptar
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// Permite los encabezados que deseas aceptar
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');



use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

// Path al archivo JSON de credenciales descargado desde Google Cloud Console
putenv('GOOGLE_APPLICATION_CREDENTIALS=carbide-ether-429302-b3-80e5f239fa8d.json');

// Función para convertir texto a voz
function textToSpeech($text, $outputFile) {
    // Inicializar el cliente de Text-to-Speech
    $client = new TextToSpeechClient();

    try {
        // Configurar parámetros de voz
        $voice = (new VoiceSelectionParams())
            ->setLanguageCode('es-MX') // Cambiar al código de idioma deseado
            ->setSsmlGender(SsmlVoiceGender::MALE); // Cambiar género si es necesario

        // Configurar tipo de audio y configuración
        $audioConfig = (new AudioConfig())
            ->setAudioEncoding(AudioEncoding::MP3);

        // Configurar el texto a sintetizar
        $synthesisInputText = (new SynthesisInput())
            ->setText($text);

        // Realizar la solicitud de síntesis de voz
        $response = $client->synthesizeSpeech($synthesisInputText, $voice, $audioConfig);

        // Guardar la respuesta en un archivo de audio
        file_put_contents($outputFile, $response->getAudioContent());

        echo 1;
    } finally {
        $client->close();
    }
}

// Ejemplo de uso
$text = $_REQUEST['text'];
$outputFile = "audio.mp3"; // Archivo de salida donde se guardará el audio

// Llamar a la función para convertir texto a voz
textToSpeech($text, $outputFile);
