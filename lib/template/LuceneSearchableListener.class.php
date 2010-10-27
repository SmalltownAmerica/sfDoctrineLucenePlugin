<?php
class LuceneSearchableListener extends Doctrine_Record_Listener
{
  public function postSave(Doctrine_Event $event)
  {
    $event->getObject()->updateLuceneIndex();
  }
  
  public function postDelete(Doctrine_Event $event)
  {
    $event->getObject()->deleteFromLuceneIndex();
  }
}