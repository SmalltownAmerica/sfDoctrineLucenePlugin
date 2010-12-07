<dl id="search-controls">
<?php include_component('search','controls',array('form'=>$form)) ?>
</dl>

<?php if($results && count($results)): ?>

  <ol id="search-results">
  <?php foreach($results as $result): ?>
    <li><?php
      if($p = $result->getPartialName()):
        include_partial($p,array($result->getPartialVar()=>$result));
      else:
        printf("%s: %s (%u%%)", __($result->getSearchName()), $result, $result->getScore()*100);
      endif
    ?></li>
  <?php endforeach ?>
  </ol>

<?php else: ?>  

  <p><?php echo __('Sorry! Couldn\'t find anything that matched your search.') ?></p>

<?php endif ?>