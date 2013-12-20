<div class="sir-trevor-block sir-trevor-block--tweet">
  <blockquote class="twitter-tweet tw-align-center">
    <p><?php echo $block['text']; ?></p>
    &mdash; <a href="http://twitter.com/<?php echo $block['user']['screen_name']; ?>">@<?php echo $block['user']['screen_name']; ?></a>
    <a href="<?php echo $block['status_url']; ?>"><?php echo $block['created_at']; ?></a>
  </blockquote>
</div>