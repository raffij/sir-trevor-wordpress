<div class="sir-trevor-block sir-trevor-block--video">
	<div class="sir-trevor-block--video__container embed-container">
		<?php if ($block['source'] == "vimeo") { ?>
			<iframe class="sir-trevor-block--video__container-player" src="//player.vimeo.com/video/<?php echo $block['remote_id']; ?>?title=1&amp;byline=1&amp;portrait=1&amp;autoplay=0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		<?php } else if ($block['source'] == "youtube") { ?>
			<iframe class="sir-trevor-block--video__container-player" src="//www.youtube.com/embed/<?php echo $block['remote_id']; ?>" frameborder="0" allowfullscreen></iframe>
		<?php } ?>
	</div>
</div>