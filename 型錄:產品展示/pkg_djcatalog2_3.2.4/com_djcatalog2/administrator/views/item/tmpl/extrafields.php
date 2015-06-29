<?php
/**
 * @version $Id: extrafields.php 105 2013-01-23 14:05:57Z michal $
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

$out = '<div class="adminformlist">';
foreach ($this->fields as $k=>$v) {
	$input = null;
	switch ($v->type) {
		case 'text': {
			$input = '
					<div class="control-label">
						<label for="attribute_'.$v->id.'">
						'.$v->name.'
						</label>
					</div>
					<div class="controls">
						<input size="40" id="attribute_'.$v->id.'" type="text" name="attribute['.$v->id.']" value="'.htmlspecialchars($v->field_value).'" />
					</div>
				';
			break;
		}
		case 'textarea': 
		case 'html': {
			$input = '
					<div class="control-label">
						<label for="attribute_'.$v->id.'">
						'.$v->name.'
						</label>
					</div>
					<div class="controls">
						<textarea rows="3" cols="30" id="attribute_'.$v->id.'" name="attribute['.$v->id.']">'.htmlspecialchars($v->field_value).'</textarea>
					</div>
				';
			break;
		}
		/*case 'html': {
			$editor = JFactory::getEditor();
			$input = '
					<div class="control-label">	
						<label for="attribute_'.$v->id.'">
							'.$v->name.'
						</label>
					</div>
					<div class="controls">
						'.$editor->display( 'attribute['.$v->id.']', $v->field_value, '100%', '250', '0', '0',false).'
					</div>
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
					<div class="control-label">
						<label for="attribute_'.$v->id.'">'.$v->name.'</label>
					</div>
					<div class="controls">
						<select id="attribute_'.$v->id.'" name="attribute['.$v->id.']">'.$optionList.'</select>
					</div>
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
					<input id="attribute_'.$v->id.'-'.$i.'" type="checkbox" '.$selected.' name="attribute['.$v->id.'][]" value="'.$option->id.'">
					<label for="attribute_'.$v->id.'-'.$i.'">'.htmlspecialchars($option->value).'</label>
					';
				$i++;
			}
			$input = '
					<div class="control-label">
						<label>'.$v->name.'</label>
					</div>
					<div class="controls">
						<fieldset id="attribute_'.$v->id.'-" class="checkbox">
							'.$optionList.'
						</fieldset>
					</div>
			';
			break;
		}
	case 'radio': {
			$options = $v->optionlist;
			$optionList = null;
			$i = 0;
			foreach ($options as $option) {
				$selected = ($option->id == $v->field_value) ? 'checked="checked"' : '';
				$optionList .= '
					<input id="attribute_'.$v->id.'-'.($i).'" type="radio" '.$selected.' name="attribute['.$v->id.']" value="'.$option->id.'">
					<label for="attribute_'.$v->id.'-'.$i.'" for="attribute_'.$v->id.''.'-'.'-lbl">'.htmlspecialchars($option->value).'</label>';
				$i++;
			}
			$input = '
					<div class="control-label">
						<label>'.$v->name.'</label>
					</div>
					<div class="controls">
						<fieldset id="attribute_'.$v->id.'-" class="required radio">
							'.$optionList.'
						</fieldset>
					</div>
			';
			break;
		}
		default: break;
	}
	$out .= '<div class="control-group">'.$input.'</div>';
}

$out .= '</div>';
echo $out;
