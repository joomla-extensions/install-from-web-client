<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Installer.webinstaller
 *
 * @copyright   Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

// Stub the JInstallerScript class for older versions to perform the minimum required checks
if (!class_exists('JInstallerScript'))
{
	/**
	 * Base install script for use by extensions providing helper methods for common behaviours.
	 *
	 * @since  3.6
	 */
	class JInstallerScript
	{
		/**
		 * Minimum PHP version required to install the extension
		 *
		 * @var    string
		 * @since  3.6
		 */
		protected $minimumPhp;

		/**
		 * Minimum Joomla! version required to install the extension
		 *
		 * @var    string
		 * @since  3.6
		 */
		protected $minimumJoomla;

		/**
		 * Function called before extension installation/update/removal procedure commences
		 *
		 * @param   string             $type    The type of change (install, update or discover_install, not uninstall)
		 * @param   JInstallerAdapter  $parent  The class calling this method
		 *
		 * @return  boolean  True on success
		 *
		 * @since   3.6
		 */
		public function preflight($type, $parent)
		{
			// Check for the minimum PHP version before continuing
			if (!empty($this->minimumPhp) && version_compare(PHP_VERSION, $this->minimumPhp, '<'))
			{
				JLog::add(JText::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp), JLog::WARNING, 'jerror');

				return false;
			}

			// Check for the minimum Joomla version before continuing
			if (!empty($this->minimumJoomla) && version_compare(JVERSION, $this->minimumJoomla, '<'))
			{
				JLog::add(JText::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla), JLog::WARNING, 'jerror');

				return false;
			}

			// Theoretically we should not reach this line in this stub because triggering it means we aren't matching the minimum Joomla version
			return true;
		}
	}
}
/**
 * Support for the "Install from Web" tab
 *
 * @since  1.0
 */
class plginstallerwebinstallerInstallerScript extends JInstallerScript
{
	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $minimumPhp = JOOMLA_MINIMUM_PHP;

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $minimumJoomla = '3.9';

	/**
	 * Function called before extension installation/update/removal procedure commences
	 *
	 * @param   string             $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   JInstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.6
	 */
	public function preflight($type, $parent)
	{
		if (parent::preflight($type, $parent))
		{
			// Disallow installs on 4.0 as the plugin is part of core
			if (version_compare(JVERSION, '4.0', '>='))
			{
				// TODO - Add language string to CMS
				JLog::add(JText::_('PLG_INSTALLER_WEBINSTALLER_ERROR_PLUGIN_INCLUDED_IN_CORE'), JLog::WARNING, 'jerror');

				return false;
			}
		}

		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences
	 *
	 * @param   string            $route    The action being performed
	 * @param   JInstallerPlugin  $adapter  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function postflight($route, $adapter)
	{
		// When initially installing the plugin, enable it as well
		if ($route === 'install')
		{
			try
			{
				$db = JFactory::getDbo();
				$db->setQuery(
					$db->getQuery(true)
						->update($db->quoteName('#__extensions'))
						->set($db->quoteName('enabled') . ' = 1')
						->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
						->where($db->quoteName('element') . ' = ' . $db->quote('webinstaller'))
				)->execute();
			}
			catch (RuntimeException $e)
			{
				// Don't let this fatal out the install process, proceed as normal from here
			}
		}
	}
}
