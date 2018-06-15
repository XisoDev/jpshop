<?php


/**
 * In WordPress Administration Screens
 *
 * @since 2.3.2
 */
if ( ! defined( 'WP_ADMIN' ) ) {
    define( 'WP_ADMIN', true );
}

if ( ! defined('WP_NETWORK_ADMIN') )
    define('WP_NETWORK_ADMIN', false);

if ( ! defined('WP_USER_ADMIN') )
    define('WP_USER_ADMIN', false);

if ( ! WP_NETWORK_ADMIN && ! WP_USER_ADMIN ) {
    define('WP_BLOG_ADMIN', true);
}

if ( isset($_GET['import']) && !defined('WP_LOAD_IMPORTERS') )
    define('WP_LOAD_IMPORTERS', true);

require_once('../../../../wp-load.php');
require_once('../define_arrays.php');

nocache_headers();

function getCategory($taxonomy = 'product_cat'){
    $category_list = array();
    $args = array(
        'taxonomy'     => $taxonomy
    );
    $all_categories = get_categories( $args );
    foreach ($all_categories as $cat) {
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;
            $category_list[$category_id] = new stdClass();
            $category_list[$category_id]->category_srl = $category_id;
            $category_list[$category_id]->title = $cat->name;

            $args2 = array(
                'taxonomy'     => $taxonomy,
                'child_of'     => 0,
                'parent'       => $category_id
            );
            $sub_cats = get_categories( $args2 );
            if($sub_cats) {
                $category_list[$category_id]->children = array();
                foreach($sub_cats as $sub_category) {
                    $category_id_3 = $sub_category->term_id;
                    $oCategory = new stdClass();
                    $oCategory->category_srl = $sub_category->term_id;
                    $oCategory->title = $sub_category->name;
                    $category_list[$category_id]->children[$category_id_3] = $oCategory;

                    $args3 = array(
                        'taxonomy'     => $taxonomy,
                        'child_of'     => 0,
                        'parent'       => $category_id_3
                    );
                    $sub2_cats = get_categories( $args3 );

                    if($sub2_cats) {
                        $category_list[$category_id]->children[$category_id_3]->children = array();
                        foreach($sub2_cats as $sub_category2) {
                            $category_id_4 = $sub_category2->term_id;
                            $oCategory = new stdClass();
                            $oCategory->category_srl = $category_id_4;
                            $oCategory->title = $sub_category2->name;
                            $category_list[$category_id]->children[$category_id_3]->children[$category_id_4] = $oCategory;

                            $args4 = array(
                                'taxonomy'     => $taxonomy,
                                'child_of'     => 0,
                                'parent'       => $category_id_4
                            );
                            $sub3_cats = get_categories( $args4 );

                            if($sub3_cats) {
                                $category_list[$category_id]->children[$category_id_3]->children[$category_id_4]->children = array();
                                foreach ($sub3_cats as $sub_category3) {
                                    $oCategory = new stdClass();
                                    $oCategory->category_srl = $sub_category3->term_id;
                                    $oCategory->title = $sub_category3->name;
                                    $category_list[$category_id]->children[$category_id_3]->children[$category_id_4]->children[$oCategory->category_srl] = $oCategory;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $category_list;
}

function getCategoryIds($splitter = ">"){
    $category_list = getCategory();
    $category_ids = array();
    foreach($category_list as $category){
        $category_ids[$category->category_srl] = $category->title;
        if(!$category->children) continue;
        foreach($category->children as $cate2){
            $category_ids[$cate2->category_srl] = sprintf("%s%s%s",$category->title,$splitter,$cate2->title);
            if(!$cate2->children) continue;
            foreach($cate2->children as $cate3){
                $category_ids[$cate3->category_srl] = sprintf("%s%s%s%s%s",$category->title,$splitter,$cate2->title,$splitter,$cate3->title);
                if(!$cate3->children) continue;
                foreach($cate3->children as $cate4){
                    $category_ids[$cate3->category_srl] = sprintf("%s%s%s%s%s%s%s",$category->title,$splitter,$cate2->title,$splitter,$cate3->title,$splitter,$cate4->title);
                }
            }
        }
    }
    return $category_ids;
}
?>