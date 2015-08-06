<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Installer.webinstaller
 *
 * @copyright   Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Support for the "Install from Web" tab
 *
 * @package     Joomla.Plugin
 * @subpackage  System.webinstaller
 * @since       3.2
 */
class PlgInstallerWebinstaller extends JPlugin
{
	/*
	 * The main url to connect to
	 */
	private $directoryServer = 'extensions.joomla.org';

	/*
	 * The component we call
	 */
	private $directoryComponent = 'com_jed';

	/*
	 * Name of the category view
	 */
	private $directoryCategoryView = 'category';

	/*
	 * Name of the extension view
	 */
	private $directoryExtensionView = 'extension';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.5
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$lang = JFactory::getLanguage();
		$lang->load('plg_installer_webinstaller', JPATH_ADMINISTRATOR);
	}

	/**
	 * get the categories, it saved the category data in the session to speed up things
	 *
	 * @return JHttpResponse
	 */
	private function getCategories ()
	{
		$app = JFactory::getApplication();

		$categories = $app->setUserState('jedcategories', null);

		if (! is_null($categories))
		{
			//return $categories;
		}

		$dirUrl = $this->getDirectoryBaseUrl();
		$dirUrl->setvar('view', $this->directoryCategoryView);
		$dirUrl->setvar('layout', 'list');
		$dirUrl->setvar('format', 'json');
		$dirUrl->setvar('order', 'ordering');
		$dirUrl->setvar('limit', -1);

		$http   = $this->getDirectoryConnection();
		$result = $http->get($dirUrl);

		$categories = json_decode($result->body, true);
		$newCategories = array();

		// Reformat the array for easier access
		foreach ($categories as $category)
		{
			$cat = array();

			if ($category['level']['value'] != 0)
			{
				$cat['level']     = $category['level']['value'];
				$cat['parent_id'] = $category['parent_id']['value'];
				$cat['title']     = $category['title']['value'];
				$cat['id']        = $category['id']['value'];

				$newCategories[$cat['id']] = $cat;

				if ($cat['level'] > 1)
				{
					$children = array();

					if (array_key_exists('children', $newCategories[$cat['parent_id']]))
					{
						$children = $newCategories[$cat['parent_id']]['children'];
					}

					$children[] = $cat['id'];
					$newCategories[$cat['parent_id']]['children'] = $children;
				}
			}
		}

		$app->setUserState('jedcategories', $newCategories);

		return $newCategories;
	}

	/**
	 * Get Extensions from the directory
	 *
	 * @param   integer  $jedcatid  jed core category
	 *
	 * @return JHttpResponse
	 */
	private function getExtensions ($jedcatid=null)
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		$dirUrl = $this->getDirectoryBaseUrl();

		$dirUrl->setvar('view', $this->directoryExtensionView);
		$dirUrl->setvar('controller', 'filter');
		$dirUrl->setvar('filter[approved]', '1');
		$dirUrl->setvar('filter[published]', '1');

		if (! empty($jedcatid))
		{
			$dirUrl->setvar('filter[core_catid]', $jedcatid);
		}
		else
		{
			$dirUrl->setvar('filter[popular]', '1');
		}

		$dirUrl->setvar('order', $this->getOrdering());
		$dirUrl->setvar('dir', 'ASC');
		$dirUrl->setvar('extend', '1');
		$dirUrl->setvar('limit', $this->getLimit());

		$limitstart = $input->getInt('limitstart', 0);

		$dirUrl->setvar('limitstart', $limitstart);

		$http   = $this->getDirectoryConnection();
		$result = json_decode($http->get($dirUrl)->body, true);

		return $result;
	}

	/**
	 * get Extensions from the directory
	 *
	 * @return JHttpResponse
	 */
	private function getExtension ()
	{
		$id = JFactory::getApplication()->input->get('id', 0);

		if ($id == 0)
		{
			return '{}';
		}

		$dirUrl = $this->getDirectoryBaseUrl();
		$dirUrl->setvar('view', $this->directoryExtensionView);
		$dirUrl->setvar('controller', 'filter');
		$dirUrl->setvar('filter[approved]', '1');
		$dirUrl->setvar('filter[published]', '1');
		$dirUrl->setvar('extend', '0');
		$dirUrl->setvar('filter[id]', $id);

		$http   = $this->getDirectoryConnection();
		$result = json_decode($http->get($dirUrl)->body, true);
		$result = $result['data'][0];

		return $result;
	}

	/**
	 * get Extensions from the directory
	 *
	 * @param   integer  $jedcatid  jed core category
	 *
	 * @return JHttpResponse
	 */
	private function searchExtensions($jedcatid)
	{
		$filter_search = JFactory::getApplication()->input->getString('filter_search', '');

		if ($filter_search == '')
		{
			return '{}';
		}

		$filter_search = implode('+', explode(' ', $filter_search));
		$dirUrl = $this->getDirectoryBaseUrl();
		$dirUrl->setvar('view', $this->directoryExtensionView);
		$dirUrl->setvar('controller', 'filter');
		$dirUrl->setvar('filter[approved]', '1');
		$dirUrl->setvar('filter[published]', '1');

		if (! empty($jedcatid))
		{
			$dirUrl->setvar('filter[core_catid]', $jedcatid);
		}

		$dirUrl->setvar('extend', '0');
		$dirUrl->setvar('searchall', $filter_search);

		$http   = $this->getDirectoryConnection();
		$result = json_decode($http->get($dirUrl)->body, true);

		return $result;
	}

	/**
	 * get the http connection object to the directory
	 *
	 * @return JHttp
	 */
	private function getDirectoryConnection()
	{
		$http = JHttpFactory::getHttp();
		$http->setOption('timeout', $this->getTimeout());

		return $http;
	}

	/**
	 * get the base connection URL
	 *
	 * @return JUri
	 */
	private function getDirectoryBaseUrl()
	{
		$dirUrl = new JUri;

		// Get http(s) from configuration or plugin or request url
		$dirUrl->setScheme('http');
		$dirUrl->setHost($this->directoryServer . '/index.php');
		$dirUrl->setvar('option', $this->directoryComponent);
		$dirUrl->setvar('format', 'json');

		return $dirUrl;
	}

	/**
	 * get the timeout
	 *
	 * @return int
	 */
	private function getTimeout()
	{
		return (int) $this->params->get('timeout', 60);
	}

	/**
	 * get the limit per request
	 *
	 * @return int
	 */
	private function getLimit()
	{
		return (int) $this->params->get('limit_per_request', 36);
	}

	/**
	 * Get the ordering from the request
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 */
	private function getOrdering()
	{
		$input = JFactory::getApplication()->input;

		return $input->get('ordering', 'score');
	}

	/**
	 * Do we show the installer button for install from web
	 *
	 * @param   boolean  &$showJedAndWebInstaller  show the installer?
	 *
	 * @return  void
	 */
	public function onInstallerBeforeDisplay(&$showJedAndWebInstaller)
	{
		$showJedAndWebInstaller = false;
	}

	/**
	 * runs before the first tab at the install screens will get rendered
	 *
	 * @return string
	 */
	public function onInstallerViewBeforeFirstTab()
	{
		if ($this->params->get('tab_position', 0) == 0)
		{
			echo $this->execute();
		}
	}

	/**
	 * run after the last tab at the install screens got rendered
	 *
	 * @return string
	 */
	public function onInstallerViewAfterLastTab()
	{
		if ($this->params->get('tab_position', 0) != 0)
		{
			echo $this->execute();
		}
	}

	/**
	 * prepare a breadcrumb date structure
	 *
	 * @param   integer  $categoryId  the category we need the breadcrumb for
	 *
	 * @return array
	 */
	private function prepareBreadcrumb($categoryId = null)
	{
		$items = array();

		$uri  = JUri::getInstance();
		$link = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));

		$linkbase = $link . '?option=com_installer&view=install';

		// Default view popular extensions
		$items[] = array('title' => 'PLG_INSTALLER_WEBINSTALLER_DEFAULTCATEGORYNAME', 'link' => $linkbase);

		if (empty($categoryId))
		{
			return $items;
		}

		// Default view popular extensions, but we name it different
		$items   = array();
		$items[] = array('title' => 'PLG_INSTALLER_WEBINSTALLER_DEFAULTCATEGORYHOME', 'link' => $linkbase);

		$categories = $this->getCategories();

		$path = array();
		$path[] = $this->parseCategory($path, $categories, $categoryId, $linkbase);

		return array_merge($items, $path);
	}

	/**
	 * recursive function to build up a breadcrumb navigation structure
	 *
	 * @param   array    &$path       the path
	 * @param   array    $categories  all categories
	 * @param   integer  $categoryId  the category id
	 * @param   string   $linkbase    the base link
	 *
	 * @return mixed
	 */
	private function parseCategory(&$path, $categories, $categoryId, $linkbase)
	{
		if (array_key_exists($categories[$categoryId]['parent_id'], $categories))
		{
			$path[] = $this->parseCategory($path, $categories, $categories[$categoryId]['parent_id'], $linkbase);
		}

		$categories[$categoryId]['link'] = $linkbase . '&jedcatid=' . $categoryId;

		return $categories[$categoryId];
	}

	/**
	 * prepares the download link for an extension,
	 * does check if it is a file and extract data from an xml when needed
	 *
	 * @param   array  $data  the jed data
	 *
	 * @return array
	 */
	private function prepareDownloadInformation($data)
	{
		jimport('joomla.updater.update');

		$result = array();

		// We set to false to be save and set it to true if we sure it will work
		$result['installable'] = false;

		// Check if we have a download integration link
		$download_integration_url = '';

		if (array_key_exists('download_integration_url', $data))
		{
			$download_integration_url = $data['download_integration_url']['value'];
		}

		if (! array_key_exists('type', $data))
		{
			return false;
		}

		$type = $data['type']['value'];

		$result['installable'] = false;

		if (array_key_exists('download_link', $data))
		{
			$result['link'] = $data['download_link']['value'];
		}

		if (! empty($download_integration_url))
		{
			$result['link'] = $download_integration_url;

			// Has it a download integration url and is type == free it has to be installable
			$result['installable'] = $type == 'free';

			if (preg_match('/\.xml\s*$/', $download_integration_url))
			{
				$update = new JUpdate;
				$update->loadFromXML($download_integration_url);
				$package_url_node = $update->get('downloadurl', false);

				if (isset($package_url_node->_data))
				{
					$result['link']        = $package_url_node->_data;
				}
			}
		}

		$result['type'] = $type;

		return $result;
	}

	/**
	 * execute and do whatever has to be done
	 *
	 * @return string
	 */
	private function execute()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		$data['categories'] = $this->getCategories();

		$data['extensionlayout'] = $input->get('extensionlayout', '');

		$data['baseUrl']       = 'http://' . $this->directoryServer;
		$data['reviewBaseUrl'] = $data['baseUrl'] . '/index.php?option=' . $this->directoryComponent
											. '&view=extension&layout=default&id=';

		$data['detailBaseUrl'] = 'index.php?option=com_installer&view=install&layout=detail';

		$data['filter_search'] = '';
		$data['ordering']      = $this->getOrdering();

		// Get the view
		$layout = $input->get('layout', 'category');

		// Get the jed category id if set
		$jedcatid = $input->getInt('jedcatid');

		$data['layout']     = $layout;
		$data['jedcatid']   = $jedcatid;
		$data['breadcrumb'] = $this->prepareBreadcrumb($jedcatid);

		// When we are in detail layout we are showing ONE extensions details
		if ($layout == 'detail')
		{
			$id = $input->getInt('id');

			$data['extension']  = $this->getExtension($id, $jedcatid);
			$data['download']   = $this->prepareDownloadInformation($data['extension']);

			$data['backlink']   = 'index.php?option=com_installer&view=install&layout=category&jedcatid=' . $jedcatid;

			return $this->renderTab($data);
		}

		// Search layout, if quite the same as a category layout just after a search
		if ($layout == 'searchresult')
		{
			$data['filter_search'] = JFactory::getApplication()->input->getString('filter_search', '');
			$data['extensions']    = $this->searchExtensions($jedcatid);
			$data['detailBaseUrl'] = $data['detailBaseUrl'] . '&jedcatid=' . $jedcatid;
			$data['pagination']    = $this->getPagination($data['extensions']);
			$data['breadcrumb']    = $this->prepareBreadcrumb($jedcatid);

			return $this->renderTab($data);
		}

		// In category layout it is ONE Category we are showing
		if ($layout == 'category' && ! empty($jedcatid))
		{
			$data['extensions'] = $this->getExtensions($jedcatid);
			$data['detailBaseUrl'] = $data['detailBaseUrl'] . '&jedcatid=' . $jedcatid;
			$data['pagination'] = $this->getPagination($data['extensions']);
			$data['breadcrumb'] = $this->prepareBreadcrumb($jedcatid);

			return $this->renderTab($data);
		}

		$data['extensions'] = $this->getExtensions($jedcatid);
		$data['pagination'] = $this->getPagination($data['extensions']);
		$data['breadcrumb'] = $this->prepareBreadcrumb($jedcatid);

		return $this->renderTab($data);
	}

	/**
	 * get a pagination object if pagination data available
	 *
	 * @param   string  $data  the data
	 *
	 * @return bool|JPagination
	 */
	private function getPagination($data)
	{
		if (! array_key_exists('pagination', $data))
		{
			return false;
		}

		$paginationData = $data['pagination'];
		$pagination = new JPagination($paginationData['total'], $paginationData['limitstart'], $paginationData['limit']);

		return $pagination;
	}

	/**
	 * render the data
	 *
	 * @param   object  $data  data to render as json
	 *
	 * @return string
	 */
	private function renderTab($data)
	{
		return JLayoutHelper::render('joomla.installer.web.tab', $data);
	}
}
