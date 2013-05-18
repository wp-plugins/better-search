<?php /* Sample template for Better Search Plugin for WordPress Default theme */

	get_header(); ?>
	<div id="content" class="narrowcolumn">

	<div id="heatmap" style="padding: 5px; border: 1px dashed #ccc">
	<div style="padding: 5px; border-bottom: 1px dashed #ccc">
	<h2>
	<?php echo get_bsearch_title_daily(); ?>
	</h2>
	<?php echo get_bsearch_heatmap(true); ?>
	</div>
	<div style="padding: 5px;">
	<h2>
	<?php echo get_bsearch_title(); ?>
	</h2>
	<?php echo get_bsearch_heatmap(false); ?>
	</div>
	<div style="clear:both">&nbsp;</div>
	</div>

	<div style="padding: 5px;margin: 5px;">
	<?php echo get_bsearch_form($s); ?>
	</div>

	<div id="searchresults"><h2 class="pagetitle"><?php _e('Search Results for: ', BSEARCH_LOCAL_NAME); ?>	&quot;<?php echo $s; ?>&quot;</h2>

	<?php
		$search_info = get_bsearch_terms($s);

		$searches = get_bsearch_matches($search_info);
		$topsearch = $searches[0];
		$topscore = $topsearch->score;
		$display_range = bsearch_match_range(count($searches), $limit);
		
		if ($searches && $display_range['last'] >= 0) {
			
			//---------------------------------------------------------
			//	calculate values for page numbers and whatnot
			//---------------------------------------------------------
			
			$bsearch_settings = bsearch_read_options();
			if (!($limit)) $limit = intval(bsearch_RemoveXSS(bsearch_quote_smart($_GET['limit']))); // Read from GET variable
			if (!($limit)) $limit = $bsearch_settings['limit']; // Default number of results as entered in WP-Admin
			// shouldn't be necessary but just in case since there's division ahead...
			if ($limit < 1) $limit = 10;
			
			$page = intval(bsearch_RemoveXSS(bsearch_quote_smart($_GET['paged']))); // Read from GET variable
			if (!($page)) $page = 0; // Default page value.
		
			$numrows = count($searches);
			$page_num = ceil(($page+1)/$limit);
			$num_pages = ceil($numrows/$limit);
			$last_match_on_page = min($page + $limit, $numrows);
			$first = $display_range['first'] +1;
			$last = $display_range['last'] +1;
			
			$pages = intval($numrows/$limit); // Number of results pages.
			if ($numrows % $limit) {$pages++;} // has remainder so add one page
			$current = ($page/$limit) + 1; // Current page number.
			if (($pages < 1) || ($pages == 0)) {$total = 1;} // If $pages is less than one or equal to 0, total pages is 1.
			else {	$total = $pages;} // Else total pages is $pages value.
			
			//-----------------------------
			//	display the header
			//-----------------------------
			
			$output = '';
			
			// get the search string
			
			$output .= '<table class="bsearch_results_header" width="100%" border="0">
			 <tr>
			  <td width="50%" align="left">';
			$output .= __('Results', BSEARCH_LOCAL_NAME);
			$output .= ' <strong>'.$first.'</strong> - <strong>'.$last.'</strong> ';
			$output .= __('of', BSEARCH_LOCAL_NAME);
			$output .= ' <strong>'.$numrows.'</strong>
			  </td>
			  <td width="50%" align="right">';
			$output .= __('Page', BSEARCH_LOCAL_NAME);
			$output .= ' <strong>'.$current.'</strong> ';
			$output .= __('of', BSEARCH_LOCAL_NAME);
			$output .= ' <strong>'.$total.'</strong>
			  </td>
			 </tr>
			 <tr>
			  <td colspan="2" align="right">&nbsp;</td>
			 </tr>
			 <tr>
			  <td align="left"></td>';
			$output .= '<td align="right">';
			$output .= __('Results per-page', BSEARCH_LOCAL_NAME);
			$output .= ': <a href="'.get_settings('siteurl').'/?s='.$s.'&limit=10">10</a> | <a href="'.get_settings('siteurl').'/?s='.$s.'&limit=20">20</a> | <a href="'.get_settings('siteurl').'/?s='.$s.'&limit=50">50</a> | <a href="'.get_settings('siteurl').'/?s='.$s.'&limit=100">100</a> 
			  </td>
			 </tr>
			 <tr>
			  <td colspan="2" align="right"><hr /></td>
			 </tr>
			</table>';
			echo $output;


			//-------------------------------------------------------
			//	loop through the correct range of matches
			//-------------------------------------------------------
			
	
			for ($i = $display_range['first']; $i <= $display_range['last']; $i++) {
				$search = $searches[$i];
				//print_r($search);
				$post_title = trim(stripslashes($search->post_title));
				$excerpt = htmlspecialchars(trim(stripslashes($search->post_excerpt)));
				$content = trim(stripslashes($search->post_content));
				echo '<h2><a href="'.get_permalink($search->ID).'" rel="bookmark">'.$post_title."</a></h2>\n\t\t\t<p>";
				if ($search->score > 0) {
						$score = $search->score * 100 / $topscore;
						_e('Relevance: ', BSEARCH_LOCAL_NAME); printf("%.1f", $score);
						echo '%&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				echo date('Y-m-d H:i:s',strtotime($search->post_date));
				echo "</p>\n\t\t\t<p>";
				if ($excerpt) {
					echo $excerpt;
				} else {
					echo bsearch_search_excerpt($content);
				}
				echo '</p>';
			} //end of for loop


			//-----------------------------
			//	display the footer
			//-----------------------------
			
			$url_root = get_settings('siteurl');
			$output =  "\n\n\t\t\t<p style=\"text-align:center\">";
			if ($page != 0) { // Don't show back link if current page is first page.
				$back_page = $page - $limit;
				$output .=  "<a href=\"".$url_root."/?s=$s&paged=$back_page&limit=$limit\">&laquo; ";
				$output .=  __('Previous', BSEARCH_LOCAL_NAME);
				$output .=  "</a>    \n";
			}
		
			for ($i=1; $i <= $num_pages; $i++) // loop through each page and give link to it.
			{
				$ppage = $limit*($i - 1);
				if ($ppage == $page){
					$output .=  ("<b>$i</b>\n");} // If current page don't give link, just text.
				else{
					$output .=  ("<a href=\"".$url_root."/?s=$s&paged=$ppage&limit=$limit\">$i</a> \n");
				}
			}
				
			if ($page+$limit <= $numrows) { // If last page don't give next link.
				$output .=  "    <a href=\"".$url_root."/?s=$s&paged=".($page + $limit)."&limit=".$limit."\">";
				$output .=  __('Next', BSEARCH_LOCAL_NAME);
				$output .=  " &raquo;</a>";
			}
			$output .=   '</p>';
			echo $output;


		} else {
			// no matches!
			echo '<h2>'; 
			_e('No results found for ', BSEARCH_LOCAL_NAME);
			echo '&quot;'.$s.'&quot;';
			echo '</h2>';
		}
	?>

	</div>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
