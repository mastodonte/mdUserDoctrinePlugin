<?php

/**
 * PluginmdUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class PluginmdUser extends BasemdUser
{

    public function __toString() {
        return $this->getEmail();
    }


    public function getObjectClass()
    {
      return get_class($this);
    }
	
    public function delete(Doctrine_Connection $conn = null) {

        sfContext::getInstance()->getLogger()->info("Preparando para borrar el usuario");
        return parent::delete($conn);
    }

    public function postDelete($event)
    {
        if(sfConfig::get('sf_driver_cache'))
        {
            Doctrine::getTable('mdUser')->removeCacheByKey('_md_user_'.$this->getId());
            Doctrine::getTable('mdUser')->removeCacheByKey('_list_md_users_ids');
        }
    }
    
    public function preDelete($event){
        sfContext::getInstance()->getLogger()->info("Preparando para borrar los pasaportes del usuario");
        foreach($this->getMdPassport() as $mdPassport){
            $mdPassport->delete();
        }
        sfContext::getInstance()->getLogger()->info("Preparando para borrar los mdContents del usuario");
        foreach($this->getMdContent() as $mdContent){
            /*
            $aux = $mdContent->retrieveObject();
            if($aux && !is_null($aux))
            {
              $aux->delete();
            }*/
            $mdContent->delete();
            /*if($mdContent->getObjectClass() !== "mdMediaContent")
            {
                $aux = $mdContent->retrieveObject();
                if($aux && !is_null($aux))
                {
                  $aux->delete();
                }
                $mdContent->delete();
            }*/
            
        }
        if(class_exists("mdWall"))
        {
          mdWall::deleteUserNews($this->getId());
        }
        if(class_exists("mdNewsfeedHandler"))
        {
          mdNewsfeedHandler::deleteNewsFeedsOfMdUser($this->getId());
        }        
        if(class_exists("mdCommentsHandler"))
        {
          mdCommentsHandler::deleteAllMdUserComments($this->getId());
        }         
        
    }
		
    public static function retrieveMdUsers($page = 1, $limit = 20)
    {
        $mdUsersIds = Doctrine::getTable('mdUser')->retrieveMdUsersReference();

        $contentIds = array();
        $array_contents = array_chunk($mdUsersIds, $limit);

        if(array_key_exists(($page-1), $array_contents))
        {
            $contentIds = $array_contents[$page-1];
        }
        else
        {
            return $contentIds;
        }

        $list = array();
        foreach($contentIds as $arrIds){
            $list[] = Doctrine::getTable('mdUser')->retrieveMdUserById($arrIds[0]);
        }
        return $list;
    }
		
    public function retrieveMdPassport(){
        return Doctrine::getTable('mdPassport')->retrieveMdPassportByUserId($this->getId());
    }
		
    /**
     * 	Salva el objeto mdUSer.
     * Genera el objeto sfGuardUser padre con los datos del mdUser
     * Si el email que se esta queriendo ingresar ya existe, hidrata esta instancia con el mdUser ya existente.
     *
     * @return void
     * @author maui .-
     * */
    public function save(Doctrine_Connection $conn = null) {
        if(sfConfig::get('sf_driver_cache'))
        {
                Doctrine::getTable('mdUser')->removeCacheByKey('_md_user_'.$this->getId());
                Doctrine::getTable('mdUser')->removeCacheByKey('_list_md_users_ids');
        }
				
				// agrego cultura al usuario (la de la seccion, la por defecto ó es)
				if(!$this->getCulture() != null){
					if(sfContext::hasInstance()){
						$culture = sfContext::getInstance()->getUser()->getCulture();
					}else{
						$culture = sfConfig::get('sf_default_culture','es');
					}
					$this->setCulture($culture);
				}
				
        return parent::save($conn);
    }

    public function postSave($event)
    {
        mdUserSearchHandler::saveMdUser($this);
    }
    
    public function retrieveAllMdUserContents() {
        throw new Exception('Old logic for profile', 102);
        $helper = new mdUserContent();
        $list = Doctrine::getTable('mdContent')->retrieveByMdUserClassName($this->getId(), get_class($helper));
        $returnList = array();
        foreach ($list as $mdContent) {
            array_push($returnList, $mdContent->retrieveObject());
        }
        return $returnList;
    }

    public function getMdUserProfile() {
        return Doctrine::getTable('mdUserProfile')->findByMdUserId($this->getId());
    }

    /**
     * Valida que un email sea de un solo usuario si esta desactivado las aplicaciones multiples.
     * @param String $email
     * @return bool | Exception
     * @author Rodrigo Santellan
     */
    public static function validateEmail($email) {
        $multipleAplication = sfConfig::get('app_multiple_active', false);
        if (!$multipleAplication) {
            $mdUser = Doctrine::getTable('mdUser')->findOneby('email', $email);
            if($mdUser){
                throw new Exception("A user exists with that email", 130);
            }
        }
        return true;
    }

    public function getBackendClosedBoxText()
    {
      
      $html = '<div class="md_object_owner">';
      $mdPassport = $this->retrieveMdPassport(); 
      if($mdPassport)
      {
        $html .= '<div>';
        $html .= '<label style="font-weight:bolder">'.$mdPassport->getUsername().'</label>';
        $mdUserProfile = $this->getMdUserProfile();
        if($mdUserProfile)
        {
          $html .= '<span>'.$mdUserProfile->getName().' - '.$mdUserProfile->getLastName().'</span>';
        }
        $html .=' - ';
        if($mdPassport->getAccountActive() == 1)
        {
          $html .= __('mdUserDoctrine_text_isActive');
        }
        else
        {
          $html .= __('mdUserDoctrine_text_notIsActive');
        }
        if($mdPassport->getAccountBlocked() == 1)
        {
          $html .= __('mdUserDoctrine_text_isBlocked');
        }
        $html .= '</div>';
      }
      $html .= '<div>'.$this->getEmail().'</div>';
      $html .= '</div>';
      if(!$mdPassport)
      {
        $html .= '<div class="md_object_categories"><span>-</span>'.__('mdUserDoctrine_text_Solo de mailing').'</div>';
      }
      return $html;
       
    }

}
