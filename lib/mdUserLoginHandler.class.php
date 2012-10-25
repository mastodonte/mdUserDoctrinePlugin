<?php

class mdUserLoginHandler {
  
  public static function checkRequieredProfiles($mdPassport, $appName)
  {
  	if($appName == 'backend'){
  		if($mdPassport->getMdUser()->getSuperAdmin() == 1){
  			return true;
  		}
  	}
    return false;
  }
}
