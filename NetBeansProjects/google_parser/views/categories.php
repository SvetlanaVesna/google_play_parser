
<?php foreach ($this->vars->categories as $category) { ?>
	<a href="<?= $category->url ?>"  onclick="return false;"><?= $category->title ?></a><br/>
<? } ?>
