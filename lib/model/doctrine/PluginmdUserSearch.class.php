<?php

/**
 * PluginmdUserSearch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class PluginmdUserSearch extends BasemdUserSearch
{
    /*
    public function preSave($event)
    {
        //$this->setFullField($this->getEmail().' '.$this->getUsername().' '.$this->getName().' '.$this->getLastName());
    }
    */
    
    public function retrieveAvatarSrc()
    {
      $src = $this->getAvatarSrc();
      if(!$src)
        $src = "/../mdUserDoctrinePlugin/images/md_user_image.jpg";
      return $src;
    }
}
