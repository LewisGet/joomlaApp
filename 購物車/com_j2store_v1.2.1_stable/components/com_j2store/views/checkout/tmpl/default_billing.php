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
if ($this->bill_address) {
	$billAddr = @$this->bill_address;
}
$j2params = $this->params;
/*
 * 1 required
* 2 not required
* 3 disable
* */
?>

<div id="j2store_billing_section">
	<ul class="form-list">
		<li id="billing-new-address-form">
			<fieldset>

				<ul>
					<!-- First Name and last name fields-->

					<li class="fields">
						<div class="customer-name">
							<div class="field name-firstname">
								<label class="required" for="first_name"><em>*</em> <?php echo JText::_('J2STORE_FIRST_NAME'); ?>
								</label>
								<div class="input-box">
									<input type="text" class="input-text required"
										title="First Name"
										value="<?php echo $billAddr->first_name; ?>"
										name="billing[first_name]" id="billing:firstname">
								</div>
							</div>

							<?php if($j2params->get('bill_lname') != 3):?>
							<div class="field name-lastname">
								<label
									class="<?php echo ($j2params->get('bill_lname')==1)?'required':''; ?>"
									for="billing:lastname"><?php echo ($j2params->get('bill_lname')==1)?'<em>*</em>':''; ?> <?php echo JText::_('J2STORE_LAST_NAME')?>
								</label>
								<div class="input-box">

									<input type="text"
										class="input-text <?php echo ($j2params->get('bill_lname')==1)?'required':''; ?>"
										title="Last Name" value="<?php echo $billAddr->last_name; ?>"
										name="billing[last_name]" id="billing:lastname">
								</div>
							</div>
							<?php endif; // bill last name?>
						</div>
					</li>

					<?php if($j2params->get('bill_addr_line1') != 3):?>
					<li class="wide"><label
						class="<?php echo ($j2params->get('bill_addr_line1')==1)?'required':''; ?>"
						for="billing:address_1"><?php echo ($j2params->get('bill_addr_line1')==1)?'<em>*</em>':''; ?> <?php echo JText::_('J2STORE_ADDRESS'); ?>
					</label>
						<div class="input-box">
							<input type="text"
								class="input-text <?php echo ($j2params->get('bill_addr_line1')==1)?'required':''; ?>"
								title="Street Address"
								value="<?php echo $billAddr->address_1; ?>"
								id="billing:address_1" name="billing[address_1]">
						</div>
					</li>
					<?php endif; // bill address line 1?>
					<?php if($j2params->get('bill_addr_line2') != 3):?>
					<li class="wide">
						<div class="input-box">
							<input type="text"
								class="input-text <?php echo ($j2params->get('bill_addr_line2')==1)?'required':''; ?>"
								title="Street Address 2"
								value="<?php echo $billAddr->address_2; ?>"
								id="billing:address_2" name="billing[address_2]">
						</div>
					</li>
					<?php endif; // bill address line 2?>


					<?php if(($j2params->get('bill_zip') != 3)||($j2params->get('bill_city') != 3)):?>
					<li class="fields">
					<?php if($j2params->get('bill_zip') != 3):?>
						<div class="field">
							<label class="<?php echo ($j2params->get('bill_zip')==1)?'required':''; ?>" for="billing:zip">
							<?php echo ($j2params->get('bill_zip')==1)?'<em>*</em>':''; ?> <?php echo JText::_('J2STORE_POSTCODE');?>
								</label>
							<div class="input-box">
								<input type="text" class="input-text <?php echo ($j2params->get('bill_zip')==1)?'required':''; ?>"
									title="Zip/Postal Code" value="<?php echo $billAddr->zip; ?>"
									id="billing:zip" name="billing[zip]">
							</div>
						</div>
						<?php endif; // end zip ?>
						<?php if($j2params->get('bill_city') != 3):?>
						<div class="field">
							<label class="<?php echo ($j2params->get('bill_city')==1)?'required':''; ?>" for="billing:city">
							<?php echo ($j2params->get('bill_city')==1)?'<em>*</em>':''; ?> <?php echo JText::_('J2STORE_CITY'); ?>
							</label>
							<div class="input-box">
								<input type="text" class="input-text <?php echo ($j2params->get('bill_city')==1)?'required':''; ?>" title="City"
									value="<?php echo $billAddr->city; ?>" id="billing:city"
									name="billing[city]">
							</div>
						</div>
						<?php endif; // end city ?>
					</li>
					<?php endif; // end both city and zip?>

					<?php if($j2params->get('bill_country_zone') != 3):?>
					<li class="fields">
						<div class="field">
							<label
							class="required"
							for="billing:country"><em>*</em> <?php echo JText::_('J2STORE_COUNTRY'); ?>
							</label>
							<div class="input-box">
								<?php echo $this->bill_country; ?>
							</div>

						</div>

						<div class="field">

							<label class="" for="billing:state"><?php echo JText::_('J2STORE_STATE_PROVINCE'); ?>
							</label>
							<div class="input-box">

								<span id="billZoneList"> </span>
							</div>

						</div>
					</li>
					<?php endif; // end country , zone ?>

					<?php if(($j2params->get('bill_phone1') != 3)||($j2params->get('bill_phone2') != 3)):?>

					<li class="fields">
						<?php if($j2params->get('bill_phone1') != 3):?>
						<div class="field">
							<label
							class="<?php echo ($j2params->get('bill_phone1')==1)?'required':''; ?>"
							for="billing:phone_1">
							<?php echo ($j2params->get('bill_phone1')==1)?'<em>*</em>':''; ?> <?php echo JText::_('J2STORE_TELEPHONE')?>
							</label>
							<div class="input-box">
								<input type="text"
								class="input-text <?php echo ($j2params->get('bill_phone1')==1)?'required':''; ?>"
								title="Telephone" value="<?php echo $billAddr->phone_1; ?>" id="billing:phone_1"
									name="billing[phone_1]">
							</div>
						</div>
						<?php endif; // end phone1 ?>
						<?php if($j2params->get('bill_phone2') != 3):?>
						<div class="field">
							<label
							class="<?php echo ($j2params->get('bill_phone2')==1)?'required':''; ?>"
							for="billing:phone_2">
							<?php echo ($j2params->get('bill_phone2')==1)?'<em>*</em>':''; ?> <?php echo JText::_('J2STORE_MOBILE')?>
							</label>
							<div class="input-box">
								<input type="text"
								class="input-text <?php echo ($j2params->get('bill_phone2')==1)?'required':''; ?>"
								title="Mobile" value="<?php echo $billAddr->phone_2; ?>" id="billing:phone_2"
									name="billing[phone_2]">
							</div>
						</div>
						<?php endif; // end phone2 ?>
					</li>
					<?php endif; // end mobile , phone1 phone2?>
					<input type="hidden" name="bill_id"
						value="<?php echo @$billAddr->id; ?>" />
				</ul>
			</fieldset>
		</li>
	</ul>

</div>
