<?php
global $post;

$short_name = sp_team_short_name($post->ID); //$post->post_title;
?>

<style>
  .team-content-header-wrapper .content-header {
    font-size: 2em;
    margin-bottom: 2em;
  }
  .team-content-header-wrapper .content-header-below {
    margin-bottom: 1em;
  }
</style>
<div class="team-content-header-wrapper">
  <div class="team-single-header-prefix"><?php echo _e('Team', 'sportspress') ?></div>
  <h3 class="content-header"><?php echo $short_name?></h3>
  <h5 class="content-header-below"><?php echo __('Herzlich Willkommen!', 'astra-child') ?></h5>
</div>