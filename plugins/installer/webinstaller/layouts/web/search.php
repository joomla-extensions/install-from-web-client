<?php
	$ordering = array('num_reviews','score','core_title','core_created_time','core_modified_time');
?>

<div id="filter-bar" class="jstools clearfix">
	<?php if ($displayData['layout'] != 'detail') : ?>

		<div class="filter-search pull-left btn-wrapper" style="margin-right: 10px;">
			<?php echo JLayoutHelper::render('joomla.installer.web.categories', $displayData); ?>
		</div>

		<div class="filter-search pull-left btn-wrapper input-append">
			<input name="filter_search" placeholder="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SEARCH_PLACEHOLDER'); ?>"
				   value="<?php echo $displayData['filter_search']; ?>" class="hasTooltip" title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SEARCH_TITLE'); ?>" type="text"
				   onchange="Joomla.webinstall.search();">
			<button type="button" class="btn hasTooltip" title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SEARCH_BUTTONTITLE'); ?>" onclick="Joomla.webinstall.search();"
					data-original-title="Search"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SEARCH_RESETTITLE'); ?>" onclick="Joomla.webinstall.resetsearch();"
					data-original-title="Clear" id="search-reset"><i class="icon-remove"></i></button>
		</div>

		<div class="btn-wrapper pull-right select">
			<select name="ordering" onchange="Joomla.webinstall.resort();">
				<?php foreach ($ordering as $sort) : ?>
					<option value="<?php echo $sort; ?>"<?php echo $sort == $displayData['ordering'] ? ' selected="selected"' : '';?>>
						<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SORT_' . strtoupper($sort)); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php else :?>
			<a class="btn btn-warning" href="<?php echo $displayData['backlink']; ?>"><span class="icon-list"></span><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_BACK_TO_CATEGORY'); ?></a>
	<?php endif; ?>
</div>