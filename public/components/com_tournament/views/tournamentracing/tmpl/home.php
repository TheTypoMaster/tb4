<?php
defined('_JEXEC') or die('Restricted access');
?>
<div class="col-left">
	<div class="home-left-col">
		<div class="left-menu-head-betting">Today's Racing - <i><strong>Bet Now!</strong></i></div>
        <div class="left-menu">
        	<ul class="menu">
                <li class="active item1"><a href="/betting/racing/galloping"><span>Galloping</span></a></li> 
                <li class="item2"><a href="/betting/racing/harness"><span>Harness</span></a></li> 
                <li class="item3"><a href="/betting/racing/greyhounds"><span>Greyhounds</span></a></li>
			</ul>
		</div>
        <div class="left-menu-head-tournaments">Tournaments - <i><strong>Free &amp; Paid</strong></i></div>
        <div class="left-menu"> 
            <ul class="menu"> 
                <li class="active item1"><a href="/tournament/racing"><span>Racing</span></a></li> 
                <li class="item6"><a href="/tournament/sports/7"><span>Football</span></a></li> 
                <li class="item11"><a href="/tournament/sports/5"><span>Rugby League</span></a></li> 
                <li class="item11"><a href="/tournament/sports/6"><span>Rugby Union</span></a></li> 
            </ul> 
        </div>
        <div class="home-left-ad"><?php echo $this->left_banner?></div>
	</div><!-- close home-left-col -->
	
<?php if($this->banner_count > 1) {?>
<script>
window.addEvent('domready',function(){
	var hs1 = new noobSlide({
		box: $('box1'),
		items: [<?php echo $this->banner_item?>],
		size: 472,
		autoPlay: true,
		interval: 3500,
		replayMode: 'continuous'
	});
	
	$$('#box1 span').addEvents({
		'mouseover':function(){
			hs1.stop();
		},
		'mouseleave':function(){
			if(hs1.replayMode == 'continuous' && hs1.currentIndex == <?php echo $this->banner_count?>) {
				hs1.continuousPlay();
			} else {
				hs1.play(3500,"next",false);	
			}
		}
	});
});	
</script>
<?php }?>
	<div class="home-middle-col">
		<?php echo $this->next_to_jump ?>
		<?php echo $this->upcoming_tournaments ?>

		<div class="home-ads-large">
			<?php echo $this->center_large_banner ?>
		</div><!-- home-ads-large -->
	</div><!-- close home-middle-col -->
</div><!-- close col-left -->

<div class="col-right">
	<?php echo $this->right_col ?>
</div><!-- close col-right -->
<div class="clear">&nbsp;</div>
