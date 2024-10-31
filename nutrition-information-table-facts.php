<?php
/*
Plugin Name: Nutrition Information Table Facts
Description:  Show a nutrition table in the place you choose: pages, products, post, etc with a simple shortcode. The plugin includes structured markup JSON + ld. Languages: EN / ES.
Text Domain: nutrition-information-table-facts
Domain Path: /languages
Author: Gaizka González Graña
Version: 0.1
Author URI: https://gycza.com/
License: GPL v2

Nutrition table for WordPress
Copyright (C) 2012-2018, Gaizka González Graña, gaizka.gzgr@gmail.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// Prevent direct file access
defined( 'ABSPATH' ) or exit;
//
add_shortcode( 'nitf-label', 'nitf_label_shortcode');//LOAD SHORTCODE
add_action( 'wp_head', 'nitf_style');//LOAD STYLE
add_action( 'init', 'nitf_init');//INIT
load_plugin_textdomain('nutrition-information-table-facts', false, basename( dirname( __FILE__ ) ) . '/languages' );//LOAD TRANSLATION
add_filter( 'manage_edit-nitf-label_columns', 'nitf_modify_nutritional_label_table' );
add_filter( 'manage_posts_custom_column', 'nitf_modify_nutritional_label_table_row', 10, 2 );
add_action( 'add_meta_boxes', 'nitf_create_metaboxes' );
add_action( 'save_post', 'nitf_save_meta', 1, 2 );

	/* RDA-CDR / 2000 Kcal / Daily */
	$rda = array(
		'totalfat' 					=> 65, //g
		'satfat' 					=> 20, //16 a 22g
		'cholesterol' 				=> 300, //mg
		'sal' 						=> 6, //g
		'carbohydrates' 			=> 200, // min 140-200g
		'fiber' 					=> 27, //g 25-30
		'protein' 					=> 55, //g
		'vitamin_a' 				=> 5000,
		'vitamin_c' 				=> 60,
		'calcium' 					=> 1000,
		'sugars'					=> 90, //g
		'iron' 						=> 18
	);


	/* BASE NUTRIIONAL FIELDS */
	$nutrional_fields = array(
		'servingsize' 				=> __('Unidad de medida (g,ml,mg...)', 'nutrition-information-table-facts'),
		'servings' 					=> __('Valor medio (Cantidad)', 'nutrition-information-table-facts'),
		'calories' 					=> __('Kilocalorías (Kcal)', 'nutrition-information-table-facts'),
		'totalfat' 					=> __('Grasas', 'nutrition-information-table-facts'),
		'satfat' 					=> __('Grasas saturadas', 'nutrition-information-table-facts'),
		'transfat' 					=> __('Grasas transgénicas', 'nutrition-information-table-facts'),
		'cholesterol' 				=> __('Colesterol (mg)', 'nutrition-information-table-facts'),
		'sal' 						=> __('Sal','nutrition-information-table-facts'),
		'carbohydrates' 			=> __('Hidratos de carbono', 'nutrition-information-table-facts'),
		'fiber' 					=> __('Fibra', 'nutrition-information-table-facts'),
		'sugars' 					=> __('Azúcares', 'nutrition-information-table-facts'),
		'protein' 					=> __('Proteínas', 'nutrition-information-table-facts')
	);


/*
 * Init
 */
function nitf_init()
{
	//ADMIN PANEL
	$labels = array(
		'name' 						=> __('Tabla nutricional', 'nutrition-information-table-facts'),
		'singular_name' 			=> __('Nutrición', 'nutrition-information-table-facts'),
		'add_new' 					=> __('Añadir nueva', 'nutrition-information-table-facts'),
		'add_new_item' 				=> __('Añadir nueva tabla', 'nutrition-information-table-facts'),
		'edit_item' 				=> __('Editar tabla', 'nutrition-information-table-facts'),
		'new_item' 					=> __('Nueva tabla', 'nutrition-information-table-facts'),
		'all_items' 				=> __('Todas las tablas', 'nutrition-information-table-facts'),
		'view_item' 				=> __('Ver tabla', 'nutrition-information-table-facts'),
		'search_items' 				=> __('Buscar tabla', 'nutrition-information-table-facts'),
		'not_found' 				=>  __('No se ha encontrado la tabla', 'nutrition-information-table-facts'),
		'not_found_in_trash' 		=> __('No se han encontrado en la papelera', 'nutrition-information-table-facts'),
		'parent_item_colon' 		=> '',
		'menu_name' 				=> __('Tabla nutricional', 'nutrition-information-table-facts')
	);
	//OPTIONS
	$args = array(
		'labels' 					=> $labels,
		'public' 					=> true,
		'publicly_queryable' 		=> false,
		'show_ui' 					=> true,
		'show_in_menu'				=> true,
		'query_var' 				=> true,
		'rewrite' 					=> false,
		'capability_type' 			=> 'post',
		'has_archive'				=> false, //NO ARCHIVE -> SEO
		'hierarchical' 				=> false,
		'menu_position' 			=> null,
		'menu_icon' 				=> plugins_url('/assets/img/nutrition-menu-icon.png', __FILE__),
		'supports' 					=> array( 'title' )
	);
	//nitf-label -> register post type.
	register_post_type('nitf-label', $args);
}


/*
 * Meta Box with Data
 */
function nitf_create_metaboxes()
{
	$options = __('Opciones de la tabla nutricional','nitf-label');
	add_meta_box( 'nitf_metabox_render', ''.$options.'', 'nitf_metabox_render', 'nitf-label', 'normal', 'default' );
}

function nitf_metabox_render()
{
	global $post, $nutrional_fields;
	$meta_values = get_post_meta( $post->ID );

	$pages = get_posts( array( 'post_type' => 'page', 'numberposts' => -1 ) );
	$posts = get_posts( array( 'numberposts' => -1 ) );

	$selected_page_id = isset($meta_values['_pageid']) ? $meta_values['_pageid'][0] : 0;
	?>

	<div style="border-bottom: solid 1px #ccc; padding-bottom: 10px; padding-top: 10px;">
		<div style="width: 75px; margin-right: 10px; float: left; text-align: right; padding-top: 3px;">
			<?php _e('Page'); ?>
		</div>
		<select name="pageid" style="float: left;">
			<option value=""><?php _e('Selecciona una página...','nutrition-information-table-facts'); ?></option>
			<optgroup label="<?php _e('Páginas','nutrition-information-table-facts'); ?>">
				<?php foreach($pages as $page) { ?>
				<option value="<?php echo $page->ID ?>"<?php if($selected_page_id == $page->ID) echo " SELECTED"; ?>><?php echo $page->post_title ?></option>
				<?php } ?>
			</optgroup>
			<optgroup label="<?php _e('Posts'); ?>">
				<?php foreach($posts as $post) { ?>
				<option value="<?php echo $post->ID ?>"<?php if($selected_page_id == $post->ID) echo " SELECTED"; ?>><?php echo $post->post_title ?></option>
				<?php } ?>
			</optgroup>
		</select>
		<div style="clear:both;"></div>
	</div>

	<?php
	foreach( $nutrional_fields as $name => $nutrional_field ) { ?>
	<div style="padding: 3px 0;">
		<div style="width: 125px; margin-right: 10px; float: left; text-align: right; padding-top: 5px;">
			<?php echo $nutrional_field ?>
		</div>
		<input type="text" style=" float: left; width: 45px;" name="<?php echo $name ?>" value="<?php if(isset($meta_values['_' . $name])) { echo esc_attr( $meta_values['_' . $name][0] ); } ?>" maxlength="4"/>

		<div style="clear:both;"></div>
	</div>
<?php
	}
}

function nitf_save_meta( $post_id, $post )
{                 
	global $nutrional_fields;
	foreach( $nutrional_fields as $name => $nutrional_field )
	{
		if ( isset( $_POST[ $name ] ) ) { 
			/* If we get servingsize in $name var, is a text field instead of float and is only 2 letter. */
			if ( $name == 'servingsize'){
				$data = substr( sanitize_text_field( $_POST[ $name ] ), 0, 2 );
			}
			/* If we didn't get servingsize is a float var, we need float because we need decimal and 4 */
			else{
				 $data = substr( floatval( $_POST[ $name ] ), 0, 4 );
			}
			//update the record with $data
			update_post_meta( $post_id, '_' . $name,  $data );
		}
	}
	/* Sanitize integer with inval on pageid. */
	if ( isset( $_POST[ 'pageid' ] ) ) { update_post_meta( $post_id, '_pageid', wp_strip_all_tags( intval( $_POST['pageid'] ) ) ); }
}

/*
 * Add Column to WordPress Admin
 * Displays the shortcode needed to show label
 *
 * 2 Functions
 */

function nitf_modify_nutritional_label_table( $column )
{
	$columns = array(
		'cb'       			=> '<input type="checkbox" />',
		'title'    			=> __('Nombre', 'nutrition-information-table-facts'),
		'nutr_shortcode'    => __('Shortcode', 'nutrition-information-table-facts'),
		'nutr_page'    		=> __('Página', 'nutrition-information-table-facts'),
		'date'     			=> __('Fecha', 'nutrition-information-table-facts')
	);

	return $columns;
}
function nitf_modify_nutritional_label_table_row( $column_name, $post_id )
{
 	if($column_name == "nutr_shortcode")
 	{
 		echo "[nitf-label id={$post_id}]";
 	}

 	if($column_name == "nutr_page")
 	{
 		$p_name = get_the_title( get_post_meta( $post_id, "_pageid", true ) );
 		$p_url = get_permalink( get_post_meta( $post_id, "_pageid", true ) );
 		if($p_url){
 			echo '<a href="'.esc_url($p_url).'">'.esc_html($p_name).'</a>';
 		}
 	}

}


/*
 * output our style sheet at the head of the file
 * because it's brief, we just embed it rather than force an extra http fetch
 *
 * @return void
 */
function nitf_style()
{
?>
<style type='text/css'>
	.nutrition-information-table-facts { border: 1px solid #ccc; font-family: helvetica, arial, sans-serif; font-size: .9em; width: 22em; padding: 1em 1.25em 1em 1.25em; line-height: 1.4em; margin: 1em; }
	.nutrition-information-table-facts hr { border:none; border-bottom: solid 8px #666; margin: 3px 0px; }
	.nutrition-information-table-facts .heading { font-size: 2.6em; font-weight: 900; margin: 0; line-height: 1em; }
	.nutrition-information-table-facts .indent { margin-left: 1em; }
	.nutrition-information-table-facts .small { font-size: .8em; line-height: 1.2em; }
	.nutrition-information-table-facts .item_row { border-top: solid 1px #ccc; padding: 3px 0; }
	.nutrition-information-table-facts .amount-per { padding: 0 0 8px 0; }
	.nutrition-information-table-facts .daily-value { padding: 0 0 8px 0; font-weight: bold; text-align: right; border-top: solid 4px #666; }
	.nutrition-information-table-facts .f-left { float: left; }
	.nutrition-information-table-facts .f-right { float: right; }
	.nutrition-information-table-facts .noborder { border: none; }

	.cf:before,.cf:after { content: " "; display: table;}
	.cf:after { clear: both; }
	.cf { *zoom: 1; }
</style>
<?php
}


/*
 *
 * @param array $atts
 * @return string
 */
function nitf_label_shortcode($atts)
{
	$id = (int) isset($atts['id']) ? $atts['id'] : false;
	$width = (int) isset($atts['width']) ? $atts['width'] : 22;

	if($id) { return nitf_label_generate($id, $width); }
	{
		global $post;

		$label = get_posts( array( 'post_type' => 'nitf-label', 'meta_key' => '_pageid', 'meta_value' => $post->ID ));

		if($label)
		{
			$label = reset($label);
			return nitf_label_generate( $label->ID, $width );
		}
	}
}


/*
 * @param integer $contains
 * @param integer $reference
 * @return integer
 */
function nitf_percentage($contains, $reference)
{
	return round( $contains / $reference * 100 );
}

/*
 * @param array $args
 * @return string
 */
function nitf_label_generate( $id, $width = 22 )
{
	global $rda, $nutrional_fields;

	$label = get_post_meta( $id );

	if(!$label) { return false; }

	// GET VARIABLES
	foreach( $nutrional_fields as $name => $nutrional_field )
	{
		//Sanatized esc_html on values.
		$$name = esc_html($label['_' . $name][0]);
	}

	// BUILD CALORIES IF WE DONT HAVE ANY
	if($calories == 0)
	{
		$calories = ( ( $protein + $carbohydrates ) * 4 ) + ($totalfat * 9);
	}

	// WIDTH THE LABEL
	$style = '';
	if($width != 22)
	{
		$style = " style='width: " . $width . "em; font-size: " . ( ( $width / 22 ) * .75 ) . "em;'";
	}

	$rtn = "";
	$rtn .= "<div class='nutrition-information-table-facts' id='nutrition-information-table-facts-$id' " . ($style ? $style : "") . ">\n";

	$rtn .= "	<div class='heading'>".__("Información Nutricional", "nutrition-information-table-facts")."</div>\n";

	$rtn .= "	<div>" . __("Valores medios por", "nutrition-information-table-facts") . " <strong>" . $servings . $servingsize ."</strong></div>\n";

	$rtn .= "	<hr />\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>".__("Valor energético", "nutrition-information-table-facts")." (kj/kcal)</strong></span>";
	$rtn .= "		<span class='f-right'>" . $calories*4.184 . " / " . $calories . "</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='item_row daily-value small'>% " . __("Valor diario", "nutrition-information-table-facts") . "*</div>\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>".__("Proteínas", "nutrition-information-table-facts")."</strong> ". $protein ."g</span>\n";
	$rtn .= "		<span class='f-right'>". nitf_percentage($protein, $rda['protein'])."%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>" . __("Hidratos de carbono", "nutrition-information-table-facts") . "</strong> " . $carbohydrates . "g</span>\n";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($carbohydrates, $rda['carbohydrates']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>" . __("Grasas totales", "nutrition-information-table-facts") . "</strong> " . $totalfat . "g</span>\n";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($totalfat, $rda['totalfat']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='indent item_row cf'>\n";
	$rtn .= "		<span class='f-left'>" . __("Grasas saturadas", "nutrition-information-table-facts") . " " . $satfat . "g</span>\n";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($satfat, $rda['satfat']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='indent item_row cf'>\n";
	$rtn .= "		<span>" . __("Grasas transgénicas", "nutrition-information-table-facts") . " " . $transfat . "g</span>";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='indent item_row cf'>\n";
	$rtn .= "		<span>".__("Azúcares", "nutrition-information-table-facts")." ". $sugars ."g</span>";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($sugars, $rda['sugars']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>" . __("Fibra", "nutrition-information-table-facts")."</strong> ". $fiber . "g</span>\n";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($fiber, $rda['fiber']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>" . __("Sal", "nutrition-information-table-facts")."</strong> " . $sal . "g</span>\n";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($sal, $rda['sal']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='indent item_row cf'>\n";
	$rtn .= "		<span>".__("Sodio", "nutrition-information-table-facts")." ". (($sal*0.4)*1000) ."mg</span>";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='indent item_row cf'>\n";
	$rtn .= "		<span>".__("Cloro", "nutrition-information-table-facts")." ". (($sal*0.6)*1000) ."mg</span>";
	$rtn .= "	</div>\n";

	$rtn .= "	<div class='item_row cf'>\n";
	$rtn .= "		<span class='f-left'><strong>" . __("Colesterol", "nutrition-information-table-facts")."</strong> ". $cholesterol . "mg</span>\n";
	$rtn .= "		<span class='f-right'>" . nitf_percentage($cholesterol, $rda['cholesterol']) . "%</span>\n";
	$rtn .= "	</div>\n";

	$rtn .= "	<hr />\n";

	$rtn .= "	<div class='small cf'>\n";
	$rtn .= "		*" . __("El porcentaje de valores diarios se basa en una dieta de 2,000 calorías. Sus valores diarios pueden ser más altos o más bajos según sus necesidades calóricas (Edad, peso, altura, ejercicio, etc.)");
	$rtn .= "	</div>\n";

	$rtn .= "</div>";

	$rtn .= '
		<script type="application/ld+json">
		{
			"@context": "http://schema.org/",
			"@type": "NutritionInformation",
			"calories": "'.$calories.'kcal",
			"carbohydrateContent": "'.$carbohydrates.'g",
			"cholesterolContent": "'.$cholesterol.'mg",
			"fatContent": "'.$totalfat.'g",
			"fiberContent": "'.$fiber.'g",
			"proteinContent": "'.$protein.'g",
			"saturatedFatContent": "'.$satfat.'g",
			"servingSize": "'.$servingsize.'",
			"sodiumContent": "'.(($sal*0.4)*1000).'mg",
			"sugarContent": "'.$sugars.'g",
			"transFatContent": "'.$transfat.'g"
		}
		</script>
	';

	return $rtn;
}

?>
