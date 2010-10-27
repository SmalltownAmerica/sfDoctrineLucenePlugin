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
      new sfCommandOption('model', null, sfCommandOption::PARAMETER_REQUIRED, 'The model to rebuild'),
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
    
    $objects = Doctrine::getTable($options['model'])
      ->createQuery()
      ->execute();
      
    foreach($objects as $object)
    {
      $object->updateLuceneIndex();
      $this->logSection("Lucene",sprintf("Saving index for %s with primary key %u",get_class($object),$object->getPrimaryKey()));
    }
  }
}
