<?php
$itemClass = $displayData['itemClass'];
$item      = $displayData['item'];

$commercial = $item['type']['value'] != 'free';
$tags = $item['includes']['value'];
?>

<div class="<?php echo $itemClass; ?>">

    <div class="thumbnail">
        <p class="rating center">
            <a target="_blank" href="<?php echo $displayData['reviewBaseUrl'] . $item['id']['value'];?>#reviews">
                <?php echo JText::sprintf('PLG_INSTALLER_WEBINSTALLER_EXTENSION_VOTES_REVIEWS_LIST', $item['score']['value'], $item['num_reviews']['value']); ?>
            </a>
        </p>
        <div>
			<div class="center item-image">
				<?php if (isset($item['logo']['value'][0]['path'])) : ?>
						<img src="<?php echo $item['logo']['value'][0]['path']; ?>" class="img center" />
				<?php endif; ?>
			</div>
            <ul class="item-type center">
                <?php if ($commercial) : ?>
                    <span title="<?php echo $item['type']['value']; ?>" class="label label-jcommercial">$</span>
                <?php endif; ?>
                <?php if (in_array('com', $tags)) : ?>
                    <span title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSIONSVIEW_COMPONENT'); ?>" class="label label-jcomponent">C</span>
                <?php endif; ?>
                <?php if (in_array('lang', $tags)) : ?>
                    <span title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSIONSVIEW_LANGUAGE'); ?>" class="label label-jlanguage">L</span>
                <?php endif; ?>
                <?php if (in_array('mod', $tags)) : ?>
                    <span title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSIONSVIEW_MODULE'); ?>" class="label label-jmodule">M</span>
                <?php endif; ?>
                <?php if (in_array('plugin', $tags)) : ?>
                    <span title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSIONSVIEW_PLUGIN'); ?>" class="label label-jplugin">P</span>
                <?php endif; ?>
                <?php if (in_array('esp', $tags)) : ?>
                    <span title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSIONSVIEW_EXTENSION_SPECIFIC_ADDON'); ?>" class="label label-jspecial">S</span>
                <?php endif; ?>
                <?php if (in_array('tool', $tags)) : ?>
                    <span title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSIONSVIEW_TOOL'); ?>" class="label label-jtool">T</span>
                <?php endif; ?>
            </ul>
            <h4 class="center muted">
                <a class="" href="<?php echo $displayData['detailBaseUrl'] . '&id=' . $item['id']['value'] ?>"><?php echo trim($item['core_title']['value']); ?></a>
            </h4>
            <div class="item-description">
                <?php echo mb_strlen(trim($item['core_body']['value'])) > 400 ? mb_substr(trim($item['core_body']['value']), 0, mb_stripos(trim($item['core_body']['value']), ' ', 400)) . '...' : trim($item['core_body']['value']); ?>
                <div class="fader">&nbsp;</div>
            </div>
        </div>
    </div>
</div>


