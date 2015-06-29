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
J2StoreSubmenuHelper::addSubmenu($vName = 'taxprofiles');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">		
<div id="j2store_taxprofile_item" class="col width-60">

<fieldset>
	<legend><?php echo JText::_('J2STORE_TAX_PROFILE_DETAILS'); ?> </legend>
	
	<table class="admintable" width="100%">
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="taxprofile_name">
					<?php echo JText::_( 'J2STORE_TAX_PROFILE_NAME' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="taxprofile_name" id="taxprofile_name" size="32" maxlength="250" value="<?php echo $this->taxprofile->taxprofile_name;?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="tax_percent">
					<?php echo JText::_( 'J2STORE_TAX_PERCENT' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" name="tax_percent" id="tax_percent" size="32" maxlength="11" value="<?php echo $this->taxprofile->tax_percent;?>" />
			</td>
		</tr>
	
		<tr>
			<td valign="top" align="right" class="key">
				<?php echo JText::_( 'J2STORE_TAX_PROFILE_STATE' ); ?>:
			</td>
			<td>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>
	
</fieldset>	
			
</div>
	<input type="hidden" name="option" value="com_j2store" />
	<input type="hidden" name="view" value="taxprofiles" />
	<input type="hidden" name="cid[]" value="<?php echo $this->taxprofile->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<div class="clr"></div>

