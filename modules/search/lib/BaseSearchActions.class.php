<?php
/**
 * Base actions for the search module, should be extended by searchActions
 * in the module
 *
 * @package default
 * @author Ben Lancaster
 **/
abstract class BaseSearchActions extends sfActions
{
  public function executeIndex()
  {
    $this->forward('search','search');
  }
  
  /**
   * undocumented function
   *
   * @return void
   * @author Ben Lancaster
   **/
  public function executeSearch(sfWebRequest $request)
  {
    $this->form = new sfDoctrineLuceneSearchForm;
    
    if($request->getGetParameter('q',false))
    {
      $this->form->bind(array(
        'q' => $request->getGetParameter('q'),
        't' => $request->getGetParameter('t',null),
      ));
    
      foreach($this->form->getModelFromChoice($this->form->getValue('t')) as $model)
      {
        if($q = Doctrine::getTable($model)->getForLuceneQuery($this->form->getValue('q')))
        {
          $q->execute();
        }
      }
    
    }
  }
} // END class 