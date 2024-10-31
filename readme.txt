=== Nutrition Information Table Facts ===
Contributors: gaizkagzgr
Tags: food, cook, nutrition fact, nutrition table, nutrition schema, comida, nutricion, tabla de nutricion, tabla nutricional, nutricion json, nutrition schema
Requires at least: 3.0
Tested up to: 4.9.7
Requires PHP: 5.6
Stable tag: 0.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.me/gaizkagzgr

== Description ==

This plugin creates a nutrition table (custom post type) which can be assigned to any product (WooCommerce), page, post. It includes structured data json+ld on every table, for increase SEO and optimization. The plugin is multilanguage (English/Spanish).

Use the shortcode [nitf-label id=XX] to generate a nutrition table with label style and echo it where you want.

Reference daily intake values come from some websites and searching information (RDA).

**************************

Este plugin crea una tabla de nutrición (tipo de publicación personalizada) que se puede asignar a cualquier producto (WooCommerce), página, publicación. Incluye datos estructurados JSON+ld en cada tabla para aumentar el SEO y la optimización. El complemento es multilenguaje (inglés / español).

Utilice el shortcode [nitf-label id=XX] para generar una tabla de nutrición con el estilo de etiqueta y ponlo donde quieras.

Los valores de referencia de ingesta diaria provienen de algunos sitios web e información de búsqueda (CDR).

**************************

Reference webs for information / sitios web para obtener la información:

Schema:
* https://schema.org/nutrition
* https://schema.org/NutritionInformation

Info:
* https://en.wikipedia.org/wiki/Dietary_Reference_Intake
* https://muyfitness.com/la-ingesta-diaria-recomendada-de-calorias-carbohidratos-grasas-sodio-y-proteinas_13129697/
* http://doctorjuliovida.com/carbohidratos/
* https://as.com/deporteyvida/2017/08/26/portada/1503755888_446146.html
* https://www.directoalpaladar.com/manerasdedisfrutar/cual-es-la-ingesta-diaria-de-fibra-recomendada-y-como-lograrlo-con-all-bran-r
* https://www.botanical-online.com/medicinalesgrasascantidadrecomendada.htm
* https://medlineplus.gov/spanish/ency/patientinstructions/000838.htm
* https://www.campodebenamayor.es/2011/02/04/que-es-el-colesterol/
* https://biotrendies.com/cual-es-la-cantidad-diaria-recomendada-de-sodio.html	
* http://www.fundaciondelcorazon.com/corazon-facil/blog-impulso-vital/2296-y-tu-iusas-mucho-el-salero.html

== Installation ==

1. Upload the folder nutrition-information-table-facts to the /wp-content/plugins directory or use WordPress tool for upload nutrition-information-table-facts.zip

2. Activate the plugin in plugins menu.

3. Create a table in the admin panel of plugin (Nutritional table)

4. Include the shortcode [nitf-label id=XX] where you want a specific label to be displayed. 

5. DEVELOPERS: When creating the label you can also specify the Page or Post you want the label to appear and include echo do_shortcode("[nitf-label]"); in the template where you want the label.

***************************

1. Cargue la carpeta nutrition-table en el directorio /wp-content/plugins o usa la herramienta WordPress para cargar nutrition-information-table-facts.zip

2. Activa el plugin en el menú de plugins.

3. Crea una tabla en el panel de administración del complemento (tabla nutricional).

4. Incluye el código corto [nitf-label id=XX] en el que desees que se muestre una tabla específica.

5. DESARROLLADORES: al crear la etiqueta, también puedse especificar la página o publicación en la que desees que aparezca la etiqueta e incluir echo do_shortcode("[nitf-label]"); en la plantilla donde quieras la tabla.

== Frequently Asked Questions ==

= What units does the label use? =

* Grams: Total fat, Satured fat, Transgenic fat, Carbohydrates, Fiber, Sugars, Salt and Proteins
* Milligrams (mg): Cholesterol, Chlorine and Sodium
* Unitless: calories, serving size
* You should include the unit in the serving size attribute (example: "mg, ml, g, l, oz...")

= Which is the RDA table value? =

* Total fat => 65g
* Satured fat => 20g
* Cholesterol => 300mg
* Salt => 6g
* Sodium => 40% of salt.
* Carbohydrates => 200g
* Fiber => 27g
* Proteins => 55g
* Sugars => 90g.

== Screenshots ==

1. Menu in dashboard of Nutrition Table.
2. Listing all tables options with shortcode, page (with a href) and date.
3. Adding new table
4. Nutritional table echo.
5. JSON+LD on every page/post/product where is the shortcode.

== Upgrade Notice ==

= 0.1 =
* Initial release

== Changelog ==

= 0.1 =
* Initial release
