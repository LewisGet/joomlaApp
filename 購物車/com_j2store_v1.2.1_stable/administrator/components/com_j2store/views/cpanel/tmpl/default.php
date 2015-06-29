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

?>

<div id="cpanel" class="j2storeAdminCpanel">
  	
	<table class="adminTable" cellpadding="0" cellspacing="0" width="100%">
	<?php if($this->params->get('show_quicktips', 1)):?>
		<tr>
		<td colspan="2" valign="top" width="100%">	<?php echo $this->loadTemplate('info'); ?></td>
		</tr>
	<?php endif; ?>
		<tr>
			<td valign="top" width="50%"><?php echo $this->loadTemplate('quickicons'); ?> </td>
			<td valign="top" width="50%"><?php echo $this->loadTemplate('update'); ?> </td>
		</tr>
	  </table>		
	
	<div class="clr"></div>
	
</div>
<div class="clr"></div>

