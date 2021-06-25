

jQuery(document).ready( function () {

    jQuery('#save').click(function(e){
    e.preventDefault();

      let migrateProducts = jQuery("#migrateProducts").val();
      let limit = jQuery("#limit").val();
      let migrateSingle = jQuery("#migrateSingle").val();
      let prod_id = jQuery("#prod_id").val();

      let params = {};

      if(migrateProducts != '' && migrateProducts != 'yes'){
          jQuery("#error").html('Migrate products field must be \'yes\'');
          return;
      }

        if(limit != '' && limit != 'no'){
            jQuery("#error").html('Limit field must be \'no\'');
            return;
        }

        if(migrateProducts != '' && migrateProducts == 'yes' && limit != '') {
          params.migrateProducts = migrateProducts;
          params.limit = limit;
          params.action = 'migrate_all';

          ajaxCall(params);
          return;

      }else{
          jQuery("#error").html('Migrate all products and limit must NOT be empty!');
          return;
      }

        if(migrateSingle != '' && migrateSingle == 'yes' && prod_id != '') {
            params.migrateSingle = migrateSingle;
            params.prod_id = prod_id;
            params.action = 'myaction';
            ajaxCall(params);
        }else{
            jQuery("#error").html('Migrate single products and product ID must NOT be empty!');
            return;
        }

    });


    function checkBeforeRequest(params) {
        if ("migrateProducts" in params && "migrateSingle" in params && "prod_id" in params && "limit" in params) {
            return false;
        }
    }

    function ajaxCall(params) {

       // document.getElementById("semiTransparenDiv").style.display = "block";
        jQuery("#semiTransparenDiv").css("display", "block");
        jQuery("#loader").css("display", "");

          //Some event will trigger the ajax call, you can push whatever data to the server, simply passing it to the "data" object in ajax call
        jQuery.ajax({
         url: getBaseURL()+"/wp-admin/admin-ajax.php", 
          type: 'POST',
          data:{
            action: 'myaction', // this is the function in your functions.php that will be triggered
            migrateProducts: 'yes',
            limit: 'no'
          },
          success: function( data ){
            jQuery("#semiTransparenDiv").css("display", "none");
        jQuery("#loader").css("display", "none");
            console.log( data );
          }
        });
 
    }



    function getBaseURL() {
    var url = location.href;
    var baseURL = url.substring(0, url.indexOf('/', 14));

    if (baseURL.indexOf('http://localhost') != -1) {

        var url = location.href;
        var pathname = location.pathname;
        var index1 = url.indexOf(pathname);
        var index2 = url.indexOf("/", index1 + 1);
        var baseLocalUrl = url.substr(0, index2);

        return baseLocalUrl + "/";
    }
    else {

        return baseURL + "/";
      }

    }



    });