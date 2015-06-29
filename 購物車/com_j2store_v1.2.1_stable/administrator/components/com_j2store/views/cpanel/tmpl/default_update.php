<?php
/*------------------------------------------------------------------------
# com_j2store - J2 Store v 1.0
# ------------------------------------------------------------------------
# author    Sasi varna kumar - Weblogicx India http://www.weblogicxindia.com
# copyright Copyright (C) 2012 Weblogicxindia.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://j2store.org
# Technical Support:  Forum - http://j2store.org/forum/index.html
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//$mrl = 'http://www.j2store.org/index.php?option=com_wiupdate&view=checkupdate&format=ajax';
$mrl = JURI::root().'index.php?option=com_wiupdate&view=checkupdate&format=ajax';
$version = $this->row->version;
$doc = &JFactory::getDocument();

?>

<div class="j2store_update">
<table>
	<?php if(!empty($this->wiupdate->version)):?>
	<tr>
		<td><h1>
		<?php echo JText::_('J2STORE_UPDATE_CHECK'); ?> </h1>
		</td>
		<td id="checkUpdate">
			<?php
			if($this->wiupdate->version != $this->row->version ) { ?>
				<span class='updateyes'><?php echo JText::_('J2STORE_NEW_VERSION_AVAILABLE'); ?> : <?php echo $this->wiupdate->version; ?>
				<br/>
				<a href="<?php echo JRoute::_('index.php?option=com_installer&view=update')?>"><?php echo JText::_('J2STORE_GOTO_UPDATE_MANAGER');?></a>
				</span>
			<?php } else { ?>
				<span class='updateno'><?php echo JText::_('J2STORE_IS_UPTODATE'); ?></span>
			<?php } ?>

		 </td>
	</tr>
	<?php endif; ?>

	<tr>
		<td><h1>
		<?php echo JText::_('J2STORE_CURRENT_VERSION'); ?> </h1>
		</td>
		<td id="currentVersion"><?php echo $this->row->version; ?></td>
	</tr>
	
	<tr>
		<td>
			<iframe src="http://j2store.org/updates/j2store_1.2.1_newsfeed.html" scrolling="no" width="500px" height="100px">
			</iframe>
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<h1> J2 Store</h1>
			<p>
			J2Store is a simple shopping cart extension for Joomla 2.5.x
			The extension is created by <a target="_blank" href="http://weblogicxindia.com">Weblogicx India</a>, professional Joomla extension
			developers.
			</p>


			<h2>Key features: </h2>
			<ul>
			<li>Ajax shopping cart </li>
			<li>Product options, tax, shipping, global discount.</li>
			<li>Payment plugins - Paypal, Authorize.Net, SagePay and OGone </li>
			<li>Enhanced order management </li>
			<li>Guest checkout </li>
			<li>Professional Support from developers</li>
			</ul>
			<!--
			<p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_donations">
			<input type="hidden" name="business" value="rameshelamathi@gmail.com">
			<input type="hidden" name="lc" value="US">
			<input type="hidden" name="item_name" value="J2Store">
			<input type="hidden" name="no_note" value="0">
			<input type="hidden" name="currency_code" value="USD">
			<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			</p>
					 -->
			<p>
			<strong> Visit : <a target="_blank" href="http://j2store.org">J2Store.Org</a> to know more. <br />
			Use our <a target="_blank" href="http://j2store.org/support.html"> support forum</a> to post your queries <br />
			</strong>
			</p>


		 </td>
	</tr>


</table>
</div>
