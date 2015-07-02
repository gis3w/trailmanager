<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cci18n extends Controller{
    
    public function action_strings(){
        
    $i18n = array(
       // Cookie Consent
        
        
        
        
        "necessaryDefaultTitle" => "Strettamente necessari",
        "defaultTitle" => "Sessione",
        "necessaryDefaultDescription" => "Alcuni cookies sono strettamente necessari e non possono essere disabilitati, sono necessari per ricordarsi chi sei e fornirti una buona esperienza di navigazione.",
        "notificationTitleImplicit" => "Stiamo utilizzando i cookies per assicurarti un buona esperienza di navigazione",
        "hideDetails" => "Chiudi i dettagli",
        "seeDetailsImplicit" => "cambia le tue impostazioni",
        'savePreference' => "Salva le preferenze",
        'saveForAllSites' => "Salva per tutti i siti",
        'allowCookiesImplicit' => 'Chiudi',
        'allowForAllSites' => 'Permetti per tutti i siti',
        "customCookie" => 'Questo sito richiede un speficifico cookie che richiede approvazione',
        "privacySettings" => "Impostazioni privacy",
        "privacySettingsDialogTitleA" => "Impostazioni privacy",
        "privacySettingsDialogTitleB" => "per questo sito",
        "privacySettingsDialogSubtitle" => "Alcune elementi di questo sito necessitano del tuo consenso per ricordarsi chi sei.",
        
     );   
    
        $this->response->body("$.extend(cc.strings,".json_encode($i18n).");");
        $this->response->headers('Content-Type','application/json');
    }
    
    
            
}