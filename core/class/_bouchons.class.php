<?php
  
class bouchons {
     /* @brief Fonction qui permet de synchroniser
     *        
     * @return false en cas d'erreur le nombre de modules synchroniser       
     */
//class Synchro
    public static function getBouchon( $fonction , $data = null , $format = 'json' ) {
      
      log::add('saurclient', 'debug',  __METHOD__.'(ln '.__LINE__.')'.': $fonction='.$fonction.' - $data='.$data );
      
      switch ($fonction){
        case 'login':
            return outils::LireJSON( 'bouchons/login.json' , $format ) ;
            break;
        case 'get_Meter_indexes_last':
            return outils::LireJSON( 'bouchons/get_Meter_indexes_last_'.$data.'.json' , $format ) ;
            break;
        default:
            log::add('saurclient', 'error',  __METHOD__.'(ln '.__LINE__.') fonction inconnu ('.$fonction.')' ) ;
            return false ;
		}
    }

  
  
} // Fin class bouchons