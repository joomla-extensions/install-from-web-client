<?php

// Only 6,4,3,2,1 makes sense
$size       = 6;
$itemClass  = 'span' . (int) 12 / $size;
$data       = $displayData['extensions'];
$extensions = array_chunk($data['data'], $size);

$displayData['itemClass'] = $itemClass;

?>
<div class="extensions">
    <?php foreach ($extensions AS $row) : ?>
        <div class="row-fluid">
            <?php foreach ($row AS $item) : ?>
                <?php $displayData['item'] = $item; ?>
                <?php echo JLayoutHelper::render('joomla.installer.web.item', $displayData); ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
    <?php if ($displayData['pagination'] !== false) : ?>
        <?php echo $displayData['pagination']->getPaginationLinks('joomla.installer.web.pagination.links'); ?>
    <?php endif; ?>
</div>
