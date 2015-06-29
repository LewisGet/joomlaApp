<?php
/**
 * @package Locator Component
 * @copyright 2009 - Fatica Consulting L.L.C.
 * @license GPL - This is Open Source Software 
 * $Id$
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

function let_to_num($v){ //This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
    $l = substr($v, -1);
    $ret = substr($v, 0, -1);
    switch(strtoupper($l)){
    case 'P':
        $ret *= 1024;
    case 'T':
        $ret *= 1024;
    case 'G':
        $ret *= 1024;
    case 'M':
        $ret *= 1024;
    case 'K':
        $ret *= 1024;
        break;
    }
    return $ret;
}

$max_upload_size = min(let_to_num(ini_get('post_max_size')), let_to_num(ini_get('upload_max_filesize')));



?>
<script language="javascript">
function verify(t){
	if(t.checked == true){
		if(confirm('Are you sure?  Choosing this option will remove all entries from your Locator database.')){
		}else{
			t.checked = false;
		}
	}
}
</script>
<hr />

<div class="csv">
<ul>
<li>Your upload must be in CSV format using <a href="index.php?option=com_locator&task=showimportcsv" target="_blank">this template</a></li>
<li><?php echo "Maximum upload file size is ".($max_upload_size/(1024*1024))."MB."; ?></li>
<li>Ensure the first row contains the column names <u>exactly</u> as in the template provided.</li>
<li>The file must be UTF-8 Encoded to support international character sets</li>
</ul>
</div>
		
<hr />
<form name="adminForm" method="POST" ENCTYPE="multipart/form-data" onsubmit="" >

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="directory"/>
<input type="hidden" name="option" value="com_locator"/>
<input type="hidden" name="upload" value="1"/>
<input type="hidden" name="task" value="import_upload"/>
<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid',''); ?>"/>
<label for="userfile"><?php echo JText::_('Import File:'); ?></label><input type="file" name="userfile" id="userfile" />
<br /><input type="checkbox" name="delete" value="1" onchange="verify(this);" />Remove all entries before import?

</form>