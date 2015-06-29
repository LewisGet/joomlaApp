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
defined( '_JEXEC' ) or die( 'Restricted access' );
if ($this->ship_address) {
	$shipAddr = @$this->ship_address;
}
$j2params = $this->params;

?>

<div id="j2store_shipping_section" >
<h3><?php echo JText::_('J2STORE_SHIPPING_ADDRESS'); ?></h3>
	<ul class="form-list">
		<li id="shipping-new-address-form">
			<fieldset>
				
				<ul>
					<li class="fields">

						<div class="customer-name">
							<div class="field name-firstname">
								<label class="input-label" for="first_name"><em>*</em><?php echo JText::_('J2STORE_FIRST_NAME'); ?></label>
								<div class="input-box">
									<input type="text" class="input-text"
										title="First Name" value="<?php echo $shipAddr->first_name; ?>" name="shipping[first_name]"
										id="shipping:firstname">
								</div>
							</div>
							
							<?php if($j2params->get('ship_lname') != 3):?>
							<div class="field name-lastname">
								<label class="input-label" for="shipping:lastname"><em>*</em><?php echo JText::_('J2STORE_LAST_NAME')?></label>
								<div class="input-box">
									<input type="text" class="input-text"
										title="Last Name" value="<?php echo $shipAddr->last_name; ?>" name="shipping[last_name]"
										id="shipping:lastname">
								</div>
							</div>
							<?php endif; // ship last name?>
						</div>


					</li>

					<li class="wide"><label class="input-label" for="shipping:address_1"><em>*</em><?php echo JText::_('J2STORE_ADDRESS'); ?></label>
						<div class="input-box">
							<input type="text" class="input-text"
								title="Street Address" value="<?php echo $shipAddr->address_1; ?>" id="shipping:address_1"
								name="shipping[address_1]">
						</div>
					</li>
					
					<?php if($j2params->get('ship_addr_line2') != 3):?>
					<li class="wide">
						<div class="input-box">
							<input type="text" class="input-text1" value="<?php echo $shipAddr->address_2; ?>"
								id="shipping:address_2" name="shipping[address_2]"
								title="Street Address 2">
						</div>

					</li>
					<?php endif; // ship address line 2?>


					<?php if(($j2params->get('ship_zip') != 3)||($j2params->get('ship_city') != 3)):?>
					<li class="fields">
					<?php if($j2params->get('ship_zip') != 3):?>
						<div class="field">

							<label class="input-label" for="shipping:zip"><em>*</em><?php echo JText::_('J2STORE_POSTCODE');?>
								</label>
							<div class="input-box">
								<input type="text"
									class="input-text"
									title="Zip/Postal Code" value="<?php echo $shipAddr->zip; ?>" id="shipping:zip"
									name="shipping[zip]">
							</div>

						</div>
						<?php endif; // end zip ?>
						<?php if($j2params->get('ship_city') != 3):?>

						<div class="field">

							<label class="input-label" for="shipping:city"><em>*</em><?php echo JText::_('J2STORE_CITY'); ?></label>
							<div class="input-box">
								<input type="text" class="input-text"
									title="City" value="<?php echo $shipAddr->city; ?>" id="shipping:city" name="shipping[city]">
							</div>

						</div>
						<?php endif; // end city ?>
					</li>
					<?php endif; // end both city and zip?>
					
					<?php if($j2params->get('ship_country_zone') != 3):?>
					<li class="fields">

						<div class="field">

							<label class="input-label required" for="shipping:country"><em>*</em><?php echo JText::_('J2STORE_COUNTRY'); ?></label>
							<div class="input-box">
							<?php echo $this->ship_country; ?>
							</div>

						</div>

						<div class="field">

							<label class="" for="shipping:state"><?php echo JText::_('J2STORE_STATE_PROVINCE'); ?></label>
							<div class="input-box">
									<span id="shipZoneList"> </span>
							</div>

						</div>
					</li>
					<?php endif; // end country , zone ?>
					
					<?php if(($j2params->get('ship_phone1') != 3)||($j2params->get('ship_phone2') != 3)):?>
					
					<li class="fields">
					
					<?php if($j2params->get('ship_phone1') != 3):?>

						<div class="field">

							<label class="input-label" for="shipping:phone_1"><em>*</em><?php echo JText::_('J2STORE_TELEPHONE')?></label>
							<div class="input-box">
								<input type="text" class="input-text"
									title="Telephone" value="<?php echo $shipAddr->phone_1; ?>" id="shipping:phone_1"
									name="shipping[phone_1]">
							</div>

						</div>
						<?php endif; // end phone1 ?> 
						<?php if($j2params->get('ship_phone2') != 3):?>
						<div class="field">

							<label class="input-label" for="shipping:phone_2"><em>*</em><?php echo JText::_('J2STORE_MOBILE')?></label>
							<div class="input-box">
								<input type="text" class="input-text"
									title="Mobile" value="<?php echo $shipAddr->phone_2; ?>" id="shipping:phone_2"
									name="shipping[phone_2]">
							</div>

						</div>
						<?php endif; // end phone1 ?>

					</li>
					<?php endif; // end mobile , phone1 phone2?>
				</ul>
			</fieldset>
		</li>
	</ul>
	
			<input type="hidden" name="ship_id"	value="<?php echo @$shipAddr->id; ?>" /> 
				 
</div>