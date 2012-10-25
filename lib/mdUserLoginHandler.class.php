<?php

class mdUserLoginHandler {
  
  public static function checkRequieredProfiles($mdPassport, $appName)
  {
  	if($appName == 'backend'){
  		if($mdPassport->getMdUser()->isSuperAdmin()){
  			return true;
  		}
  	}
    return false;
  }
}
