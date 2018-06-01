<?php

function delete_from_sales($my_sales_id) {

    $args = array(
        'include' => $my_sales_id
    );

    $product_category = get_terms('product_cat', $args)[0];

    $my_sales_name = $product_category->name;
    $my_sales_count = $product_category->count;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 10000000,
        'product_cat' => $my_sales_name
    );

    $products = get_posts($args);

    $results = array("html" => [], "products" => [], "errors" => 0);

    $html = "";
    $error_count = 0;
    foreach ($products as $product) {
        $product = wc_get_product($product->ID);

        if ($product->is_on_sale() === FALSE) {

            $html .= "<div class='error sales_error'>" .
                    "<span class='title'>{$product->get_title()}</span>" .
                    "<input type='submit' name='fix_cat_{$error_count}' value='entfernen' class='fixit'>" .
                    "<input type='hidden' name='fix_cat_{$error_count}[product_id]' value='{$product->get_id()}'>" .
                    "<input type='hidden' name='fix_cat_{$error_count}[my_sales_id]' value='{$my_sales_id}'>" .
                    "<a href='" . get_permalink($product->get_id()) . "' target='_blank'>anzeigen</a>" .
                    "<a href='/wp-admin/post.php?post={$product->get_id()}&action=edit' target='_blank'>editieren</a>" .
                    "</div>";
            $error_count++;
            $results["products"] = $product;
            $results["html"] = $html;
        }
        $results["errors"] = $error_count;
    }
    return $results;
}

function add_to_sales($my_sales_id) {

    $ids = wc_get_product_ids_on_sale();

    $error_count = 0;

    $results = array("html" => [], "products" => [], "all_product_ids" => [], "errors" => 0);

    $id_list = [];
    $html = "";
    foreach ($ids as $id) {
        $product = wc_get_product($id);
        $product_id = $id;

        if ($product->is_type('variation')) {
            $variation = new WC_Product_Variation($product);
            $product_id = $variation->get_parent_id();
            $product = wc_get_product($product_id);
        }

        $product_title = $product->get_title();
        if (!in_array($product_id, $id_list)) {
            $id_list[] = $product_id;
            $cat_ids = $product->get_category_ids();
            if (!in_array($my_sales_id, $cat_ids)) {
//				build_output();
                $html .= "<div class='error sales_missing'>" .
                        "<span class='title'>{$product_title}</span>" .
                        "<input type='submit' name='fix_cat_{$error_count}[submit]' value='hinzufügen' class='fixit'>" .
                        "<input type='hidden' name='fix_cat_{$error_count}[product_id]' value='{$product_id}'>" .
                        "<input type='hidden' name='fix_cat_{$error_count}[my_sales_id]' value='{$my_sales_id}'>" .
                        "<a href=" . get_permalink($id) . " target='_blank'>anzeigen</a>" .
                        "<a href='" . get_home_url() . "/wp-admin/post.php?post={$product_id}&action=edit' target='_blank'>editieren</a>" .
                        "</div>";
                $error_count++;
                $results["products"] = $product;
            }
            $results["html"] = $html;
            $results["all_product_ids"][] = $product_id;
        }
        $results["errors"] = $error_count;
    }
    return $results;
}

function sales_checker_start($my_sales_id, $del, $add) {

    $args = array(
        'include' => $my_sales_id
    );

    $product_category = get_terms('product_cat', $args)[0];

    $my_sales_name = $product_category->name;
    $my_sales_count = $product_category->count;
    $all_products_count = count($add["all_product_ids"]);
    
    $error_count_del = $del["errors"];
    $error_count_add = $add["errors"];
    $error_count = $error_count_del + $error_count_add;

    $ret = "";
    $fix = "";
    
    if ($error_count > 0) {
        $fix_button= "<div class='sales-checker fix-all'><input type='submit' name='fix_all' value='alles reparieren' class='fixit'></div>";
    }
    
    $f1 = $error_count > 0 ? "Upps" : "Alles Top";
    $f2 = $error_count > 0 ? "Die" : "Alle";
    $f3 = $error_count > 0 ? "nicht" : "";
    $f4 = $error_count > 0 ? "!" : ".";
    $p = "<p>{$f2} reduzierten Artikel ({$all_products_count}) stimmen <strong>{$f3}</strong> mit den Artikeln "
    . "in der Kategorie <strong>{$my_sales_name}</strong> ({$my_sales_count}) überein{$f4}</p>";
    
    $h2 = "<h4>Die Kategorie für preisreduzierte Artikel heisst \"<strong>{$my_sales_name }</strong>\"?</h4>";
    $h2 .= "<h2>{$f1} - {$error_count} Probleme entdeckt</h2>";
    $h3 = "";
    $h5 = "";
    $del_html = "";
    if ($error_count_del > 0) {
        $h3 .= "<h3>" . $error_count_del . " Artikel aus " . $my_sales_name . " entfernen</h3>";
        $h5 .= "<h5>Folgende Artikel (" . $error_count_del . ") sind nicht reduziert und sollten aus <strong>" . $my_sales_name . "</strong> entfernt werden:</h5>";
        $del_html = $del["html"];
    }
    $ret .= $h2 . $fix_button . $p . $h3 . $h5 . $del_html;
    
    $h3 = "";
    $h5 = "";
    $add_html = "";
    if ($error_count_add > 0) {
        $h3 = "<h3>" . $error_count_add . " Artikel zu " . $my_sales_name . " hinzufügen</h3>";
        $h5 .= "<h5>Es fehlen " . $error_count_add . " reduzierte Artikel in der Kategorie <strong>" . $my_sales_name . "</strong> und sollten hinzufügt werden: </h5>";
        $add_html = $add["html"];
    }
    $ret .= $h3 . $h5 . $add_html;
    $ret = '<div><form method="post" action="">' . $ret . '</form></div>';
    
    return $ret;
}

function shortcode_handler_my_sales($atts) {
    global $post;

    $a = shortcode_atts($atts);
    $my_sales_id = $a['cat_id'];

    $del = delete_from_sales($my_sales_id);
    $add = add_to_sales($my_sales_id);

    $ret = sales_checker_start($my_sales_id, $del, $add);

    return $ret;
}

####### Fix Sales ########

function fix_sales_handler_from_post($id) {
    global $woocommerce;

    if (isset($_POST["fix_all"])) {
        foreach ($_POST as $collection) {
            if (array_key_exists('product_id', $_POST) && !empty($collection['product_id']) && !empty($collection['my_sales_id'])) {
                $product_id = $collection['product_id'];
                $my_sales_id = $collection['my_sales_id'];
                fix_cat($product_id, $my_sales_id);
            }
        }
    } else {
        foreach ($_POST as $collection) {
            if (array_key_exists('submit', $_POST) && !empty($collection['product_id']) && !empty($collection['my_sales_id'])) {
                $product_id = $collection['product_id'];
                $my_sales_id = $collection['my_sales_id'];
                fix_cat($product_id, $my_sales_id);
            }
        }
    }
}

function fix_cat($post_id, $term_id) {
    global $woocommerce;

    $post_id = intval($post_id);
    $term_id = intval($term_id);

    $product = wc_get_product($post_id);

    switch ($term_id) {
        case SALES_CAT_ID:
            $is_attribute = $product->is_on_sale();
            break;
        default:
            return 0;
    }
    set_product_cats($product, $term_id, $is_attribute);
}

function set_product_cats($product, $term_id, $is_attribute) {
    $term_ids = $product->get_category_ids();
    $term_ids = array_unique(array_map('intval', $term_ids));

    if (!$is_attribute) {
        # remove id
        $term_ids = array_diff($term_ids, array($term_id));
    } else {
        # add id
        $term_ids[] = $term_id;
    }
    wp_set_object_terms($product->get_id(), $term_ids, 'product_cat');
}
