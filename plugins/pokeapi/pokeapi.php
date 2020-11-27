<?php
/**
 * Plugin Name:       Pokeapi
 * Description:       Plugin basado en la api de pokemon para ser manejado desde el dashboard
 * Version:           1.0.0
 * Author:            Daniel Cordero
 * Author URI:        https://author.example.com/
 */
if(!defined('POKEAPI_PLUGIN_URL')) {
    define('POKEAPI_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}

class pokeapi_plugin {
	function __construct() {
        add_action('admin_menu', array( &$this,'pokeapi_plugin_register_menu') );
	   
    }
    function pokeapi_plugin_register_menu(){
        add_menu_page( 'Pokeapi plugin', 'Pokeapi', 'manage_options', 'test-plugin', 'pokeapi_init' );
    }
}


function pokeapi_plugin_register_database(){
    global $wpdb;
    $table_name = $wpdb->prefix . "pokeapishortcode";
    $charset_collate = $wpdb->get_charset_collate();
      
    $sql = "CREATE TABLE `$table_name` (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        pokemon text NOT NULL,
        shortcode text NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
 }

 function pokeapi_plugin_delete_database(){
    global $wpdb;
    $table_name = $wpdb->prefix . "pokeapishortcode"; 
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
 }

 register_activation_hook( __FILE__, 'pokeapi_plugin_register_database' );
 register_deactivation_hook(__FILE__, 'pokeapi_plugin_delete_database');

$GLOBALS['pokeapi_plugin'] = new pokeapi_plugin();

function pokeapi_init(){
    global $wpdb;
    $table_name = $wpdb->prefix . "pokeapishortcode"; 
    $apiurl = 'https://pokeapi.co/api/v2/';
    $response = wp_remote_get($apiurl.'pokemon?limit=100');
    $items = wp_remote_retrieve_body( $response );
    $items = json_decode($items,true);
    $items = $items['results'];
    $band = true;
    $names = array();
    if($band){
        foreach($items as $key => $item){
            $names[$key+1] = $item['name'];
        }
        $band = false;
    }

    if (isset($_POST['pokemon'])) {   
        $pokemon = $_POST['pokemon'];
        if(isset( $_POST['name']) ){
            $name_atts = 'true';
        }else{
            $name_atts = 'false';
        }
        if(isset( $_POST['ability']) ){
            $ability_atts = 'true';
        }else{
            $ability_atts = 'false';
        }
        if(isset( $_POST['img']) ){
            $img_atts = 'true';
        }else{
            $img_atts = 'false';
        }
       
        $date = date( 'Y-m-d H:i:s', strtotime( 'now' ) );
        $data=array(
             'pokemon'    => "$names[$pokemon]",
             'shortcode'  => "[pokeapi id=$pokemon name=$name_atts ability=$ability_atts img=$img_atts]",
             'created_at' => "$date",
        );
        $insert = $wpdb->insert( $table_name, $data);
}
?>
        <form method="post">
            <h2>Atributos a mostrar en las entradas: </h2>
                <input type="checkbox" name="name" value=true checked>Nombre
                <input type="checkbox" name="ability" value=true cheked>Habilidades
                <input type="checkbox" name="img" value=true>Imagen
            <hr>
            <label for="pokemon">Seleccione Pokemon</label>
            <select name="pokemon" id="">
                <?php foreach($items as $key => $item ): ?>
                    <option value="<?php echo esc_attr($key+1); ?>"> <?php echo  esc_html( $item['name']); ?> </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">agregar pokemon</button>
        </form>
        <hr>
       
        <h2>Shortcodes generados</h2>
    <!--    -->
    
<?php
    if(isset($_POST['id'])){
        $delete = $wpdb->delete($table_name,array('id'=>$_POST['id']));
        if($delete){
            echo sprintf('<span>Se ha eleminado con éxito el shortcode del pokemon</span>');
        }
    }
    
    $items = $wpdb->get_results("SELECT * FROM `$table_name`");
    $result = '';
    ?>

    <table class="table-title">
        <tr>
            <th>Nombre</th>
            <th>shortcode</th>
            <th>fecha agregado</th>
        </tr>

    </table>
    
    
    <?php foreach($items as $item): ?>
        <form method="post" action="<?= sprintf('%s%s', 'admin.php?page=',$_GET['page'] ); ?>"  >
        <tr>
            <table class="table-content">
                <td> <p> <?php print_r($item->pokemon) ;?>    </p></td>
                <td> <p> <?php print_r($item->shortcode) ;?>    </p></td>
                <td> <p> <?php print_r($item->created_at) ;?>   </p></td>
               
                    <td><button type="submit">Eliminar</button></td>
                    <?php
                    echo sprintf('<input type="hidden" value="%s" name="%s"> ',$item->id, 'id' );
                    ?>                     
            </table>  
        </tr>      
        </form>     
    <?php endforeach; ?>
    
    <?php
}
//  creacion del shortcode
add_shortcode('pokeapi', 'shortcode_pokeapi');

function shortcode_pokeapi($atts) {
    $pokemon = array();
    $atts = shortcode_atts( array (
        'id'      => null,
        'name'    => true,
        'ability' => false,
        'img'     => false,
        ), $atts );
    
    if( isset($atts['id']) ){
        $apiurl = 'https://pokeapi.co/api/v2/';
        $response = wp_remote_get($apiurl.'pokemon/'.$atts['id']);
        $pokemon = wp_remote_retrieve_body( $response );
        $pokemon = json_decode($pokemon,true);
    }
 
    ob_start();
?>
    <?php if( isset($atts['id']) ): ?>
        <?php if(isset($pokemon)): ?>
        <div class="pokemon_container">    
            <div class="pokemon_info">
                <?php if($atts['name']=='true'):?>
                    <div class="pokemon_name">
                        <h3>Nombre:</h3> <p><?php echo esc_html( $pokemon['forms'][0]['name'] ) ;?> </p>
                    </div>
                <?php endif;?>
                <?php if($atts['img']=='true'):?>
                <img class="imagen" src="<?php echo $pokemon['sprites']['other']['official-artwork']["front_default"]?>" alt=""> 
            <?php endif; ?>
            </div>  
            <div class="pokemon_abilities">
                    <?php if($atts['ability']=='true'): ?>
                    <h4>Habilidades: </h4>
                    <p>
                        <?php  foreach($pokemon['abilities'] as $key => $item):?>
                            <?php echo esc_html($pokemon['abilities'][$key]['ability']['name']) ?>
                        <?php  endforeach;?>
                    <?php endif ?>
                    </p>
            </div>   
        </div>
        <?php endif ;?>
    <?php else:?>
            <h1>No existe el pokemon</h1>
    <?php endif;?>
<?php
    return ob_get_clean();
}