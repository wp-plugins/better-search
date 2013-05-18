<?php
/*
Template Name: Template for Better Search results
*/
?>

<?php get_header(); ?>

<div id="content" class="narrowcolumn">

<?php

	if (!($limit)) $limit = intval(bsearch_RemoveXSS(bsearch_quote_smart($_GET['limit']))); // Read from GET variable
	if (!($limit)) $limit = $bsearch_settings['limit']; // Default number of results as entered in WP-Admin
	$page = intval(bsearch_RemoveXSS(bsearch_quote_smart($_GET['paged']))); // Read from GET variable
	if (!($page)) $page = 0; // Default page value.

	//query_posts('posts_per_page='.$limit);

	$form = get_bsearch_form($s);
	echo $form;	

	$s = attribute_escape(apply_filters('the_search_query', get_search_query()));
	$s = bsearch_quote_smart($s);
	$s = bsearch_RemoveXSS($s);
	$pageposts = bsearch_matches($s,$page,$limit);

?>
 <?php if ($pageposts){ ?>
  <?php foreach ($pageposts as $post){ ?>
    <?php setup_postdata($post); ?>

    <div class="post" id="post-<?php the_ID(); ?>">
      <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to ', BSEARCH_LOCAL_NAME); the_title(); ?>">
      <?php the_title(); ?></a></h2>
      <small><?php the_time('F jS, Y') ?> <!-- by <?php the_author() ?> --></small>
      <div class="entry">
         <?php the_excerpt('Read the rest of this entry »', BSEARCH_LOCAL_NAME); ?>
      </div>
  
      <p class="postmetadata">Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  
      <?php comments_popup_link('No Comments »', '1 Comment »', '% Comments »'); ?></p>
    </div>
  <?php } ?>
  
  <?php } else { ?>
    <h2 class="center"><?php _e('No results found for ', BSEARCH_LOCAL_NAME); echo '&quot;'.$s.'&quot;' ?></h2>
    <?php echo get_bsearch_form($s); ?>
 <?php } ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
