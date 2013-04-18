<?php
?>
<style type="text/css">
  label {
    display:block;
    width:150px;
    margin-right:10px;
    background-color:#eee;
    float:left;
    padding:5px;
    font-weight:bolder;
    color: #666;
  }
  select, input, textarea {
  }

  div.input-field {
    clear:both;
    padding:5px;
  }

  div.input-field p {
    clear:both;
    padding:5px 0 0 5px;
  }
</style>
<form action="index.php?option=com_tournament&controller=tournamentracing&task=cancelsave" method="post" name="adminForm" id="adminForm">
<div class="col50">
    <fieldset name="tournament-information">
    <legend><?php echo JText::_( 'Are you sure you want to cancel the tournament "' . $this->tournament->name . '"' ); ?></legend>
    <div class="input-field">
        <label for="description">
          <?php echo JText::_( 'Reason for Cancellation' ); ?>:
        </label>
        <textarea class="text_area" type="textarea" name="admin_cancelled_reason" id="admin_cancelled_reason" <?php echo $this->tournament->cancelled_flag ? 'readOnly="readOnly"' : '' ?>><?php echo $this->escape($this->tournament->cancelled_reason); ?></textarea>
    </div>
  </fieldset>
</div>
<input type="hidden" name="id" value="<?php echo $this->tournament->id; ?>" />
<input type="hidden" name="task" value="" />
</form>