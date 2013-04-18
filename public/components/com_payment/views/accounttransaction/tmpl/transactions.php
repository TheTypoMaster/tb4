<?php defined('_JEXEC') or die('Restricted access'); ?>

<div id="bettaWrap" class="min-height-360">
	<div class="moduletable">
		<h3>My Account</h3>
		<div class="hdrBar">
			<div id="hdrBar_trans"></div>
			<span class="transaction_title">Account Transactions</span>
			<div id="date_select"><span class="date_select_txt">DATE: </span><?php echo $this->current_date; ?></div>
		</div>
		<form action="<?php echo JURI::current(); ?>" method="post" name="filter_transaction_form">
			<table width="100%">
				<tr>
				<?php foreach ($this->nav_list as $nav => $label) :?>
					<td>
					<?php if ($nav == $this->current_nav) :?>
						<?php echo $label; ?>
					<?php else : ?>
						<a href="/user/account/transactions/<?php echo $nav == 'all' ? '' : 'type/' . $nav; ?>"><?php echo $label; ?>
					<?php endif; ?>
					</td>
				<?php endforeach ?>
					<td>
						Dates from:
					</td>
					<td>
						<input type="text" value="<?php echo $this->lists['from_date'];?>" class="DatePicker" name="filter_transaction_from_date" id="from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
					</td>
					<td>
						to:
					</td>
					<td>
						<input type="text" value="<?php echo $this->lists['to_date'];?>" class="DatePicker" name="filter_transaction_to_date" id="to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
					</td>
					<td>
						<button onclick="this.form.submit();">
							<?php echo JText::_('Search'); ?>
						</button>
						<button onclick="
							document.filter_transaction_form.filter_transaction_from_date.value='';
							document.filter_transaction_form.filter_transaction_to_date.value='';
							this.form.submit();">
							<?php echo JText::_('Reset'); ?>
						</button>
					</td>
				</tr>
			</table>
		</form>

		<?php if ($this->transaction_display_list) : ?>
		<table id="receipt" border="1" class="mytrans" width="100%">
			<tr>
    			<th>TRANS. No.</th>
				<th>TIME</th>
				<th>TRANSACTION DESCRIPTION</th>
				<th>VALUE (AUD $)</th>
				<th>TYPE</th>
			</tr>

			<?php foreach ($this->transaction_display_list as $transaction_id => $transaction) : ?>
			<tr>
    			<td><?php echo $this->escape($transaction_id); ?></td>
    			<td><?php echo $this->escape($transaction['time']); ?></td>
				<?php if( $transaction['link'] ) : ?>
				<td><a href="<?php echo $this->escape($transaction['link']); ?>"><?php echo $this->escape($transaction['description']); ?></a></td>
				<?php else : ?>
				<td><?php echo $this->escape($transaction['description']) ?></td>
				<?php endif; ?>
				<td>
      				<div class="<?php echo $transaction['amount_class']; ?>"><?php echo $transaction['amount']; ?></div>
				</td>
				<td><?php echo $this->escape($transaction['type']); ?></td>
			</tr>
			<?php endforeach; ?>
		</table>

		<?php if ($this->current_nav == 'all') :?>
			<?php echo preg_replace('/\/index.php\?option=com_payment&amp;c=account&amp;layout=transactions&amp;Itemid=6(&amp;limitstart=){0,1}/s', '/user/account/transactions/', $this->page->getPagesLinks()); ?>
		<?php else : ?>
			<?php echo preg_replace('/\/index.php\?option=com_payment&amp;c=account&amp;layout=transactions&amp;transaction_type=' . $this->current_nav . '&amp;Itemid=6(&amp;limitstart=){0,1}/s', '/user/account/transactions/type/' . $this->escape($this->current_nav) . '/', $this->page->getPagesLinks()); ?>
		<?php endif; ?>
		<br /><br />
		<?php else : ?>
		<p>There are no transactions to list.</p>
		<?php endif; ?>
    </div>
</div>
