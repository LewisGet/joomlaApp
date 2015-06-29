<?php
/**
 * @version $Id: extrafields_legacy.php 105 2013-01-23 14:05:57Z michal $
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

defined('_JEXEC') or die;

$out = '<ul class="adminformlist">';
foreach ($this->fields as $k=>$v) {
	$input = null;
	switch ($v->type) {
		case 'text': {
			$input = '
				<li>
					<label for="attribute_'.$v->id.'">
					'.$v->name.'
					</label>
					<input size="40" id="attribute_'.$v->id.'" type="text" name="attribute['.$v->id.']" value="'.htmlspecialchars($v->field_value).'" />
				</li>
				';
			break;
		}
		case 'textarea':
		case 'editor':  {
			$input = '
				<li>
					<label for="attribute_'.$v->id.'">
					'.$v->name.'
					</label>
					<textarea rows="3" cols="30" id="attribute_'.$v->id.'" name="attribute['.$v->id.']">'.htmlspecialchars($v->field_value).'</textarea>
					<div class="clr"></div>
				</li>
				';
			break;
		}
		/*case 'html': {
			$editor = JFactory::getEditor('none');
			$editor_content = $editor->display( 'attribute['.$v->id.']', $v->field_value, '100%', '250', '20', '10', false);
			$input = '
				<li>
					<label for="attribute_'.$v->id.'">
					'.$v->name.'
					</label>
					<div class="fltlft">
					'.$editor_content.'
					</div>
				</li>
					';
			break;
		}*/
		case 'select': {
			$options = $v->optionlist;
			$optionList = '<option value="">---</option>';
			foreach ($options as $option) {
				$selected = ($option->id == $v->field_value) ? 'selected="selected"' : '';
				$optionList .= '<option '.$selected.' value="'.$option->id.'">'.htmlspecialchars($option->value).'</option>';
			}
			$input = '
				<li>
					<span class="faux-label">'.$v->name.'</span>
					<select id="attribute_'.$v->id.'" name="attribute['.$v->id.']">'.$optionList.'</select>
				</li>
				';
			break;
		}
		case 'checkbox': {
			$options = $v->optionlist;
			$optionList = null;
			$values = explode('|', $v->field_value);
			$i = 1;
			foreach ($options as $option) {
				$selected = (in_array($option->id, $values)) ? 'checked="checked"' : '';
				$optionList .= '
					<label for="attribute_'.$v->id.'_'.$i.'">'.htmlspecialchars($option->value).'</label>
					<input id="attribute_'.$v->id.'_'.$i++.'" type="checkbox" '.$selected.' name="attribute['.$v->id.'][]" value="'.$option->id.'">';
			}
			$input = '
				<li>
					<span class="faux-label">'.$v->name.'</span>
					<div class="fltlft">
						'.$optionList.'
					</div>
				</li>
			';
			break;
		}
	case 'radio': {
			$options = $v->optionlist;
			$optionList = null;
			$i = 1;
			foreach ($options as $option) {
				$selected = ($option->id == $v->field_value) ? 'checked="checked"' : '';
				$optionList .= '
					<label for="attribute_'.$v->id.''.($i-1).'" for="attribute_'.$v->id.''.$i.'-lbl">'.htmlspecialchars($option->value).'</label>
					<input id="attribute_'.$v->id.''.($i++-1).'" type="radio" '.$selected.' name="attribute['.$v->id.']" value="'.$option->id.'">';
			}
			$input = '
				<li>
					<span class="faux-label">'.$v->name.'</span>
					<fieldset id="attribute_'.$v->id.'" class="required radio">
						'.$optionList.'
					</fieldset>
				</li>
			';
			break;
		}
		default: break;
	}
	
	$out .= $input.'<div style="clear:both; border-bottom:1px dashed #ccc; width: 100%; padding-top: 10px; margin-bottom: 10px;"></div>';
}
$out .= '</ul>';
echo $out;