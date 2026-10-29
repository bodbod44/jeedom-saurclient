<?php
  
class api_saurclient {

    public static $bouchon = false ;
    
    //* Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function getURL(){
        return 'https://apib2c.azure.saurclient.fr' ;
    }
  
    //* Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function Login(){
        log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': ' );
    
        if( self::$bouchon )
            return bouchons::getBouchon( 'login' , '' , 'tab') ;
  
    	$User = config::byKey('email','saurclient','');
    	$Passwd = config::byKey('password','saurclient','');
    
        if( empty( $User ) || empty( $Passwd ) ){
            log::add('saurclient', 'error',  'Vérifiez que vous avez valorisé et sauvegardé vos informations : email + password' );
            return false ;
        }
    
        /// Preparation de la requete : json
        //$data = json_encode( array('email' => $User, 'password' => $Passwd, 'lang' => $Lang) ) ;
        $data = '{"username":"'.$User.'","password":"'.$Passwd.'","client_id":"frontjs-client","grant_type":"password","scope":"api-scope","isRecaptchaV3":true,"captchaToken":true}' ;
    
        log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $data='.$data );

        /// Parametres cUrl
        $params = array(
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array(
                    'Accept : application/json, text/plain, */*',
                    'Content-Type: application/json'
            ),
            CURLOPT_URL => self::getURL().'/admin/v2/auth',
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => $data
        );

        /// Initialisation de la ressources curl
        $SaurClientHandle = curl_init();
        if ($SaurClientHandle === false)
            return false;
             
        /// Configuration des options
        curl_setopt_array($SaurClientHandle, $params);
        
        /// Excute la requete
        $result = curl_exec($SaurClientHandle);

        /// Test le code retour http
        $httpcode = curl_getinfo($SaurClientHandle, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($SaurClientHandle);
    
        log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $params='.var_export( $params , true) );

        if( $httpcode != 200 && $httpcode != 400 ){
            log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode.' - '.$result );
            return false;
        }
        
        ///Décodage de la réponse
        $aRep = json_decode($result, true);
    
        /*if( isset( $aRep['token']['access_token'] ) ){
          	log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': access_token isset OK' );
            config::save('access_token'    , $aRep['token']['access_token' ] , 'saurclient');
            config::save('refresh_token'   , $aRep['token']['refresh_token'] , 'saurclient');
            config::save('defaultSectionId', $aRep['defaultSectionId']       , 'saurclient');
        }
      	else
          log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': access_token not isset' );*/
    
        log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.$result );
        
        return $aRep;
    }
  
    //* Fonction exécutée automatiquement toutes les minutes par Jeedom
    public static function get_Meter_indexes_last( $defaultSectionId , $token , $Recurrence = 0 ){
    
        log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': ' );

        if( self::$bouchon ){
            log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': MODE BOUCHON' );
          log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': '.var_export(bouchons::getBouchon( 'get_Meter_indexes_last' , $defaultSectionId , 'tab' ) , true) );
            return bouchons::getBouchon( 'get_Meter_indexes_last' , $defaultSectionId , 'tab' ) ;
        }
        //else
        //    log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': PAS DE BOUCHON' );
    
        /// Preparation de la requete : json
        //$token = config::byKey('access_token','saurclient','');
        //$defaultSectionId = config::byKey('defaultSectionId','saurclient','');
    
        /*if( empty($token) || empty($defaultSectionId) ){
            if( self::Login() == false)
                return false ;
            $token = config::byKey('access_token','saurclient','');
            //$defaultSectionId = config::byKey('defaultSectionId','saurclient','');
        }*/
    
        if( empty($token) || empty($defaultSectionId) ){
            log::add('saurclient', 'error',  __METHOD__.'(ln '.__LINE__.')'.': token ou SectionId non valorisé ($defaultSectionId='.$defaultSectionId.' - $token='.$token.')' );
            return false;
        }
    
        //$data = json_encode( array('email' => $User, 'Authorization' => $token) ) ;

        /// Parametres cUrl
        $params = array(
            //CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => array(
                    'Accept : application/json, text/plain, */*',
                    'Content-Type: application/json',
              		'Authorization: Bearer '.$token
            ),
            CURLOPT_URL => self::getURL().'/deli/section_subscriptions/'.$defaultSectionId.'/meter_indexes/last',
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10
            //CURLOPT_POSTFIELDS => $data
        );

        /// Initialisation de la ressources curl
        $SaurClientHandle = curl_init();
        if ($SaurClientHandle === false)
            return false;
             
        /// Configuration des options
        curl_setopt_array($SaurClientHandle, $params);
        
        /// Excute la requete
        $result = curl_exec($SaurClientHandle);

        /// Test le code retour http
        $httpcode = curl_getinfo($SaurClientHandle, CURLINFO_HTTP_CODE);

        /// Ferme la connexion
        curl_close($SaurClientHandle);
    
    	log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $params='.var_export( $params , true) );

        if( $httpcode != 200 ){
            log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': erreur http '.$httpcode.' - '.$result );
          
            if( $Recurrence < 1 && $httpcode == 401 ){
                log::add('saurclient', 'debug', __METHOD__.'(ln '.__LINE__.')'.': On retente (Recurrence '.$Recurrence.')');
                self::Login() ;
                
                $aRep = self::get_Meter_indexes_last( $defaultSectionId , $token , $Recurrence + 1) ;
                if( $aRep === false ){
                    log::add('saurclient', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative KO (Recurrence '.$Recurrence.')');
                    log::add('saurclient', 'error', 'Erreur lors de l\'appel Saur Client (erreur '.$httpcode.'). Vérifier vos parametres email, password' );
                    return false;
                }
                log::add('saurclient', 'debug', __METHOD__.'(ln '.__LINE__.')'.':'.$Did.'- Nouvelle tentative OK (Recurrence '.$Recurrence.')');
                $result = json_encode( $aRep );
            }
            else
                return false;          
        }
        
        ///Décodage de la réponse
        //$aRep = json_decode($result, true);
    
        log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.':'.$result );
        
        return json_decode($result, true);  
  }
  
} // Fin class api_saurclient