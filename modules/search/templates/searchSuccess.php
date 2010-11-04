<?php include_component('search','controls',array('form'=>$form)) ?>

<?php if($results): ?>
  
  <?php if(empty($results)): ?>
    <p><?php
      echo __("Sorry, I couldn't find anything that matched your criteria")
    ?></p>
  <?php else: ?>
    <ol id="search-results">
    <?php foreach($results as $result): ?>
      <?php if($p = $result->getPartialName()): ?>
        <li><?php include_partial($p,array($result->getPartialVar()=>$result)) ?></li>
      <?php else: ?>
      <li><?php
      printf("%s: %s (%f)", __($result->getSearchName()), $result, $result->getScore());
      ?></li>
      <?php endif ?>
      
    <?php endforeach ?>
    </ol>
  <?php endif ?>
  
<?php endif ?>