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
    
    return sfView::SUCCESS;
  }
} // END class 