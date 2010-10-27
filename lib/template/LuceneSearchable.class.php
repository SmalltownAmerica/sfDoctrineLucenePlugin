<?php
class LuceneSearchable extends Doctrine_Template
{
  public function setTableDefinition()
  {
    $this->addListener(new LuceneSearchableListener());
  }
  
  public function getLuceneIndexTableProxy()
  {
    if (file_exists($index =  $this->getInvoker()->getTable()->getLuceneIndexFile()))
    {
      return Zend_Search_Lucene::open($index);
    }

    return Zend_Search_Lucene::create($index);
  }

  public function getLuceneIndexFileTableProxy()
  {
    $obj = $this->getInvoker();
    
    return sprintf('%s/lucene/%s.idx',
      sfConfig::get('sf_data_dir'),
      get_class($obj)
    );
  }
  
  /** 
   * Returns a string that should be used on the user-facing side of things
   * when you offer users the opportunity to restrict searches by model. For
   * example, you wouldn't want 'sfGuardUserProfile' showing on the frontend
   * so you may specify a friendly name in schema.yml for a given model
   *
   * @return string
   **/
  public function getSearchNameTableProxy()
  {
    return $this->getOption('search_name',get_class($this->getInvoker()));
  }

  /**
   * Return the plural of the search name as defined by search_name_plural,
   * or the auto-generated plural of search_name
   *
   * @todo I18n!
   * @return string
   **/
  public function getSearchNamePluralTableProxy()
  {
    $obj = $this->getInvoker();
    
    if($p = $this->getOption('search_name_plural',false))
    {
      return $p;
    }
    else
    {
      $singular = $obj->getTable()->getSearchName();
      return preg_match('/s$/',$singular) ? $singular : sprintf("%ss",$singular);
    }
  }

  /** 
   * Test whether this object should be searchable
   *
   * @return boolean
   **/
  public function isIndexable()
  {
    $obj = $this->getInvoker();
    if($method = $this->getOption('validator',false))
    {
      // return $obj->$method();
    }
    return $this->getOption('searchable',true);
  }
  
  public function deleteFromLuceneIndex()
  {
    $obj = $this->getInvoker();
    $index = $obj->getTable()->getLuceneIndex();
    
    // remove existing entries
    foreach ($index->find('pk:'.$obj->getId()) as $hit)
    {
      $index->delete($hit->id);
    }
  }
  
  public function updateLuceneIndex()
  {
    $obj = $this->getInvoker();
    $obj->deleteFromLuceneIndex();

    $index = $obj->getTable()->getLuceneIndex();

    // Don't index something that shouldn't be searchable
    if(!$obj->isIndexable()) return;
    
    $doc = new Zend_Search_Lucene_Document();

    // Store object's primary key
    $doc->addField(Zend_Search_Lucene_Field::Keyword('pk', $obj->getPrimaryKey()));
    
    foreach($this->getOption('fields',array()) as $field)
    {
      $doc->addField(Zend_Search_Lucene_Field::UnStored($field, $obj[$field], $this->getOption('charset','utf-8')));
    }

    // add job to the index
    $index->addDocument($doc);
    $index->commit();
  }

  public function getForLuceneQueryTableProxy($query)
  {
    $obj    = $this->getInvoker();
    $table  = $obj->getTable();
    $hits   = $obj->getTable()->getLuceneIndex()->find($query);

    $pks = array();
    foreach ($hits as $hit)
    {
      $pks[] = $hit->pk;
    }

    if (empty($pks))
    {
      return false;
    }

    return $table->createQuery()
            ->whereIn('id', $pks);
  }  
  /**
   * We use to test externally if this model is searchable, needs to be wrapped
   * in an a try/catch case
   *
   * @return boolean
   **/
  public function isSearchable()
  {
    return true;
  }
  
  public function isSearchableTableProxy()
  {
    return true;
  }
}