<?php

class lucenerebuildTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      new sfCommandOption('model', null, sfCommandOption::PARAMETER_REQUIRED, 'The model to rebuild',false),
    ));

    $this->namespace        = 'doctrine';
    $this->name             = 'lucene-rebuild';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [lucene-rebuild|INFO] task does things.
Call it with:

  [php symfony lucene-rebuild|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $limit = 0;
    $offset = 0;
    
    if($options['model'] === false)
    {
      $models     = array();
      $model_path = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'model';
      $all_models = Doctrine_Core::loadModels($model_path, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

      foreach($all_models as $model)
      {
        if(!strstr($model, 'Base') && !strstr($model, 'Table'))
        {
          try
          {
            if(Doctrine::getTable($model)->isSearchable())
            {
              $this->indexModel($model);
            }
          }
          catch(Doctrine_Table_Exception $e){ }
        }
      }
    }
    else
    {
      $this->indexModel($options['model']);
    }
  }
  
  protected function indexModel($model)
  {
    $objects = Doctrine::getTable($model)
      ->createQuery()
      ->execute();
    
    foreach($objects as $object)
    {
      $object->updateLuceneIndex();
      $this->logSection("Lucene",sprintf("Saving index for %s with primary key %u",get_class($object),$object->getPrimaryKey()));
      $object->free(true);
    }
    
  }
}
