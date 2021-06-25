<?php
function yt_playlist_gallery_page(){}


function yt_playlist_gallery(){

    add_menu_page('Migrate Products', 'Migrate Products', 'manage_options', 'migrate-products-options', 'migrate_products_settings_page');

}

add_action('admin_menu', 'yt_playlist_gallery');



function register_yt_playlist_gallery_settings() {
    //register our settings
    register_setting( 'yt-playlist-gallery-settings-group', 'background' );
    register_setting( 'yt-playlist-gallery-settings-group', 'width' );
    register_setting( 'yt-playlist-gallery-settings-group', 'height' );
    register_setting( 'yt-playlist-gallery-settings-group', 'yt_limit' );
}

function migrate_products_settings_page() {
    ?>

<div id="semiTransparenDiv">  
  
  <div id='loader' style="display: none">
  <h1>Cooking in progress..</h1>
<div id="cooking">
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
<div class="bubble"></div>
  <div id="area">
      <div id="sides">
      <div id="pan"></div>
      <div id="handle"></div>
      </div>
      <div id="pancake">
      <div id="pastry"></div>
      </div>
  </div>
</div>

</div>
</div>

    <div class="wrap">
    
        <h1>Migrate Products by Srki</h1>
        <p><a href="https://github.com/srkis" target="_blank">Migrate products demo plugin on GitHub</a> </p>
        <p>Wordpress plugin for migration products from remote server into woocommerce plugin</p>

        <h2>Usage: </h2>
        <h4>Current Width: &nbsp; </h4>
        <h4>Current Height: &nbsp; </h4>
        <h4>Current Video Limit: &nbsp; </h4>

        <form id="zepter_form" method="post" action="">
            <?php settings_fields( 'yt-playlist-gallery-settings-group' ); ?>
            <?php do_settings_sections( 'yt-playlist-gallery-settings-group' ); ?>
            <table class="form-table">
                <h2>You must insert parameters before start migration </h2>
                <tr valign="top">
                    <th scope="row">Migrate all products</th>
                    <td><input type="text" id="migrateProducts" name="migrateProducts"  /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Limit</th>
                    <td><input type="text" name="limit" id="limit"  /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Migrate single product</th>
                    <td><input type="text" name="migrateSingle" id="migrateSingle" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Product ID:</th>
                    <td><input type="text" name="prod_id" id="prod_id"  /></td>
                </tr>

            </table>
            <?php submit_button( 'Start migration', 'primary', 'save', '' ) ?>

        </form>

        <br>
        <div id="success" style="color:green;font-weight: bold"></div>
        <div id="error" style="color:red;font-weight: bold"></div>
    </div>

  

<?php } ?>
