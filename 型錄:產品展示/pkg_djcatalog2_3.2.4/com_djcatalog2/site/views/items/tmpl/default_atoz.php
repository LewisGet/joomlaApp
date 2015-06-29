<?php
/**
 * @version $Id: default_atoz.php 99 2013-01-08 10:39:32Z michal $
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2012 DJ-Extensions.com LTD, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michal Olczyk - michal.olczyk@design-joomla.eu
 *
 * DJ-Catalog2 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DJ-Catalog2 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with DJ-Catalog2. If not, see <http://www.gnu.org/licenses/>.
 *
 */
defined ('_JEXEC') or die('Restricted access');

$juri = JURI::getInstance();
$uri = JURI::getInstance($juri->toString());
$query = $uri->getQuery(true);
unset($query['limitstart']);
unset($query['search']);
unset($query['start']);
unset($query['ind']);

$indexUrl = htmlspecialchars($uri->toString());

if (strpos($indexUrl,'?') === false ) {
    $indexUrl .= '?';
} else {
    $indexUrl .= '&amp;';
}
?>

<?php if (count($this->lists['index']) > 0) { ?>
<div class="djc_atoz_in">
    <ul class="djc_atoz_list djc_clearfix">
            <?php foreach($this->lists['index'] as $letter => $count) { 
            	
            	?>
               <li>
                   <?php 
                       $catslug = '0';
                       if ($this->item) {
                           $catslug = $this->item->catslug;
                       }
                       if ($count > 0) { ?>
                           <a href="<?php echo JRoute::_($indexUrl.'ind='.$letter.'#tlb'); ?>">
                               <span class="btn"><?php echo $letter; ?></span>
                           </a>
                       <?php }
                       else { ?>
                           <span><span class="btn">
                               <?php echo $letter; ?>
                           </span></span>
                       <?php }
                   ?>
               </li>
            <?php } ?>
         </ul>
</div>
<?php } ?>
<?php 
JURI::reset();
?>