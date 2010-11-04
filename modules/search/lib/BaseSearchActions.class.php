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
    
    $this->results = false;
    
    if($request->getGetParameter('q',false))
    {
      $this->form->bind(array(
        'q' => $request->getGetParameter('q'),
        't' => $request->getGetParameter('t',null),
      ));
      
      
      $this->results = array();
      
      // Loop through the models we should search against
      $models = $this->form->getModelFromChoice($this->form->getValue('t'));
      $this->logMessage(sprintf("Searching against %u model(s): [ %s ]",count($models),implode(", ",$models)));
      foreach($models as $model)
      {
        $this->logMessage("Starting search loop");
        try
        {
          // Fetch the results of the search from the model's table as an array
          // of Zend_Search_Lucene_Search_QueryHit objects
          $model_results = Doctrine::getTable($model)->getLuceneResults($this->form->getValue('q'));
            
          $this->logMessage(sprintf("Got %u results for %s",count($model_results),$model));

          // Got results?
          if(!empty($model_results))
          {
            // We need to keep an association between the model's primary key 
            // and the Lucene score so we can map it to the object for the
            $pks = array();
            
            foreach( $model_results as $hit )
            {
              $pks[$hit->pk] = $hit->score;
            }

            // Fetch the matched objects by their primary key
            $m_results = Doctrine::getTable($model)
                        ->createQuery()
                        ->whereIn('id',array_keys($pks))
                        ->execute();
                    
            // Map the score for each hit to its corresponding Doctrine_Record
            foreach($m_results as $obj)
            {
              $obj->mapValue('score',$pks[$obj->getId()]);
              $this->results[] = $obj;
            }
          }
        }
        catch(Exception $e) {
          $this->logMessage($e->getMessage() . "when searching " .$model,'emerg');
        }
      }
      self::sortByScore($this->results);
    } // end if there's a query
  }
  
  /**
   * usort give an warning "Array was modified by the user comparison function",
   * please check bug http://bugs.php.net/bug.php?id=50688
   * So I've changed usort to array_multisort
   *
   * @param Array $objects Array of Doctrine_Records
   * @param string The Object property to sort against (uses array access)
   * @param const Either SORT_DESC or SORT_ASC
   * @param const SORT_ASC, SORT_DESC, SORT_REGULAR, SORT_NUMERIC, SORT_STRING
   * @return bool TRUE on success or FALSE on failure.
   * @see http://uk2.php.net/array_multisort
   * @see http://bugs.php.net/bug.php?id=50688
   */
  static private function sortByScore(&$objects,$direction = SORT_DESC, $method = SORT_NUMERIC)
  {
    $rs = array();
    foreach($objects as $k => $r)
    {
      $rs[$k] = $r['score'];
    }
    return array_multisort($rs,$direction,$method,$objects);
  }
  
  protected function getCache()
  {
    if(!$this->cache instanceof sfCache)
    {
      $settings = sfConfig::get('app_lucene_cache');
      $this->cache = new $settings['class']($settings['param']);
    }
    return $this->cache;
  }

} // END class 