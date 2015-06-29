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

$url = JRoute::_( "index.php?option=com_j2store&view=mycart" );
$return_url = JRoute::_( "index.php?option=com_j2store&view=checkout&task=begin" );
$guest_url = JRoute::_( "index.php?option=com_j2store&view=checkout&&task=begin");
$register_action_url = JRoute::_( "index.php?option=com_j2store&view=checkout&task=register" );
?>
<table id="j2store_checkout_table">
	<tr>
		<td class="j2store_login_box" valign="top">
			<fieldset>
				<legend>
						<?php echo JText::_('J2STORE_LOGIN'); ?>
				</legend>
				<!-- LOGIN FORM -->

				<?php if (JPluginHelper::isEnabled('authentication', 'openid')) :
				$lang->load( 'plg_authentication_openid', JPATH_ADMINISTRATOR );
				$langScript =   'var JLanguage = {};'.
						' JLanguage.WHAT_IS_OPENID = \''.JText::_( 'WHAT_IS_OPENID' ).'\';'.
						' JLanguage.LOGIN_WITH_OPENID = \''.JText::_( 'LOGIN_WITH_OPENID' ).'\';'.
						' JLanguage.NORMAL_LOGIN = \''.JText::_( 'NORMAL_LOGIN' ).'\';'.
						' var modlogin = 1;';
				$document = &JFactory::getDocument();
				$document->addScriptDeclaration( $langScript );
				JHTML::_('script', 'openid.js');
        				endif; ?>

				<form
					action="<?php echo JRoute::_( 'index.php?option=com_users&task=user.login', true, $this->params->get('usesecure')); ?>"
					method="post" name="login" id="form-login">

					<label for="username" class="j2storeUserName"><?php echo JText::_('J2STORE_USERNAME'); ?>
					</label>
					<br />
					<input type="text" name="username" class="inputbox"	alt="username" />
					<br />
					<label for="password" class="j2storePassword"><?php echo JText::_('J2STORE_PASSWORD'); ?> </label>
					<br />
					<input type="password" name="password" class="inputbox"	alt="password" />
					<br />
					<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
					<?php echo JText::_('J2STORE_REMEMBER_ME'); ?>
					<span style="float: left;"> <input type="checkbox" name="remember"
						class="inputbox" value="yes" />
					</span>
					<?php endif; ?>
					<div class="clr"></div>
					<input type="submit" name="submit" class="button"
						value="<?php echo JText::_('J2STORE_LOGIN') ?>" />
					<ul class="loginLinks">
						<li><?php // TODO Can we do this in a lightbox or something? Why does the user have to leave? ?>
							<a
							href="<?php echo JRoute::_( 'index.php?option=com_users&view=reset' ); ?>">
								<?php echo JText::_('J2STORE_FORGOT_YOUR_PASSWORD'); ?>
						</a>
						</li>
						<li><?php // TODO Can we do this in a lightbox or something? Why does the user have to leave? ?>
							<a
							href="<?php echo JRoute::_( 'index.php?option=com_users&view=remind' ); ?>">
								<?php echo JText::_('J2STORE_FORGOT_YOUR_USERNAME'); ?>
						</a>
						</li>
					</ul>
					<input type="hidden" name="option" value="com_users" /> <input
						type="hidden" name="task" value="user.login" /> <input
						type="hidden" name="return"
						value="<?php echo base64_encode( $return_url ); ?>" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
			</fieldset>
		</td>
		<td class="j2store_register_box" valign="top">
			<fieldset>
				<legend>
					
						<?php echo JText::_('J2STORE_REGISTER_NEWUSER'); ?>
				</legend>
				<!-- Registration form -->
				<form action="<?php echo $register_action_url;?>" method="post"
					class="adminform form-validate" name="adminForm" id="form-register">
					
					<div class="j2store_register_fields">
						<label for="email"> <?php echo JText::_( 'J2STORE_REGISTER_EMAIL' ); ?>
							*
						</label> <br /> <input name="email" id="email"
							class="required validate-email" type="text" />
					</div>

					<div class="j2store_register_fields">
						<label for="confirm_mail"> <?php echo JText::_( 'J2STORE_CONFIRM_EMAIL' ); ?>*
						</label> <br /> <input name="confirm_mail" id="confirm_mail"
							class="required validate-email" type="text" />
					</div>
					<div class="j2store_register_fields">

						<label for="password"> <?php echo JText::_( 'J2STORE_PASSWORD' ); ?>
							*
						</label> <br /> <input name="password" id="password"
							class="required" type="password" />
					</div>
					<div class="j2store_register_fields">

						<label for="first_name"> <?php echo JText::_( 'J2STORE_CONFIRM_PASSWORD' ); ?>*
						</label> <br /> <input name="password2" id="password2"
							class="required" type="password" /><br />
					</div>
					<div class="j2store_register_fields">

						<input type="submit" name="submit" class="button"
							value="<?php echo JText::_('J2STORE_REGISTER') ?>" />
					</div>
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>

			</fieldset>
		</td>
	</tr>
	<tr>
		<td colspan="2"><?php if ($this->params->get('allow_guest_checkout')) : ?>
			<!--Guest -->
			<div class="j2storeGuests">
			<form action="<?php echo $guest_url;?>" method="post"
					class="guestform form-validate" name="guest" id="form-guest">
				<!-- REGISTRATION -->
					<?php echo JTEXT::_('J2STORE_CHECKOUT_AS_GUEST_DESC'); ?>
					<br />
						
						<label for="guest_mail"><strong> <?php echo JText::_( 'J2STORE_GUEST_EMAIL' ); ?></strong><em>*</em>
						</label> 
						<input name="guest_mail" id="guest_mail" class="required validate-email" type="email" />
						<input type="submit" name="submit" class="button"
							value="<?php echo JText::_('J2STORE_CHECKOUT_AS_GUEST') ?>" />
				</form>
			</div> <?php endif; ?>
		</td>
	</tr>
</table>
