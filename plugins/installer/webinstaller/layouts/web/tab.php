<?php

$document = JFactory::getDocument();
$baseUri  = JURI::root() . 'media/plg_installer_webinstaller';

if (JFactory::getConfig()->get('debug'))
{
    $document->addScript($baseUri . '/js/client.js?jversion=' . time());
    $document->addStyleSheet($baseUri . '/css/client.css?jversion=' . time());
}
else
{
    $document->addScript($baseUri . '/js/client.min.js?jversion=' . JVERSION);
    $document->addStyleSheet($baseUri . '/css/client.min.css?jversion=' . JVERSION);
}

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('searchtools.main');

echo JHtml::_('bootstrap.addTab', 'myTab', 'web', JText::_('COM_INSTALLER_INSTALL_FROM_WEB', true));

?>
    <div id="jed-container" class="tab-pane">

        <?php echo JLayoutHelper::render('joomla.installer.web.search', $displayData); ?>

        <?php echo JLayoutHelper::render('joomla.installer.web.breadcrumb', $displayData); ?>

        <?php if ($displayData['layout'] == 'detail') : ?>
            <?php echo JLayoutHelper::render('joomla.installer.web.extension', $displayData); ?>
        <?php else: ?>
            <?php echo JLayoutHelper::render('joomla.installer.web.extensions', $displayData); ?>
        <?php endif; ?>
    </div>
    <input name="layout" type="hidden" value="<?php echo $displayData['layout'];?>"/>
    <input name="reload" type="hidden" value="1"/>
<?php
echo JHtml::_('bootstrap.endTab');
