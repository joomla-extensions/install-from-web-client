<?php

$item     = $displayData['extension'];
$download = $displayData['download'];

?>
<div class="extension" id="plg-webinstaller-extension-info">

	<?php if ($item !== false) : ?>

		<div class="full-item-container">
			<h2>
				<span><?php echo $item['core_title']['value'];?></span>
				<?php if ($item['popular']['value'] == 1) :?>
					<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_POPULAR'); ?>
				<?php endif; ?>
			</h2>
			<?php if (isset($item['logo']['value'][0])) : ?>
				<div class="pull-left span3">
					<img class="img img-polaroid pull-left" src="<?php echo $item['logo']['value'][0]['path'];?>" />
				</div>
			<?php endif;?>
			<div class="pull-left span9">
				<div class="row-fluid">
					<div class="span6 info">
						<div class="rating">
							<a target="_blank" href="<?php echo $displayData['reviewBaseUrl'] . $item['id']['value'];?>#reviews">
								<?php echo JText::sprintf('PLG_INSTALLER_WEBINSTALLER_EXTENSION_VOTES_REVIEWS_LIST_INLINE', $item['score']['value'], $item['num_reviews']['value']); ?>
							</a>
						</div>
						<dl class="info">
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_VERSION'); ?></dt>
							<dd>
								<?php echo $item['version']['value'];?>
								<?php echo JText::sprintf('PLG_INSTALLER_WEBINSTALLER_LASTUPDATE', date('l, d M Y', strtotime($item['core_modified_time']['value']))); ?>

							</dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_LICENSE'); ?></dt>
							<dd><?php echo $item['license']['text'];?></dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_DOWNLOAD'); ?></dt>
							<dd><?php echo $item['type']['text'];?></dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_ADDED_ON'); ?></dt>
							<dd><?php echo date('l, d M Y', strtotime($item['core_created_time']['value']));?></dd>
						</dl>
						<div class="clearfix"></div>
						<div class="badges">
							<?php if (in_array('com', $item['includes']['value'])) :?>
								<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_COMPONENT'); ?>
							<?php endif; ?>
							<?php if (in_array('mod', $item['includes']['value'])) :?>
								<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_MODULE'); ?>
							<?php endif; ?>
							<?php if (in_array('plugin', $item['includes']['value'])) :?>
								<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_PLUGIN'); ?>
							<?php endif; ?>
							<?php if (in_array('tool', $item['includes']['value'])) :?>
								<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_TOOL'); ?>
							<?php endif; ?>
							<?php if (in_array('esp', $item['includes']['value'])) :?>
								<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSION_SPECIFIC_ADDON'); ?>
							<?php endif; ?>
							<?php if (in_array('lang', $item['includes']['value'])) :?>
								<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_LANGUAGE'); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="span6">
						<h3><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SCORES'); ?></h3>
						<dl class="info">
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SCORES_OVERALL'); ?></dt>
							<dd>
							<?php echo $item['score']['text'];?>
							</dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SCORES_FUNCTIONALITY'); ?></dt>
							<dd><?php echo $item['functionality']['text'];?></dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SCORES_EASY_OF_USE'); ?></dt>
							<dd><?php echo $item['ease_of_use']['text'];?></dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SCORES_DOCUMENTATION'); ?></dt>
							<dd><?php echo $item['documentation']['text'];?></dd>
							<dt><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SCORES_SUPPORT'); ?></dt>
							<dd><?php echo $item['support']['text'];?></dd>
						</dl>
						<div class="clearfix"></div>

					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="bandl well">
				<?php if ($download['installable'] == 1) : ?>
					<button class="btn btn-success" onclick="Joomla.webinstall.showsubmitform();return false;">
						<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_INSTALL'); ?>
					</button>
				<?php else :?>
					<a class="btn btn-success" target="_blank" href="<?php echo $download['link'];?>">
						<?php if ($download['type'] == 'free') : ?>
							<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_INSTALL_WEBSITE_DEV'); ?>
						<?php else :?>
							<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_INSTALL_WEBSITE_PAID'); ?>
						<?php endif;?>
					</a>
				<?php endif;?>
				<a class="btn btn-primary" target="_blank" href="<?php echo $displayData['reviewBaseUrl'] . $item['id']['value'];?>">
					<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_DIRECTORY_LISTING'); ?>
				</a>
				<?php if (trim($item['support_link']['text']) != '') : ?>
					<a class="btn btn-primary" target="_blank" href="<?php echo $item['support_link']['text'];?>">
						<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_SUPPORT'); ?>
					</a>
				<?php endif;?>

				<?php if (trim($item['homepage_link']['text']) != '') : ?>
					<a class="btn btn-primary" target="_blank" href="<?php echo $item['homepage_link']['text'];?>">
						<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_DEVELOPER_WEBSITE'); ?>
					</a>
				<?php endif;?>

				<?php if (trim($item['documentation_link']['text']) != '') : ?>
					<a class="btn btn-primary" target="_blank" href="<?php echo $item['documentation_link']['text'];?>">
						<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_DOCUMENTATION'); ?>
					</a>
				<?php endif;?>

				<?php if (trim($item['demo_link']['text']) != '') : ?>
					<a class="btn btn-primary" target="_blank" href="<?php echo $item['demo_link']['text'];?>">
						<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_DEMO'); ?>
					</a>
				<?php endif;?>
			</div>
			<div class="description">
				<p class="item-title">
					<?php echo $item['core_title']['value'];?> <?php echo JText::sprintf('PLG_INSTALLER_WEBINSTALLER_SW_PRODUCER', $item['core_created_user_id']['text']); ?>
				</p>
				<div>
					<?php echo $item['core_body']['html'];?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php else: ?>
		<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_EXTENSION_NOT_FOUND'); ?>
	<?php endif; ?>
</div>
<fieldset class="uploadform" style="display:none;" id="plg-webinstaller-extension-form">
	<div class="control-group">
		<?php echo JText::sprintf('PLG_INSTALLER_WEBINSTALLER_CONFIRM_INSTALL', $item['core_title']['value'], $item['version']['value'], $download['link']); ?>
	</div>
	<div class="form-actions">
		<button class="btn btn-primary" onclick="Joomla.webinstall.install('<?php echo $download['link'];?>');return false;">
			<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_CONFIRM_DOINSTALL'); ?>
		</button>
		<button class="btn btn-secondary" onclick="Joomla.webinstall.disablesubmitform();return false;">
			<?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_CONFIRM_CANCEL'); ?>
		</button>
	</div>
</fieldset>



