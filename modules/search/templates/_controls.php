<?php echo $form->renderFormTag(url_for('@search'),array('method'=>'get','id'=>'search-form'))?>
  <p>
    <?php echo $form ?>
    <input type="submit" value="search" />
  </p>
</form>
