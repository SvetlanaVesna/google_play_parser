<?php foreach ($this->vars->products as $product) { ?>
	<div id="<?= $product->id ?>">
		<div style="float: left">
			<img src="<?= $product->image ?>"/>
		</div>
		<div>
			<a href="<?=$product->url?>"><strong><?= $product->title ?></strong></a><br/>
			<span><?= $product->description ?></span>
		</div>
	</div>
	<div style="clear: both"></div>
<? } ?>
