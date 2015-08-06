<?php
$bc = $displayData['breadcrumb'];
$bccount = count($bc);
?>
<div class="breadcrumbarea">
	<?php if ($bccount > 0) : ?>
		<ul class="breadcrumb">
			<?php if ($bccount == 1) : ?>
				<li class="active"><?php echo JText::sprintf($bc[0]['title'], $bc[0]['link']); ?></li>
			<?php else : ?>
				<?php foreach ($bc as $key => $value) : ?>
					<li>
						<?php echo $key == 0 ? '' : ' / '; ?>
						<?php if ($key == $bccount - 1) : ?>
							<?php echo $value['title'] ?>
						<?php else : ?>
							<a href="<?php echo $value['link'] ?>"><?php echo JText::_($value['title']); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</div>
