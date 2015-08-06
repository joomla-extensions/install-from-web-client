<?php

// Helper function (sure there is a way to do this more elegant, just make a PR)
if ( ! function_exists('installFromWebRenderChildren'))
{
    function installFromWebRenderChildren($tree, $children, $level, $jedcatid)
    {
        $result = '';
        foreach ($children as $key)
        {
            $value = $tree[$key];

			$result .= '<option value="' . $key . '"';
            $result .= $key == $jedcatid ? ' selected="selected"' : '';
            $result .= '>';
            $result .= str_repeat ('- ' , $level) . $value['title'];
            $result .= '</option>';

            if (isset($value['children']))
            {
                $result .= installFromWebRenderChildren($tree, $value['children'], $level+1, $jedcatid);
            }
        }

        return $result;
    }
}

$tree  = $displayData['categories'];
$level = 1;

?>
<select name="jedcatid" onchange="Joomla.webinstall.update('category');">
    <option value=""<?php echo '' == $displayData['jedcatid'] ? ' selected="selected"' : '';?>><?php echo JText::_('PLG_INSTALLER_WEBINSTALLER_BASE'); ?></option>

    <?php foreach ($tree as $key => $value) : ?>
		<?php if ($value['level'] == 1) : ?>
			<option value="<?php echo $key; ?>"<?php echo $key == $displayData['jedcatid'] ? ' selected="selected"' : '';?>><?php echo $value['title']; ?></option>

			<?php if (isset($value['children'])) : ?>
				<?php echo installFromWebRenderChildren($tree, $value['children'], $level+1, $displayData['jedcatid']); ?>
			<?php endif; ?>
		<?php endif; ?>
    <?php endforeach; ?>
</select>
