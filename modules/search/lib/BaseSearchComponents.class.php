<?php
/**
 * Base component methods for the Search module.
 *
 * @package default
 * @author Ben Lancaster
 **/
abstract class BaseSearchComponents extends sfComponents
{
  public function executeControls(sfWebRequest $request)
  {
    if(!isset($this->form) && !$this->form instanceof sfDoctrineLuceneSearchForm)
    {
      $this->form = new sfDoctrineLuceneSearchForm;
    }
    
    if(!isset($this->simple))
    {
      $this->simple = false;
    }
    
    return sfView::SUCCESS;
  }
} // END class 