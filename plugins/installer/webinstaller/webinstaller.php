<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Installer.webinstaller
 *
 * @copyright   Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
if (version_compare(JVERSION, '3.0', '<')) {
	$document = JFactory::getDocument();
	$document->addScript(JURI::root() . "plugins/installer/webinstaller/js/jquery.min.js");
	$document->addScript(JURI::root() . "plugins/installer/webinstaller/js/jquery-noconflict.js");
	$document->addScript(JURI::root() . "plugins/installer/webinstaller/js/jquery-migrate.min.js");
}

/**
 * Support for the "Install from Web" tab
 *
 * @package     Joomla.Plugin
 * @subpackage  System.webinstaller
 * @since       3.2
 */
class PlgInstallerWebinstaller extends JPlugin
{
	public $appsBaseUrl = 'http://appscdn.joomla.org/webapps/';	// will be https once CDN is setup for SSL

	private $_hathor = null;
	private $_installfrom = null;
	private $_j25 = null;
	private $_rtl = null;

	public function onInstallerBeforeDisplay(&$showJedAndWebInstaller)
	{
		$showJedAndWebInstaller = false;
	}
	
	public function onInstallerViewBeforeFirstTab()
	{
		$app = JFactory::getApplication();
 
		$lang = JFactory::getLanguage();
		$lang->load('plg_installer_webinstaller', JPATH_ADMINISTRATOR);
		if (!$this->params->get('tab_position', 0)) {
			$this->getChanges();
		}
	}
	
	public function onInstallerViewAfterLastTab()
	{
		if ($this->params->get('tab_position', 0)) {
			$this->getChanges();
		}
		$ishathor = $this->isHathor();
		$installfrom = $this->getInstallFrom();
		$installfromon = $installfrom ? 1 : 0;

		$document = JFactory::getDocument();
		$ver = new JVersion;
		$min = JFactory::getConfig()->get('debug') ? '' : '.min';

		if ($this->isJ25() || $this->isHathor()) {
			$document->addStyleSheet(JURI::root() . 'plugins/installer/webinstaller/css/bootstrap' . $min . '.css');
			$document->addStyleSheet(JURI::root() . 'plugins/installer/webinstaller/css/bootstrap-responsive' . $min . '.css');
		}
		$document->addScript(JURI::root() . 'plugins/installer/webinstaller/js/client' . $min . '.js?jversion=' . JVERSION);
		$document->addStyleSheet(JURI::root() . 'plugins/installer/webinstaller/css/client' . $min . '.css?jversion=' . JVERSION);
		if ($this->isJ25() || $this->isHathor()) {
			$document->addStyleSheet(JURI::root() . 'plugins/installer/webinstaller/css/client-j25' . $min . '.css?jversion=' . JVERSION);
		}

		$installer = new JInstaller();
		$manifest = $installer->isManifest(JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'installer' . DIRECTORY_SEPARATOR . 'webinstaller' . DIRECTORY_SEPARATOR . 'webinstaller.xml');

		$apps_base_url = addslashes($this->appsBaseUrl);
		$apps_installat_url = base64_encode(JURI::current(true) . '?option=com_installer&view=install');
		$apps_installfrom_url = addslashes($installfrom);
		$apps_product = base64_encode($ver->PRODUCT);
		$apps_release = base64_encode($ver->RELEASE);
		$apps_dev_level = base64_encode($ver->DEV_LEVEL);
		$btntxt = JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true);
		$pv = base64_encode($manifest->version);
		$updatestr1 = JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_UPDATE_AVAILABLE', true);
		$obsoletestr = JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_OBSOLETE', true);
		$updatestr2 = JText::_('JLIB_INSTALLER_UPDATE', true);
		$j25 = $this->isJ25();

		$javascript = <<<END
apps_base_url = '$apps_base_url';
apps_installat_url = '$apps_installat_url';
apps_installfrom_url = '$apps_installfrom_url';
apps_product = '$apps_product';
apps_release = '$apps_release';
apps_dev_level = '$apps_dev_level';
apps_is_hathor = $ishathor;
apps_installfromon = $installfromon;
apps_btntxt = '$btntxt';
apps_pv = '$pv';
apps_updateavail1 = '$updatestr1';
apps_updateavail2 = '$updatestr2';
apps_obsolete = '$obsoletestr';
apps_j25 = $j25;

jQuery(document).ready(function() {
	if (apps_j25) {
		var outerDiv = jQuery('#jed-container');
	
		jQuery('<div id="loading"></div>')
			.css("background", "rgba(255, 255, 255, .8) url('../plugins/installer/webinstaller/img/ajax-loader.gif') 50% 15% no-repeat")
			.css("top", outerDiv.position().top - jQuery(window).scrollTop())
			.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
			.css("width", outerDiv.width())
			.css("height", outerDiv.height())
			.css("position", "fixed")
			.css("opacity", "0.80")
			.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
			.css("filter", "alpha(opacity = 80)")
			.css("display", "none")
			.appendTo(outerDiv);
	}

	if (apps_installfromon)
	{
		jQuery('#myTabTabs a[href="#web"]').click();
	}
	var link = jQuery('#myTabTabs a[href="#web"]').get(0);
	var eventpoint = jQuery(link).closest('li');
	if (apps_is_hathor)
	{
		jQuery('#mywebinstaller').show();
		link = jQuery('#mywebinstaller a');
		eventpoint = link;
	}

	jQuery(eventpoint).click(function (event){
		if (!Joomla.apps.loaded) {
			if (apps_j25 || apps_is_hathor) {
				jQuery('#uploadform-web').parent().parent().css("width", "100%");
			}
			Joomla.apps.initialize();
		}
	});
	
	if (apps_installfrom_url != '') {
		var tag = 'li';
		if (apps_is_hathor)
		{
			tag = 'a';
		}
		jQuery(link).closest(tag).click();
	}

	if (!apps_is_hathor)
	{
		if(typeof jQuery('#myTabTabs a[href="'+localStorage.getItem('tab-href')+'"]').prop('tagName') == 'undefined' ||
			localStorage.getItem('tab-href') == null ||
			localStorage.getItem('tab-href') == 'undefined' ||
			!localStorage.getItem('tab-href')) {
			window.localStorage.setItem('tab-href', jQuery('#myTabTabs a').get(0).href.replace(/^.+?#/, '#'));
		}
	
		if (apps_installfrom_url == '' && localStorage.getItem('tab-href') == '#web')
		{
			jQuery('#myTabTabs li').each(function(index, value){
				value.removeClass('active');
			});
			jQuery(eventpoint).addClass('active');
			window.localStorage.setItem('tab-href', jQuery(eventpoint).children('a').attr('href'));
			if (jQuery(eventpoint).children('a').attr('href') == '#web')
			{
				jQuery(eventpoint).click();
			}
		}
	}
});

		
END;
		$document->addScriptDeclaration($javascript);
	}
	
	private function isJ25() {
		if (is_null($this->_j25)) {
			$this->_j25 = version_compare(JVERSION, '3.0', '<') ? 1 : 0;
		}
		return $this->_j25;
	}
	
	private function isHathor()
	{
		if (is_null($this->_hathor))
		{
			$app = JFactory::getApplication();
			$templateName = strtolower($app->getTemplate());
			if ($templateName == 'hathor')
			{
				$this->_hathor = 1;
			}
			else
			{
				$this->_hathor = $this->isJ25() ? 1 : 0;
			}
		}
		return $this->_hathor;
	}

	private function isRTL() {
		if (is_null($this->_rtl)) {
			$document = JFactory::getDocument();
			$this->_rtl = strtolower($document->direction) == 'rtl' ? 1 : 0;
		}
		return $this->_rtl;
	}
	
	private function getInstallFrom()
	{
		if (is_null($this->_installfrom))
		{
			$app = JFactory::getApplication();
			$installfrom = base64_decode($app->input->get('installfrom', '', 'base64'));
	
			if ($this->isJ25()) {
				JFormHelper::loadRuleClass('url');
			}
			$field = new SimpleXMLElement('<field></field>');
			$rule = new JFormRuleUrl;
			if ($rule->test($field, $installfrom) && preg_match('/\.xml\s*$/', $installfrom)) {
				jimport('joomla.updater.update');
				$update = new JUpdate;
				$update->loadFromXML($installfrom);
				$package_url = trim($update->get('downloadurl', false)->_data);
				if ($package_url) {
					$installfrom = $package_url;
				}
			}
			$this->_installfrom = $installfrom;
		}
		return $this->_installfrom;
	}
	
	private function getChanges()
	{
		$ishathor = $this->isHathor() ? 1 : 0;
		$installfrom = $this->getInstallFrom();
		$installfromon = $installfrom ? 1 : 0;
		$dir = '';
		if ($this->isRTL()) {
			$dir = ' dir="ltr"';
		}

		if ($ishathor)
		{
			if (!$this->isJ25()) {
				JHtml::_('jquery.framework');
				echo '<div class="clr"></div>';
			}
?>
			<fieldset class="uploadform">
				<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_WEB', true); ?></legend>
				<div id="jed-container"<?php echo $dir; ?>>
					<div id="mywebinstaller" style="display:none">
						<a href="#"><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_LOAD_APPS'); ?></a>
					</div>
					<div class="well" id="web-loader" style="display:none">
						<h2><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING'); ?></h2>
					</div>
					<div class="alert alert-error" id="web-loader-error" style="display:none">
						<a class="close" data-dismiss="alert">×</a><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING_ERROR'); ?>
					</div>
				</div>
				<fieldset class="uploadform" id="uploadform-web" style="display:none"<?php echo $dir; ?>>
					<div class="control-group">
						<strong><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM'); ?></strong><br />
						<span id="uploadform-web-name-label"><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_NAME'); ?>:</span> <span id="uploadform-web-name"></span><br />
						<?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_URL'); ?>: <span id="uploadform-web-url"></span>
					</div>
					<div class="form-actions">
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton<?php echo $installfrom != '' ? 4 : 5; ?>()" />
						<input type="button" class="btn btn-secondary" value="<?php echo JText::_('JCANCEL'); ?>" onclick="Joomla.installfromwebcancel()" />
					</div>
				</fieldset>
			</fieldset>
<?php
			if ($this->isJ25()) {
				echo '<div class="clr"></div>';
			}
		}
		else
		{
			echo JHtml::_('bootstrap.addTab', 'myTab', 'web', JText::_('COM_INSTALLER_INSTALL_FROM_WEB', true));
?>
				<div id="jed-container" class="tab-pane">
					<div class="well" id="web-loader">
						<h2><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING'); ?></h2>
					</div>
					<div class="alert alert-error" id="web-loader-error" style="display:none">
						<a class="close" data-dismiss="alert">×</a><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_LOADING_ERROR'); ?>
					</div>
				</div>
	
				<fieldset class="uploadform" id="uploadform-web" style="display:none"<?php echo $dir; ?>>
					<div class="control-group">
						<strong><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM'); ?></strong><br />
						<span id="uploadform-web-name-label"><?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_NAME'); ?>:</span> <span id="uploadform-web-name"></span><br />
						<?php echo JText::_('COM_INSTALLER_WEBINSTALLER_INSTALL_WEB_CONFIRM_URL'); ?>: <span id="uploadform-web-url"></span>
					</div>
					<div class="form-actions">
						<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton<?php echo $installfrom != '' ? 4 : 5; ?>()" />
						<input type="button" class="btn btn-secondary" value="<?php echo JText::_('JCANCEL'); ?>" onclick="Joomla.installfromwebcancel()" />
					</div>
				</fieldset>

<?php
			echo JHtml::_('bootstrap.endTab');
		}

	}
}
