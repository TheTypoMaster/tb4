<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<div class="user-top-acc-info">
					Free Credit: <span class="user-top-amount"><?php echo htmlspecialchars($this->tournament_dollars); ?></span>
				</div>

				<div class="user-top-acc-info">
					Account Balance: <span class="user-top-amount">AUD <?php echo htmlspecialchars($this->account_balance); ?></span>
				</div>