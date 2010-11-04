<?php
/**
 * Search form class
 *
 * @package default
 * @author Ben Lancaster
 **/
class sfDoctrineLuceneSearchForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema['q']  = new sfWidgetFormInputText(array(),array(
      'type'      => 'search'
    ));
    
    $choices = $this->getModelChoices();

    // Loop through the available models
    $this->widgetSchema['t'] = new sfWidgetFormChoice(array(
      'choices'   => $choices,
    ));

    $this->widgetSchema->setLabels(array(
      'q' => 'Keywords',
      't' => 'Type',
    ));
    
    $this->validatorSchema['q'] = new sfValidatorPass();
    $this->validatorSchema['t'] = new sfValidatorPass();

    $this->disableLocalCSRFProtection();
  }
  
  /**
   * Create an array of all of the models that are defined as searchable
   *
   * @return void
   * @author Ben Lancaster
   **/
  public function getModelChoices($plural = true, $search_name = true)
  {
    $models     = array();
    $model_path = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'model';
    $all_models = Doctrine_Core::loadModels($model_path, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
    
    $method = $plural ? 'getSearchNamePlural' : 'getSearchName';
    
    foreach($all_models as $model)
    {
      if(!strstr($model, 'Base') && !strstr($model, 'Table'))
      {
        try
        {
          if(Doctrine::getTable($model)->isSearchable())
          {
            if($search_name)
            {
              $choices[] = Doctrine::getTable($model)->$method();
            }
            else
            {
              $choices[] = $model;
            }
          }
        }
        catch(Doctrine_Table_Exception $e)
        { }
      }
    }
    array_unshift($choices,sfContext::getInstance()->getI18n()->__('All'));
    return $choices;
  }
  
  public function getModelFromChoice($choice = 0)
  {
    $choices = $this->getModelChoices(false,false);
    if(is_array($choice))
    {
      $return = array();
      foreach($choice as $v)
      {
        $return[] = $this->getModelFromChoice($choice);
      }
      return $return;
    }
    elseif(intval($choice) === 0)
    {
      array_shift($choices);
      return $choices;
    }
    else
    {
      if(array_key_exists($choice, $choices))
      {
        return array($choices[$choice]);
      }
      else
      {
        throw new InvaldiArgumentException(sprintf("Invalid choice %s '%s'",gettype($choice),(string) $choice));
      }
    }
  }
} // END class 