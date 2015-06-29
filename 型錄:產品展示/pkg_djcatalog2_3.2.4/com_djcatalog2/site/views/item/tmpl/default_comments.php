<?php
/**
 * @version $Id: default_comments.php 111 2013-01-29 06:28:50Z michal $
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

$uri = JFactory::getURI(); 
$lang = JFactory::getLanguage();
$languge_tag = str_replace('-', '_', $lang->getTag());
?>
<div class="djc_comments">
	<h3><?php echo JText::_('COM_DJCATALOG2_COMMENTS'); ?></h3>
	<?php if($this->params->get('comments',0) == '1') { ?>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/<?php echo $languge_tag; ?>/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>				
		<div class="fb-comments" data-href="<?php echo $uri->toString(); ?>" data-num-posts="2" data-width="auto"></div>
	<?php } else if($this->params->get('comments',0) == '2' && $this->params->get('disqus_shortname','') != '') {?>
    	<?php 
    	$devlist = array('localhost', '127.0.0.1');
    	$disqus_shortname = $this->params->get('disqus_shortname','');
    	$disqus_url = $uri->toString();
    	$disqus_identifier = $disqus_shortname.'-djc2-'.$this->item->id;
    	$disqus_developer = (in_array($_SERVER['HTTP_HOST'], $devlist)) ? 1 : 0;
    	?>
    	<div id="disqus_thread"></div>
	    <script type="text/javascript">
	        var disqus_shortname = '<?php echo $disqus_shortname; ?>';
	        var disqus_url = '<?php echo $disqus_url; ?>';
	        var disqus_identifier = '<?php echo $disqus_identifier; ?>';
			var disqus_developer = <?php echo $disqus_developer; ?>;
			
	        /* * * DON'T EDIT BELOW THIS LINE * * */
	        (function() {
	            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
	            dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
	            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	        })();
	    </script>
	    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
	    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
	<?php } ?>				
</div>
