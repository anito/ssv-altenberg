<div class="search-form-wrapper" style="">
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <label>
            <span class="screen-reader-text"><?php _ex('Suche nach:', 'label', 'mesmerize'); ?></span>
            <input type="search" class="search-field" placeholder="<?php esc_attr_e('Search &hellip;', 'mesmerize'); ?>" value="<?php echo get_search_query(); ?>" name="s" tabindex="0"/>
        </label>
    </form>
</div>
