<?php

if(!is_active_sidebar('mesmerize_pages_sidebar'))
{
    return;
}

?>

<div class="sidebar col-sm-4 col-md-3">
    <div class="sidebar-row">
        <?php dynamic_sidebar('sidebar-1'); ?>
    </div>
</div>