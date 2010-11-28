<?php echo $form->renderFormTag(url_for('@search'),array('method'=>'get','id'=>'search-form'))?>
  <?php if(!$simple): ?>
  <p>
    <?php echo $form ?>
    <input type="submit" value="search" />
  </p>
  <?php else: ?>
    <?php echo $form['q']->render() ?>
    <input type="submit" value="search" />
  <?php endif ?>
</form>
