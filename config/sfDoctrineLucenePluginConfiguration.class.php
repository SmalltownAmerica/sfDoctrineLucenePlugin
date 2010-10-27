<?php
class sfDoctrineLucenePluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    set_include_path(sfConfig::get('sf_plugins_dir').'/sfDoctrineLucenePlugin/lib/vendor'.PATH_SEPARATOR.get_include_path());

    if(!class_exists('Zend_Loader_Autoloader'))
    {
      require sfConfig::get('sf_plugins_dir').'/sfDoctrineLucenePlugin/lib/vendor/Zend/Loader/Autoloader.php';
    }

    Zend_Loader_Autoloader::getInstance();
  }
}
