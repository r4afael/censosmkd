<?php
header('Access-Control-Allow-Origin: *');
error_reporting(0);
	$dominio = $_SERVER["HTTP_HOST"];

	$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );

    global $valores;
session_start();



//error_reporting(E_ALL & ~( E_STRICT | E_NOTICE ));



//error_reporting(E_ALL);

//ini_set('display_errors', '1');



// tiempo de sesion

//define('vigencia', 12000);

define('distancia', 0.000000001);

define ( 'DS', DIRECTORY_SEPARATOR);

define ( 'DIR_ROOT', dirname(__FILE__) . DS );







// aqui registrar los dominios que van a tener temas diferentes

$tema = 'default'; // <-- nombre de la carpeta dentro del folder de temas

	if ( stripos($_SERVER["HTTP_HOST"], 'localhost') === 0 ){

		$tema = 'default';

	}

//*************************************************************

define ( 'TEMA_ACTIVO', $tema );

define ( 'DIR_TEMA_ACTIVO', 'temas' . DS . TEMA_ACTIVO . DS);



ini_set("memory_limit", "900M");

set_time_limit(600);





function actualizaBaseDeDatos(){

	if (conectar()){

		// Version del sistema, si se hacen cambios en la base de datos solo necesitas cambiar la version y ejecutara el script de actualizacion

		// SISTEMA ACTUALIZADO POR: @ISRAEL CASTAÑEDA SERRANO ( vs.1.1 , vscr.1.91 ) 

		$versionSistema = '1.1';

		$versionScript = '1.91';

		// TABLA DE VERSION DEL SISTEMA

		mysql_query ( 'create table if not exists sistema(

						versionSistema varchar(20),

						versionScript varchar(20)

					   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error en la creacion de la tabla de sistema: ' . mysql_error() );

		$resultado = mysql_query ( 'select versionScript from sistema' ) or die ( 'Error en la consulta de la informacion del sistema: ' . mysql_error() );

		$datos = mysql_fetch_assoc($resultado);

			if ( $versionScript != $datos['versionScript'] ){

				// INICIO DEL SCRIPT DE ACTUALIZACION

				// PANEL DE CONTROL

				mysql_query ( 'create table if not exists panelcontrol(

								no int not null auto_increment,

								fecha datetime not null,

								usuario varchar(10) not null,

								ip varchar(50),

								ipCiudad varchar(50),

								cmd int,

								nota text,

								PRIMARY KEY (no)

								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . @mysql_error() );

				$resultado = mysql_query ( 'select * from paginas where nombre = "panelcontrol"' );

					if ( mysql_num_rows ( $resultado ) == 0 ){

						mysql_query ( 'insert into paginas( idpagina, nombre, descripcion) values(default, "panelcontrol", "" )' );

						$idpagina = mysql_insert_id();

						mysql_query ( 'insert into derechos(idpagina, nombre, descripcion)values( ' . $idpagina . ', "Consultar", "" )' );

					}

				// GRUPOS DE SUBPOLIGONOS

				mysql_query ( 'create table if not exists subPoligonos(

								idSubPoligono int not null auto_increment,

								idUsuario varchar(20) not null,

								idCompra int not null,

								fecha datetime,

								nombre varchar(20) not null,

								PRIMARY KEY (idSubPoligono)

								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				mysql_query ( 'create table if not exists subPoligonosPuntos(

								idSubPoligonoPunto int not null auto_increment,

								idSubPoligono int not null,

								latitud decimal(15, 12),

								longitud decimal(15, 12),

								PRIMARY KEY (idSubPoligonoPunto)

								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				mysql_query ( 'create table if not exists gruposSubPoligonos(

								idGrupoSubPoligono varchar(20) not null,

								descripcion varchar(100),

								PRIMARY KEY (idGrupoSubPoligono)

								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				// TABLA DE RESTRICCIONES CENSOXUSUARIO

				mysql_query ( 'create table if not exists censoxusuario(

  								idcenso INT NOT NULL,

  								idUsuario VARCHAR(20) NOT NULL,

  								INDEX `fk_censoxusuario_idcenso` (`idcenso` ASC),

  								INDEX `fk_censoxusuario_idusuario` (`idUsuario` ASC),

  								PRIMARY KEY (`idcenso`, `idUsuario`),

  								CONSTRAINT `fk_censoxusuario_idcenso`

    								FOREIGN KEY (`idcenso` )

    								REFERENCES `censos` (`idcenso` )

    									ON DELETE CASCADE

    									ON UPDATE CASCADE,

  								CONSTRAINT `fk_censoxusuario_idusuario`

    								FOREIGN KEY (`idUsuario` )

    								REFERENCES `usuario` (`idusuario` )

    									ON DELETE CASCADE

    									ON UPDATE CASCADE

								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				// TABLA DE CATALOGOACTIVIDADES

				mysql_query( 'create table if not exists catalogoActividades (

  								clase_act VARCHAR(10) NOT NULL ,

  								desc_act VARCHAR(200) NULL ,

								act_econ VARCHAR(200) NULL ,

  								tipo_act VARCHAR(200) NULL ,

  								sub_act VARCHAR(200) NULL ,

  								detalle_act VARCHAR(200) NULL ,

  								icono INT NULL ,

  								clasificacion VARCHAR(50) NULL ,

  								PRIMARY KEY ( clase_act ) 

  								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				// TABLA DE RESPUESTASABIERTAS

				mysql_query( 'create table if not exists respuestasAbiertas (

  								idpunto INT(11) NOT NULL,

  								idcenso INT(11) NOT NULL,

								idpregunta INT(11) NULL,

								respuesta VARCHAR(500),

  								PRIMARY KEY (idpunto, idcenso, idpregunta),

								CONSTRAINT fk_respuestasabierta_idcenso FOREIGN KEY (idcenso) REFERENCES censos (idcenso) ON DELETE CASCADE ON UPDATE CASCADE,

								CONSTRAINT fk_respuestasabierta_idpunto FOREIGN KEY (idpunto) REFERENCES puntos (idpunto) ON DELETE CASCADE ON UPDATE CASCADE,

								CONSTRAINT fk_respuestasabierta_idpregunta FOREIGN KEY (idpregunta) REFERENCES preguntas (idpregunta) ON DELETE CASCADE ON UPDATE CASCADE

  								) ENGINE=InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				$resultado = mysql_query ( 'select * from paginas where nombre = "Grupos SubPoligonos"' );

					if ( mysql_num_rows ( $resultado ) == 0 ){

						mysql_query ( 'insert into paginas (idpagina, nombre, descripcion) values(default, "Grupos SubPoligonos", "")' );

						$idpagina = mysql_insert_id();

						mysql_query ( 'insert into derechos (idpagina, nombre, descripcion) 

										values( ' . $idpagina . ', "Agregar", "" ),

										( ' . $idpagina . ', "Consultar", "" ),

										( ' . $idpagina . ', "Eliminar", "" ),
										
										( ' . $idpagina . ', "Ficha", "" ),

										( ' . $idpagina . ', "Modificar", "" )' );

					}

				$resultado = mysql_query ( 'select schema() base' );

				$datos = mysql_fetch_assoc( $resultado );

				$base = $datos [ 'base' ];

				$resultado = mysql_query ( 'select * from information_schema.columns 

											where table_schema = "' . $base . '" and table_name = "usuario" and column_name = "grupoSubPoligono"'  );

					if ( mysql_num_rows ( $resultado ) == 0 ){

						mysql_query ( 'alter table usuario add column grupoSubPoligono varchar(20)' );

						mysql_query ( "alter table usuario add column generador tinyint(1) default 0" );

					}

				// Busquedas en la categorias

				// usamos la variable $base porque ya tiene el schema

				$resultado = mysql_query ( 'select * from information_schema.columns 

											where table_schema = "' . $base . '" and table_name = "categorias" and column_name = "alias"' );

					if ( mysql_num_rows ( $resultado ) == 0 ){

						mysql_query ( 'alter table categorias add column alias varchar(500)' );

					}

				// Busquedas en la censoxpregunta

				// usamos la variable $base porque ya tiene el schema

				$resultado = mysql_query ( 'select * from information_schema.columns 

											where table_schema = "' . $base . '" and table_name = "censoxpregunta" and column_name = "orden"' );

					if ( mysql_num_rows ( $resultado ) == 0 ){

						mysql_query ( 'ALTER TABLE censoxpregunta ADD COLUMN orden INT default 0' );

					}

				// Busquedas en las preguntas

				// usamos la variable $base porque ya tiene el schema

				$resultado = mysql_query ( 'select * from information_schema.columns

											where table_schema = "' . $base . '" and table_name = "preguntas" and column_name = "alias"' );

					if ( mysql_num_rows ( $resultado ) == 0 ){

						mysql_query ( 'alter table preguntas add column alias varchar(500)' );

					}

				// Buscamos la columna area en compras

				// usamos la variable $base porque ya tiene el schema

				$resultado = mysql_query ( 'select * from information_schema.columns

											where table_schema = "' . $base . '" and table_name = "compra" and column_name = "area"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( 'alter table compra add column area float default 0.00' );

				$resultado = mysql_query ( 'select * from information_schema.columns

											where table_schema = "' . $base . '" and table_name = "config" and column_name = "LimiteKm2Liberar"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( 'alter table config add column LimiteKm2Liberar float default 0.00' );

				$resultado = mysql_query ( 'select * from information_schema.columns

				                            where table_schema = "' . $base . '" and table_name = "config" and column_name = "compartirCompras"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( 'alter table config add column compartirCompras tinyint(1) default 0' );

				$resultado = mysql_query ( 'select * from information_schema.columns

											where table_schema = "' . $base . '" and table_name = "censos" and column_name = "general"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( 'alter table censos add column general tinyint(1) default 0' );

				$resultado = mysql_query ( 'select * from information_schema.columns

											where table_schema = "' . $base . '" and table_name = "config" and column_name = "restringirCensos"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( 'alter table config add column restringirCensos tinyint(1) default 0' );

			// UPDATE CONFIG @ ISRAEL

			

			$resultado = mysql_query ( 'select * from information_schema.columns

											where table_schema = "' . $base . '" and table_name = "config" and column_name = "poligonoCircular"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( 'alter table config add column poligonoCircular tinyint(1) default 0' );

						mysql_query ( 'alter table config add column chat tinyint(1) default 1' );

						mysql_query ( 'alter table config add column popup tinyint(1) default 1' );

						mysql_query ( 'alter table config add column reporte tinyint(1) default 0' );

						mysql_query ( 'alter table config add column times float default 3000' );

							

						

				// UPDATES PARA LOS ALIAS DE CATEGORIAS Y PREGUNTAS

				$arrayAlias = array ( 

					// CATEGORIAS

					"update categorias set alias='siembra,mineria,cultivo' where idcategoria=1",

					"update categorias set alias='cultivo,pesca' where idcategoria=2",

					"update categorias set alias='siembra,cultivo' where idcategoria=3",

					"update categorias set alias='cultivo,bosques,cria,abejas,cultivo,bosques,elaboracion,miel' where idcategoria=4",

					"update categorias set alias='cria,ganado' where idcategoria=5",

					"update categorias set alias='servicios,campo' where idcategoria=7",

					"update categorias set alias='pozos,petroleros,gaseras' where idcategoria=9",

					"update categorias set alias='pozos,petroleros' where idcategoria=10",

					"update categorias set alias='minerales' where idcategoria=11",

					"update categorias set alias='luz,agua,luz,alcantarillado' where idcategoria=17",

					"update categorias set alias='fabricas,alimentos,produccion,alimentos' where idcategoria=23",

					"update categorias set alias='alimentos,enlatados' where idcategoria=24",

					"update categorias set alias='refresqueras' where idcategoria=26",

					"update categorias set alias='vinaterias,vinos,licores' where idcategoria=27",

					"update categorias set alias='fabrica,botanas,dulces,botanas' where idcategoria=28",

					"update categorias set alias='aguas' where idcategoria=30",

					"update categorias set alias='carne,mariscos,embutidos' where idcategoria=31",

					"update categorias set alias='cigarros' where idcategoria=32",

					"update categorias set alias='tiendas,ropa,zapaterias,boutiques,zapaterias' where idcategoria=34",

					"update categorias set alias='productos,cosmetologicos,medicinas,productos,limpieza,detergentes,suavizantes' where idcategoria=38",

					"update categorias set alias='vasos,vajillas,ventanas' where idcategoria=40",

					"update categorias set alias='materiales,construccion' where idcategoria=41",

					"update categorias set alias='industria,metalurgica' where idcategoria=42",

					"update categorias set alias='office,depot,office,max,elektra,viana,coppel,famsa,liverpool,palacio,hierro,sears' where idcategoria=45",

					"update categorias set alias='mudanzas,renta,carros' where idcategoria=46",

					"update categorias set alias='costco,city,club' where idcategoria=51",

					"update categorias set alias='costco,city,club' where idcategoria=52",

					"update categorias set alias='costco,city,club' where idcategoria=53",

					"update categorias set alias='costco,city,club' where idcategoria=54",

					"update categorias set alias='chatarra' where idcategoria=55",

					"update categorias set alias='abarrotes,tiendas,conveniencia' where idcategoria=57",

					"update categorias set alias='detallistas,abarrotes,tiendas,conveniencia,oxxo,7,eleven' where idcategoria=58",

					"update categorias set alias='abarrotes,tiendas,conveniencia' where idcategoria=59",

					"update categorias set alias='abarrotes,tiendas,conveniencia' where idcategoria=60",

					"update categorias set alias='renta,autos,hosteleria,central,camionera,hoteles' where idcategoria=62",

					"update categorias set alias='renta,autos,aerolineas' where idcategoria=63",

					"update categorias set alias='cruceros,ferrocarriles,ferrys' where idcategoria=64",

					"update categorias set alias='mudanza,paqueteria' where idcategoria=67",

					"update categorias set alias='sitios,taxis' where idcategoria=68",

					"update categorias set alias='casas,empeño' where idcategoria=70",

					"update categorias set alias='soluciones,informaticas' where idcategoria=73",

					"update categorias set alias='radio,television,internet' where idcategoria=74",

					"update categorias set alias='video,casas,productoras' where idcategoria=75",

					"update categorias set alias='radiodifusoras,television,internet' where idcategoria=78",

					"update categorias set alias='universidades' where idcategoria=81",

					"update categorias set alias='inmobiliarias,bienes,raices' where idcategoria=83",

					"update categorias set alias='bienes,raices' where idcategoria=84",

					"update categorias set alias='salones,fiestas' where idcategoria=85",

					"update categorias set alias='marketing,agencias,mercadotecnia' where idcategoria=89",

					"update categorias set alias='hospitales,animales' where idcategoria=91",

					"update categorias set alias='kinder,preescolar,maternal,guarderias' where idcategoria=94",

					"update categorias set alias='secundarias' where idcategoria=95",

					"update categorias set alias='clinicas,laboratorios,medicos,consultorios' where idcategoria=99",

					"update categorias set alias='especialidades' where idcategoria=100",

					"update categorias set alias='seguros,medicos' where idcategoria=102",

					"update categorias set alias='galerias,museos' where idcategoria=105",

					"update categorias set alias='maquinitas' where idcategoria=106",

					"update categorias set alias='club' where idcategoria=107",

					"update categorias set alias='moteles,casas,huespedes,bed,breakfast' where idcategoria=109",

					"update categorias set alias='banquetes' where idcategoria=110",

					"update categorias set alias='palacio,municipal,delegacion' where idcategoria=116",

					"update categorias set alias='fondas,bares' where idcategoria=127",

					// PREGUNTAS

					"update preguntas set alias='siembra' where idpregunta=1",

					"update preguntas set alias='forraje' where idpregunta=2",

					"update preguntas set alias='jitomate,tomate' where idpregunta=3",

					"update preguntas set alias='limon' where idpregunta=4",

					"update preguntas set alias='cafetales' where idpregunta=5",

					"update preguntas set alias='plataneros' where idpregunta=6",

					"update preguntas set alias='aguacate' where idpregunta=7",

					"update preguntas set alias='frutas,frutos,secos' where idpregunta=8",

					"update preguntas set alias='productos,organicos' where idpregunta=9",

					"update preguntas set alias='invernadero,flores' where idpregunta=10",

					"update preguntas set alias='invernaderos,viveros' where idpregunta=12",

					"update preguntas set alias='jardineria' where idpregunta=13",

					"update preguntas set alias='jardineria' where idpregunta=14",

					"update preguntas set alias='vacas' where idpregunta=15",

					"update preguntas set alias='puerco' where idpregunta=16",

					"update preguntas set alias='gallinas' where idpregunta=17",

					"update preguntas set alias='pollos' where idpregunta=18",

					"update preguntas set alias='pavos' where idpregunta=19",

					"update preguntas set alias='huevo' where idpregunta=20",

					"update preguntas set alias='ovejas' where idpregunta=21",

					"update preguntas set alias='camaron' where idpregunta=22",

					"update preguntas set alias='peces,granja' where idpregunta=23",

					"update preguntas set alias='abejas' where idpregunta=24",

					"update preguntas set alias='madera' where idpregunta=25",

					"update preguntas set alias='madera' where idpregunta=26",

					"update preguntas set alias='aserradero' where idpregunta=27",

					"update preguntas set alias='camarones' where idpregunta=28",

					"update preguntas set alias='atun' where idpregunta=29",

					"update preguntas set alias='sardinas,anchoas' where idpregunta=30",

					"update preguntas set alias='mariscos' where idpregunta=31",

					"update preguntas set alias='fumigacion,del,campo' where idpregunta=32",

					"update preguntas set alias='ganaderia' where idpregunta=36",

					"update preguntas set alias='pozos,petroleros' where idpregunta=38",

					"update preguntas set alias='mineral,carbon' where idpregunta=39",

					"update preguntas set alias='mineral,hierro' where idpregunta=40",

					"update preguntas set alias='mineral,oro' where idpregunta=41",

					"update preguntas set alias='mineral,plata' where idpregunta=42",

					"update preguntas set alias='mineral,cobre' where idpregunta=43",

					"update preguntas set alias='mineral,plomo,zinc' where idpregunta=44",

					"update preguntas set alias='mineral,manganeso' where idpregunta=45",

					"update preguntas set alias='mineral,mercurio,antimonio' where idpregunta=46",

					"update preguntas set alias='mineral' where idpregunta=47",

					"update preguntas set alias='mineral,piedra,caliza' where idpregunta=48",

					"update preguntas set alias='piedra,marmol' where idpregunta=49",

					"update preguntas set alias='mineral' where idpregunta=50",

					"update preguntas set alias='agregados,arena,grava' where idpregunta=51",

					"update preguntas set alias='mineral,tezontle,tepetate' where idpregunta=52",

					"update preguntas set alias='mineral,feldespato' where idpregunta=53",

					"update preguntas set alias='mineral,silice' where idpregunta=54",

					"update preguntas set alias='mineral,caolin' where idpregunta=55",

					"update preguntas set alias='mineral,arcillas' where idpregunta=56",

					"update preguntas set alias='sal' where idpregunta=57",

					"update preguntas set alias='yeso' where idpregunta=58",

					"update preguntas set alias='mineral,barita' where idpregunta=59",

					"update preguntas set alias='mineral,roca,fosforica' where idpregunta=60",

					"update preguntas set alias='mineral,fluorita' where idpregunta=61",

					"update preguntas set alias='mineral,grafito' where idpregunta=62",

					"update preguntas set alias='mineral,azufre' where idpregunta=63",

					"update preguntas set alias='mineral' where idpregunta=64",

					"update preguntas set alias='mineral' where idpregunta=65",

					"update preguntas set alias='plataforma,petrolera' where idpregunta=66",

					"update preguntas set alias='cfe' where idpregunta=68",

					"update preguntas set alias='cfe' where idpregunta=69",

					"update preguntas set alias='gaseras,gasolineras' where idpregunta=72",

					"update preguntas set alias='construccion,residencial' where idpregunta=73",

					"update preguntas set alias='construccion,residencial' where idpregunta=74",

					"update preguntas set alias='construccion,naves' where idpregunta=75",

					"update preguntas set alias='construccion' where idpregunta=76",

					"update preguntas set alias='construccion' where idpregunta=77",

					"update preguntas set alias='construccion' where idpregunta=78",

					"update preguntas set alias='construccion' where idpregunta=79",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=80",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=81",

					"update preguntas set alias='construccion' where idpregunta=82",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=83",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=84",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=85",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=86",

					"update preguntas set alias='construccion' where idpregunta=87",

					"update preguntas set alias='construccion' where idpregunta=88",

					"update preguntas set alias='urbanización' where idpregunta=90",

					"update preguntas set alias='señalamientos,viales' where idpregunta=92",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=93",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=94",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=95",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=96",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=97",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=98",

					"update preguntas set alias='construccion,infraestructura' where idpregunta=99",

					"update preguntas set alias='obras' where idpregunta=103",

					"update preguntas set alias='instalaciones,electricas' where idpregunta=105",

					"update preguntas set alias='instalaciones,agua,gas' where idpregunta=106",

					"update preguntas set alias='instalaciones,aire,acondicionado' where idpregunta=107",

					"update preguntas set alias='instalaciones' where idpregunta=108",

					"update preguntas set alias='albañileria' where idpregunta=109",

					"update preguntas set alias='albañileria' where idpregunta=110",

					"update preguntas set alias='pintura,recubrimiento' where idpregunta=111",

					"update preguntas set alias='recubrimientos,albañileria' where idpregunta=112",

					"update preguntas set alias='recubrimientos,albañileria' where idpregunta=113",

					"update preguntas set alias='albañileria' where idpregunta=114",

					"update preguntas set alias='albañileria' where idpregunta=115",

					"update preguntas set alias='albañileria' where idpregunta=116",

					"update preguntas set alias='albañileria' where idpregunta=117",

					"update preguntas set alias='harina,trigo' where idpregunta=120",

					"update preguntas set alias='harina,maiz' where idpregunta=121",

					"update preguntas set alias='harinas' where idpregunta=122",

					"update preguntas set alias='aceite,cocina' where idpregunta=125",

					"update preguntas set alias='cereales' where idpregunta=126",

					"update preguntas set alias='azucar' where idpregunta=127",

					"update preguntas set alias='azucar' where idpregunta=128",

					"update preguntas set alias='chocolates' where idpregunta=129",

					"update preguntas set alias='chocolates' where idpregunta=130",

					"update preguntas set alias='dulces,confites' where idpregunta=131",

					"update preguntas set alias='frutas,verduras,congeladas' where idpregunta=132",

					"update preguntas set alias='comida,congelada' where idpregunta=133",

					"update preguntas set alias='frutas,verduras,deshidratadas' where idpregunta=134",

					"update preguntas set alias='frutas,verdudas,enlatadas' where idpregunta=135",

					"update preguntas set alias='comida,enlatada' where idpregunta=136",

					"update preguntas set alias='leche' where idpregunta=137",

					"update preguntas set alias='leche,en,polvo' where idpregunta=138",

					"update preguntas set alias='lacteos' where idpregunta=139",

					"update preguntas set alias='paleterias' where idpregunta=140",

					"update preguntas set alias='granjas-mataderos' where idpregunta=141",

					"update preguntas set alias='empacadoras,carne,roja,blanca' where idpregunta=142",

					"update preguntas set alias='embutidos' where idpregunta=143",

					"update preguntas set alias='manteca' where idpregunta=144",

					"update preguntas set alias='panaderias' where idpregunta=146",

					"update preguntas set alias='panaderia,artesanal' where idpregunta=147",

					"update preguntas set alias='galletas,pastas' where idpregunta=148",

					"update preguntas set alias='tortillas,maiz,tortilleria' where idpregunta=149",

					"update preguntas set alias='botanas' where idpregunta=150",

					"update preguntas set alias='cafeterias' where idpregunta=151",

					"update preguntas set alias='cafeterias' where idpregunta=152",

					"update preguntas set alias='cafeterias' where idpregunta=153",

					"update preguntas set alias='cafeterias' where idpregunta=154",

					"update preguntas set alias='pastelerias' where idpregunta=157",

					"update preguntas set alias='restaurante' where idpregunta=159",

					"update preguntas set alias='restaurante' where idpregunta=160",

					"update preguntas set alias='refresqueras' where idpregunta=161",

					"update preguntas set alias='agua,embotellada' where idpregunta=162",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=164",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=165",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=166",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=167",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=168",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=169",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=170",

					"update preguntas set alias='bebidas,alcoholicas' where idpregunta=171",

					"update preguntas set alias='cigarros' where idpregunta=172",

					"update preguntas set alias='cigarros' where idpregunta=173",

					"update preguntas set alias='cigarros' where idpregunta=174",

					"update preguntas set alias='textil' where idpregunta=175",

					"update preguntas set alias='textil' where idpregunta=176",

					"update preguntas set alias='textil' where idpregunta=177",

					"update preguntas set alias='textil' where idpregunta=178",

					"update preguntas set alias='textil' where idpregunta=179",

					"update preguntas set alias='textil' where idpregunta=180",

					"update preguntas set alias='textil' where idpregunta=181",

					"update preguntas set alias='textil' where idpregunta=182",

					"update preguntas set alias='textil' where idpregunta=183",

					"update preguntas set alias='decoración,textil' where idpregunta=184",

					"update preguntas set alias='decoración,textil' where idpregunta=185",

					"update preguntas set alias='textil' where idpregunta=188",

					"update preguntas set alias='textil' where idpregunta=189",

					"update preguntas set alias='textil' where idpregunta=190",

					"update preguntas set alias='textil' where idpregunta=191",

					"update preguntas set alias='textil' where idpregunta=192",

					"update preguntas set alias='textil,calzones' where idpregunta=193",

					"update preguntas set alias='textil' where idpregunta=194",

					"update preguntas set alias='textil' where idpregunta=195",

					"update preguntas set alias='textil,pijamas' where idpregunta=196",

					"update preguntas set alias='textil' where idpregunta=197",

					"update preguntas set alias='ropa,uniformes' where idpregunta=198",

					"update preguntas set alias='textil,disfraz' where idpregunta=199",

					"update preguntas set alias='textil' where idpregunta=200",

					"update preguntas set alias='textil' where idpregunta=201",

					"update preguntas set alias='gorras' where idpregunta=202",

					"update preguntas set alias='cuero,piel' where idpregunta=204",

					"update preguntas set alias='calzado,zapatos' where idpregunta=205",

					"update preguntas set alias='calzado,zapatos' where idpregunta=206",

					"update preguntas set alias='calzado,zapatos' where idpregunta=207",

					"update preguntas set alias='calzado' where idpregunta=208",

					"update preguntas set alias='calzado' where idpregunta=209",

					"update preguntas set alias='cuero,piel' where idpregunta=211",

					"update preguntas set alias='carpinteria' where idpregunta=216",

					"update preguntas set alias='tienda,empaques' where idpregunta=217",

					"update preguntas set alias='carpinteria' where idpregunta=221",

					"update preguntas set alias='fabricas,papel' where idpregunta=223",

					"update preguntas set alias='fabricas,papel' where idpregunta=224",

					"update preguntas set alias='imprentas' where idpregunta=232",

					"update preguntas set alias='imprentas' where idpregunta=233",

					"update preguntas set alias='imprentas' where idpregunta=234",

					"update preguntas set alias='fertilizantes' where idpregunta=247",

					"update preguntas set alias='pesticidas' where idpregunta=248",

					"update preguntas set alias='farmacos' where idpregunta=249",

					"update preguntas set alias='farmacos' where idpregunta=250",

					"update preguntas set alias='pintura,recubrimiento' where idpregunta=251",

					"update preguntas set alias='adhesivos' where idpregunta=252",

					"update preguntas set alias='detergentes' where idpregunta=253",

					"update preguntas set alias='cosmeticos' where idpregunta=254",

					"update preguntas set alias='toners' where idpregunta=255",

					"update preguntas set alias='fotografia' where idpregunta=258",

					"update preguntas set alias='reciclaje' where idpregunta=259",

					"update preguntas set alias='pet' where idpregunta=266",

					"update preguntas set alias='plástico' where idpregunta=267",

					"update preguntas set alias='plástico' where idpregunta=268",

					"update preguntas set alias='plástico' where idpregunta=269",

					"update preguntas set alias='plástico' where idpregunta=270",

					"update preguntas set alias='plástico' where idpregunta=271",

					"update preguntas set alias='plástico' where idpregunta=272",

					"update preguntas set alias='vulcanizadoras' where idpregunta=274",

					"update preguntas set alias='materiales,construccion' where idpregunta=278",

					"update preguntas set alias='materiales,construccion' where idpregunta=279",

					"update preguntas set alias='materiales,construccion' where idpregunta=280",

					"update preguntas set alias='materiales,construccion' where idpregunta=281",

					"update preguntas set alias='vidrio' where idpregunta=282",

					"update preguntas set alias='vidrio' where idpregunta=283",

					"update preguntas set alias='vidrio' where idpregunta=284",

					"update preguntas set alias='vidrio' where idpregunta=285",

					"update preguntas set alias='vidrio' where idpregunta=286",

					"update preguntas set alias='vidrio' where idpregunta=287",

					"update preguntas set alias='vidrio' where idpregunta=288",

					"update preguntas set alias='cementeras' where idpregunta=289",

					"update preguntas set alias='concreteras' where idpregunta=290",

					"update preguntas set alias='cementeras' where idpregunta=291",

					"update preguntas set alias='concreteras' where idpregunta=292",

					"update preguntas set alias='yeseras' where idpregunta=295",

					"update preguntas set alias='cantera' where idpregunta=297",

					"update preguntas set alias='hierro,acero' where idpregunta=301",

					"update preguntas set alias='hierro,acero' where idpregunta=302",

					"update preguntas set alias='aluminio' where idpregunta=303",

					"update preguntas set alias='fundición,cobre' where idpregunta=304",

					"update preguntas set alias='joyas' where idpregunta=305",

					"update preguntas set alias='fundición,metales' where idpregunta=306",

					"update preguntas set alias='laminas,cobre' where idpregunta=307",

					"update preguntas set alias='laminas' where idpregunta=308",

					"update preguntas set alias='moldeo,hierro,acero' where idpregunta=309",

					"update preguntas set alias='hierro,forjado,troquelado' where idpregunta=311",

					"update preguntas set alias='herreria,rejas' where idpregunta=315",

					"update preguntas set alias='herrajes' where idpregunta=319",

					"update preguntas set alias='fabrica,equipo,textil' where idpregunta=334",

					"update preguntas set alias='fabrica,vidrio' where idpregunta=336",

					"update preguntas set alias='fabrica,aires,acondicionados' where idpregunta=340",

					"update preguntas set alias='fabrica,refrigeradores' where idpregunta=341",

					"update preguntas set alias='maquinas,troqueles' where idpregunta=342",

					"update preguntas set alias='equipos,soldar' where idpregunta=346",

					"update preguntas set alias='basculas' where idpregunta=348",

					"update preguntas set alias='telefonos' where idpregunta=351",

					"update preguntas set alias='antenas' where idpregunta=352",

					"update preguntas set alias='audio,video' where idpregunta=354",

					"update preguntas set alias='relojeria' where idpregunta=356",

					"update preguntas set alias='lamparas' where idpregunta=359",

					"update preguntas set alias='lamparas' where idpregunta=360",

					"update preguntas set alias='electrodomesticos' where idpregunta=361",

					"update preguntas set alias='electrodomesticos' where idpregunta=362",

					"update preguntas set alias='baterias' where idpregunta=365",

					"update preguntas set alias='extensiones' where idpregunta=366",

					"update preguntas set alias='enchufes' where idpregunta=367",

					"update preguntas set alias='agencias,carro' where idpregunta=370",

					"update preguntas set alias='agencias,carro' where idpregunta=371",

					"update preguntas set alias='agencias,carro' where idpregunta=372",

					"update preguntas set alias='agencias,carro' where idpregunta=373",

					"update preguntas set alias='refacciones,automotrices' where idpregunta=375",

					"update preguntas set alias='refacciones,automotrices' where idpregunta=376",

					"update preguntas set alias='refacciones,automotrices' where idpregunta=377",

					"update preguntas set alias='refacciones,automotrices' where idpregunta=378",

					"update preguntas set alias='refacciones,automotrices' where idpregunta=379",

					"update preguntas set alias='refacciones,automotrices' where idpregunta=380",

					"update preguntas set alias='trenes' where idpregunta=382",

					"update preguntas set alias='barcos' where idpregunta=383",

					"update preguntas set alias='motocicletas' where idpregunta=384",

					"update preguntas set alias='bicis' where idpregunta=385",

					"update preguntas set alias='cocinas,baños' where idpregunta=387",

					"update preguntas set alias='muebles' where idpregunta=388",

					"update preguntas set alias='muebles,oficina' where idpregunta=389",

					"update preguntas set alias='tienda,colchones' where idpregunta=390",

					"update preguntas set alias='articulos,hogar' where idpregunta=391",

					"update preguntas set alias='equipo,dental' where idpregunta=392",

					"update preguntas set alias='insumos,medicos' where idpregunta=393",

					"update preguntas set alias='opticas' where idpregunta=394",

					"update preguntas set alias='joyerias' where idpregunta=396",

					"update preguntas set alias='joyerias' where idpregunta=397",

					"update preguntas set alias='joyas,fantasia' where idpregunta=398",

					"update preguntas set alias='tienda,deportiva' where idpregunta=399",

					"update preguntas set alias='jugueterias' where idpregunta=400",

					"update preguntas set alias='papelerias' where idpregunta=401",

					"update preguntas set alias='tiendas,musica' where idpregunta=403",

					"update preguntas set alias='boneterias,merceria' where idpregunta=404",

					"update preguntas set alias='tiendas,limpieza' where idpregunta=405",

					"update preguntas set alias='funerarias' where idpregunta=407",

					"update preguntas set alias='tiendas,miscelaneas' where idpregunta=409",

					"update preguntas set alias='carnicerias' where idpregunta=410",

					"update preguntas set alias='pollerias' where idpregunta=411",

					"update preguntas set alias='pescados' where idpregunta=412",

					"update preguntas set alias='fruterias' where idpregunta=413",

					"update preguntas set alias='huevo' where idpregunta=414",

					"update preguntas set alias='lecheria' where idpregunta=415",

					"update preguntas set alias='charcuteria' where idpregunta=416",

					"update preguntas set alias='panaderia' where idpregunta=418",

					"update preguntas set alias='panaderia' where idpregunta=419",

					"update preguntas set alias='mayorista' where idpregunta=420",

					"update preguntas set alias='mayorista' where idpregunta=421",

					"update preguntas set alias='mayorista' where idpregunta=422",

					"update preguntas set alias='mayorista' where idpregunta=423",

					"update preguntas set alias='mayorista' where idpregunta=424",

					"update preguntas set alias='mayorista' where idpregunta=425",

					"update preguntas set alias='mayorista' where idpregunta=426",

					"update preguntas set alias='mayorista' where idpregunta=427",

					"update preguntas set alias='mayorista' where idpregunta=428",

					"update preguntas set alias='mayorista' where idpregunta=429",

					"update preguntas set alias='mayorista' where idpregunta=430",

					"update preguntas set alias='mayorista' where idpregunta=431",

					"update preguntas set alias='mayorista,ropa' where idpregunta=432",

					"update preguntas set alias='mayorista' where idpregunta=433",

					"update preguntas set alias='mayorista' where idpregunta=434",

					"update preguntas set alias='mayorista' where idpregunta=435",

					"update preguntas set alias='mayorista' where idpregunta=436",

					"update preguntas set alias='mayorista' where idpregunta=437",

					"update preguntas set alias='mayorista' where idpregunta=438",

					"update preguntas set alias='mayorista' where idpregunta=439",

					"update preguntas set alias='mayorista' where idpregunta=440",

					"update preguntas set alias='mayorista' where idpregunta=441",

					"update preguntas set alias='mayorista' where idpregunta=442",

					"update preguntas set alias='mayorista' where idpregunta=443",

					"update preguntas set alias='mayorista' where idpregunta=444",

					"update preguntas set alias='mayorista' where idpregunta=445",

					"update preguntas set alias='mayorista' where idpregunta=446",

					"update preguntas set alias='mayorista' where idpregunta=447",

					"update preguntas set alias='mayorista' where idpregunta=448",

					"update preguntas set alias='mayorista' where idpregunta=449",

					"update preguntas set alias='mayorista' where idpregunta=450",

					"update preguntas set alias='mayorista' where idpregunta=451",

					"update preguntas set alias='mayorista' where idpregunta=452",

					"update preguntas set alias='mayorista' where idpregunta=453",

					"update preguntas set alias='mayorista' where idpregunta=454",

					"update preguntas set alias='mayorista' where idpregunta=455",

					"update preguntas set alias='mayorista' where idpregunta=456",

					"update preguntas set alias='mayorista' where idpregunta=457",

					"update preguntas set alias='mayorista' where idpregunta=458",

					"update preguntas set alias='mayorista' where idpregunta=459",

					"update preguntas set alias='mayorista' where idpregunta=460",

					"update preguntas set alias='mayorista' where idpregunta=461",

					"update preguntas set alias='mayorista,plastico' where idpregunta=462",

					"update preguntas set alias='mayorista,basura' where idpregunta=463",

					"update preguntas set alias='mayorista' where idpregunta=464",

					"update preguntas set alias='mayorista,equipo' where idpregunta=465",

					"update preguntas set alias='mayorista' where idpregunta=466",

					"update preguntas set alias='mayorista' where idpregunta=467",

					"update preguntas set alias='mayorista' where idpregunta=468",

					"update preguntas set alias='mayorista' where idpregunta=469",

					"update preguntas set alias='mayorista' where idpregunta=470",

					"update preguntas set alias='mayorista' where idpregunta=471",

					"update preguntas set alias='mayorista' where idpregunta=472",

					"update preguntas set alias='mayorista' where idpregunta=473",

					"update preguntas set alias='mayorista' where idpregunta=474",

					"update preguntas set alias='mayorista' where idpregunta=475",

					"update preguntas set alias='carnicerias' where idpregunta=477",

					"update preguntas set alias='pollerias' where idpregunta=478",

					"update preguntas set alias='pescaderia' where idpregunta=479",

					"update preguntas set alias='verduleria' where idpregunta=480",

					"update preguntas set alias='miscelaneas' where idpregunta=484",

					"update preguntas set alias='mercado' where idpregunta=485",

					"update preguntas set alias='abarrotes' where idpregunta=486",

					"update preguntas set alias='dulceria,pasteleria' where idpregunta=487",

					"update preguntas set alias='paleteria' where idpregunta=488",

					"update preguntas set alias='abarrotes' where idpregunta=489",

					"update preguntas set alias='vinateria,licoreria' where idpregunta=490",

					"update preguntas set alias='deposito' where idpregunta=491",

					"update preguntas set alias='abarrotes' where idpregunta=492",

					"update preguntas set alias='abarrotes' where idpregunta=493",

					"update preguntas set alias='tiendas,autoservicios' where idpregunta=494",

					"update preguntas set alias='abarrotes' where idpregunta=495",

					"update preguntas set alias='almacenes' where idpregunta=496",

					"update preguntas set alias='merceria' where idpregunta=497",

					"update preguntas set alias='autoservicios' where idpregunta=498",

					"update preguntas set alias='detallista' where idpregunta=499",

					"update preguntas set alias='bebes,boutique' where idpregunta=500",

					"update preguntas set alias='bebes' where idpregunta=501",

					"update preguntas set alias='lenceria' where idpregunta=502",

					"update preguntas set alias='tienda,disfraces' where idpregunta=503",

					"update preguntas set alias='detallista' where idpregunta=504",

					"update preguntas set alias='detallista,articulos,piel' where idpregunta=505",

					"update preguntas set alias='bebes' where idpregunta=506",

					"update preguntas set alias='tienda,sombreros' where idpregunta=507",

					"update preguntas set alias='zapateria' where idpregunta=508",

					"update preguntas set alias='farmacias' where idpregunta=509",

					"update preguntas set alias='farmacias' where idpregunta=510",

					"update preguntas set alias='farmacias,homeopaticas' where idpregunta=511",

					"update preguntas set alias='optica' where idpregunta=512",

					"update preguntas set alias='farmacias,ortopedicas' where idpregunta=513",

					"update preguntas set alias='estetica' where idpregunta=514",

					"update preguntas set alias='joyerias,relojeria' where idpregunta=515",

					"update preguntas set alias='tienda,cds' where idpregunta=516",

					"update preguntas set alias='jugueterias' where idpregunta=517",

					"update preguntas set alias='jugueterias,tiendas,deportivas' where idpregunta=518",

					"update preguntas set alias='tiendas,fotografia' where idpregunta=519",

					"update preguntas set alias='tiendas,deportivas' where idpregunta=520",

					"update preguntas set alias='tiendas,musica' where idpregunta=521",

					"update preguntas set alias='pepeleria' where idpregunta=522",

					"update preguntas set alias='librería' where idpregunta=523",

					"update preguntas set alias='revisteria' where idpregunta=524",

					"update preguntas set alias='tienda,mascotas' where idpregunta=525",

					"update preguntas set alias='merceria,papeleria' where idpregunta=526",

					"update preguntas set alias='articulos,religiosos' where idpregunta=527",

					"update preguntas set alias='detallista' where idpregunta=528",

					"update preguntas set alias='detallista' where idpregunta=529",

					"update preguntas set alias='muebleria' where idpregunta=530",

					"update preguntas set alias='mobiliario,exteriores' where idpregunta=532",

					"update preguntas set alias='autoservicios' where idpregunta=533",

					"update preguntas set alias='tienda,equipos,oficina,computo,office,depot' where idpregunta=534",

					"update preguntas set alias='tienda,electronica,radio,shack' where idpregunta=535",

					"update preguntas set alias='tapiceria' where idpregunta=536",

					"update preguntas set alias='floreria' where idpregunta=537",

					"update preguntas set alias='galerias' where idpregunta=538",

					"update preguntas set alias='tiendas,lamparas' where idpregunta=539",

					"update preguntas set alias='decoracion,interiores' where idpregunta=540",

					"update preguntas set alias='articulos,usados,articulos,2da,mano' where idpregunta=541",

					"update preguntas set alias='ferretera' where idpregunta=542",

					"update preguntas set alias='ceramiqueros,acabados' where idpregunta=543",

					"update preguntas set alias='tienda,pinturas' where idpregunta=544",

					"update preguntas set alias='articulos,limpieza' where idpregunta=546",

					"update preguntas set alias='materiales,construccion' where idpregunta=548",

					"update preguntas set alias='lotes,autos' where idpregunta=549",

					"update preguntas set alias='lotes,autos' where idpregunta=550",

					"update preguntas set alias='refaccionarias' where idpregunta=551",

					"update preguntas set alias='refacciones,usadas' where idpregunta=552",

					"update preguntas set alias='vulcanizadoras' where idpregunta=553",

					"update preguntas set alias='lotes,autos' where idpregunta=554",

					"update preguntas set alias='lotes,autos' where idpregunta=555",

					"update preguntas set alias='gasolinerias' where idpregunta=556",

					"update preguntas set alias='gasolinerias' where idpregunta=557",

					"update preguntas set alias='gasolinerias' where idpregunta=558",

					"update preguntas set alias='gasolinerias' where idpregunta=559",

					"update preguntas set alias='gasolinerias' where idpregunta=560",

					"update preguntas set alias='venta,en,linea,o,por,catalogo' where idpregunta=561",

					"update preguntas set alias='aerolineas,nales' where idpregunta=562",

					"update preguntas set alias='aerolineas,extranjeras' where idpregunta=563",

					"update preguntas set alias='aerolinea,privada' where idpregunta=564",

					"update preguntas set alias='tren' where idpregunta=565",

					"update preguntas set alias='transporte,escolar' where idpregunta=566",

					"update preguntas set alias='autobuses' where idpregunta=567",

					"update preguntas set alias='fletes' where idpregunta=575",

					"update preguntas set alias='mudanzas' where idpregunta=576",

					"update preguntas set alias='fletes' where idpregunta=577",

					"update preguntas set alias='transporte,especializado' where idpregunta=578",

					"update preguntas set alias='transporte,refrigerado' where idpregunta=579",

					"update preguntas set alias='metro' where idpregunta=587",

					"update preguntas set alias='taxis' where idpregunta=589",

					"update preguntas set alias='renta,autos' where idpregunta=590",

					"update preguntas set alias='autobuses' where idpregunta=591",

					"update preguntas set alias='autos' where idpregunta=592",

					"update preguntas set alias='trolebuses' where idpregunta=593",

					"update preguntas set alias='transporte,colectivo' where idpregunta=594",

					"update preguntas set alias='tours,terrestres' where idpregunta=596",

					"update preguntas set alias='tours,maritimos' where idpregunta=597",

					"update preguntas set alias='tours' where idpregunta=598",

					"update preguntas set alias='gruas' where idpregunta=607",

					"update preguntas set alias='central,autobuses' where idpregunta=608",

					"update preguntas set alias='afianzadoras' where idpregunta=611",

					"update preguntas set alias='seguros' where idpregunta=612",

					"update preguntas set alias='basculas' where idpregunta=613",

					"update preguntas set alias='aduana' where idpregunta=614",

					"update preguntas set alias='correos' where idpregunta=617",

					"update preguntas set alias='mensajeria,paqueteria' where idpregunta=618",

					"update preguntas set alias='mensajeria,paqueteria' where idpregunta=619",

					"update preguntas set alias='bodegas' where idpregunta=620",

					"update preguntas set alias='bodegas' where idpregunta=621",

					"update preguntas set alias='bodegas,refrigeradas' where idpregunta=622",

					"update preguntas set alias='bodegas' where idpregunta=623",

					"update preguntas set alias='bodegas,especializadas' where idpregunta=624",

					"update preguntas set alias='casa,editora' where idpregunta=625",

					"update preguntas set alias='casa,editora' where idpregunta=626",

					"update preguntas set alias='casa,editora' where idpregunta=627",

					"update preguntas set alias='casa,editora' where idpregunta=628",

					"update preguntas set alias='casa,editora' where idpregunta=629",

					"update preguntas set alias='casa,editora' where idpregunta=630",

					"update preguntas set alias='casa,editora' where idpregunta=631",

					"update preguntas set alias='casa,editora' where idpregunta=632",

					"update preguntas set alias='casa,editora' where idpregunta=633",

					"update preguntas set alias='casa,editora' where idpregunta=634",

					"update preguntas set alias='casa,editora' where idpregunta=635",

					"update preguntas set alias='casa,productora' where idpregunta=636",

					"update preguntas set alias='casa,productora' where idpregunta=637",

					"update preguntas set alias='casa,productora' where idpregunta=638",

					"update preguntas set alias='casa,distribuidora' where idpregunta=639",

					"update preguntas set alias='cines' where idpregunta=640",

					"update preguntas set alias='casa,productora' where idpregunta=641",

					"update preguntas set alias='casa,discografica' where idpregunta=642",

					"update preguntas set alias='casa,discografica' where idpregunta=643",

					"update preguntas set alias='casa,discografica' where idpregunta=644",

					"update preguntas set alias='casa,discografica' where idpregunta=645",

					"update preguntas set alias='casa,discografica' where idpregunta=646",

					"update preguntas set alias='radiodifusoras' where idpregunta=647",

					"update preguntas set alias='televisoras' where idpregunta=648",

					"update preguntas set alias='televisoras' where idpregunta=649",

					"update preguntas set alias='seguros' where idpregunta=654",

					"update preguntas set alias='central,reservaciones' where idpregunta=658",

					"update preguntas set alias='agencias,noticias' where idpregunta=659",

					"update preguntas set alias='bibliotecas' where idpregunta=660",

					"update preguntas set alias='bibliotecas' where idpregunta=661",

					"update preguntas set alias='cajas,ahoro' where idpregunta=669",

					"update preguntas set alias='arrendadoras' where idpregunta=671",

					"update preguntas set alias='casas,empeño' where idpregunta=676",

					"update preguntas set alias='casas,bolsa' where idpregunta=677",

					"update preguntas set alias='casas,cambio' where idpregunta=678",

					"update preguntas set alias='centros,cambiarios' where idpregunta=679",

					"update preguntas set alias='bolsa,valores' where idpregunta=680",

					"update preguntas set alias='inmobiliarias' where idpregunta=684",

					"update preguntas set alias='inmobiliarias' where idpregunta=685",

					"update preguntas set alias='inmobiliarias' where idpregunta=686",

					"update preguntas set alias='inmobiliarias' where idpregunta=687",

					"update preguntas set alias='inmobiliarias' where idpregunta=688",

					"update preguntas set alias='inmobiliarias' where idpregunta=689",

					"update preguntas set alias='inmobiliarias' where idpregunta=690",

					"update preguntas set alias='inmobiliarias' where idpregunta=691",

					"update preguntas set alias='inmobiliarias' where idpregunta=692",

					"update preguntas set alias='renta,autos' where idpregunta=693",

					"update preguntas set alias='renta,autos' where idpregunta=694",

					"update preguntas set alias='renta,autos' where idpregunta=695",

					"update preguntas set alias='banqueteras' where idpregunta=699",

					"update preguntas set alias='abogados' where idpregunta=710",

					"update preguntas set alias='notarios' where idpregunta=711",

					"update preguntas set alias='gestores' where idpregunta=712",

					"update preguntas set alias='contadores' where idpregunta=713",

					"update preguntas set alias='contadores' where idpregunta=714",

					"update preguntas set alias='arquitectos' where idpregunta=715",

					"update preguntas set alias='arquitectos' where idpregunta=716",

					"update preguntas set alias='ingenieros' where idpregunta=717",

					"update preguntas set alias='dibujantes' where idpregunta=718",

					"update preguntas set alias='proteccion,civil' where idpregunta=719",

					"update preguntas set alias='diseño,interiores' where idpregunta=723",

					"update preguntas set alias='agencias,publicidad' where idpregunta=725",

					"update preguntas set alias='consultorias' where idpregunta=728",

					"update preguntas set alias='consultorias' where idpregunta=729",

					"update preguntas set alias='consultorias' where idpregunta=730",

					"update preguntas set alias='investigacion' where idpregunta=731",

					"update preguntas set alias='investigacion' where idpregunta=732",

					"update preguntas set alias='investigacion' where idpregunta=733",

					"update preguntas set alias='investigacion' where idpregunta=734",

					"update preguntas set alias='agencias,publicidad' where idpregunta=735",

					"update preguntas set alias='agencias,rrpp' where idpregunta=736",

					"update preguntas set alias='agencias,publicidad' where idpregunta=739",

					"update preguntas set alias='rotulos' where idpregunta=742",

					"update preguntas set alias='foto,video' where idpregunta=743",

					"update preguntas set alias='traduccion' where idpregunta=744",

					"update preguntas set alias='encuestadora' where idpregunta=746",

					"update preguntas set alias='veterinarios' where idpregunta=747",

					"update preguntas set alias='veterinarios' where idpregunta=748",

					"update preguntas set alias='veterinarios' where idpregunta=749",

					"update preguntas set alias='veterinarios' where idpregunta=750",

					"update preguntas set alias='bolsas,trabajo' where idpregunta=755",

					"update preguntas set alias='bolsas,trabajo' where idpregunta=756",

					"update preguntas set alias='bolsas,trabajo' where idpregunta=757",

					"update preguntas set alias='tmk' where idpregunta=760",

					"update preguntas set alias='copias' where idpregunta=761",

					"update preguntas set alias='internet' where idpregunta=762",

					"update preguntas set alias='agencias,viajes' where idpregunta=766",

					"update preguntas set alias='secundaria,publica' where idpregunta=767",

					"update preguntas set alias='escuela,tecnica' where idpregunta=768",

					"update preguntas set alias='escuela,tecnica' where idpregunta=769",

					"update preguntas set alias='tours' where idpregunta=770",

					"update preguntas set alias='reservaciones' where idpregunta=771",

					"update preguntas set alias='fumigacion' where idpregunta=774",

					"update preguntas set alias='limpieza,muebles' where idpregunta=775",

					"update preguntas set alias='administradora,propiedades' where idpregunta=776",

					"update preguntas set alias='tapiceria' where idpregunta=777",

					"update preguntas set alias='limpieza' where idpregunta=778",

					"update preguntas set alias='organización,eventos' where idpregunta=780",

					"update preguntas set alias='preescolar' where idpregunta=784",

					"update preguntas set alias='preescolar' where idpregunta=785",

					"update preguntas set alias='primaria' where idpregunta=786",

					"update preguntas set alias='primaria' where idpregunta=787",

					"update preguntas set alias='secundaria' where idpregunta=788",

					"update preguntas set alias='secundaria' where idpregunta=789",

					"update preguntas set alias='secundaria' where idpregunta=790",

					"update preguntas set alias='preparatoria' where idpregunta=791",

					"update preguntas set alias='preparatoria' where idpregunta=792",

					"update preguntas set alias='escuelas' where idpregunta=793",

					"update preguntas set alias='escuelas' where idpregunta=794",

					"update preguntas set alias='escuelas' where idpregunta=795",

					"update preguntas set alias='escuelas' where idpregunta=796",

					"update preguntas set alias='universidades' where idpregunta=797",

					"update preguntas set alias='universidades' where idpregunta=798",

					"update preguntas set alias='universidades' where idpregunta=799",

					"update preguntas set alias='universidades' where idpregunta=800",

					"update preguntas set alias='escuela,comercial' where idpregunta=801",

					"update preguntas set alias='escuela,comercial' where idpregunta=802",

					"update preguntas set alias='escuelas' where idpregunta=803",

					"update preguntas set alias='escuelas' where idpregunta=804",

					"update preguntas set alias='escuelas' where idpregunta=805",

					"update preguntas set alias='escuelas' where idpregunta=806",

					"update preguntas set alias='escuelas' where idpregunta=807",

					"update preguntas set alias='escuelas' where idpregunta=808",

					"update preguntas set alias='escuelas' where idpregunta=809",

					"update preguntas set alias='escuelas' where idpregunta=810",

					"update preguntas set alias='escuelas' where idpregunta=811",

					"update preguntas set alias='escuelas' where idpregunta=812",

					"update preguntas set alias='escuelas' where idpregunta=813",

					"update preguntas set alias='escuelas' where idpregunta=814",

					"update preguntas set alias='profesores' where idpregunta=815",

					"update preguntas set alias='escuelas' where idpregunta=816",

					"update preguntas set alias='escuelas' where idpregunta=817",

					"update preguntas set alias='consultorio,medico' where idpregunta=819",

					"update preguntas set alias='consultorio,medico' where idpregunta=820",

					"update preguntas set alias='consultorio,medico' where idpregunta=821",

					"update preguntas set alias='consultorio,medico' where idpregunta=822",

					"update preguntas set alias='dentista,ortodoncia' where idpregunta=823",

					"update preguntas set alias='dentista,ortodoncia' where idpregunta=824",

					"update preguntas set alias='quiropracticos' where idpregunta=825",

					"update preguntas set alias='quiropracticos' where idpregunta=826",

					"update preguntas set alias='oculista,oftalmologo,optica' where idpregunta=827",

					"update preguntas set alias='psicologos' where idpregunta=828",

					"update preguntas set alias='psicologos' where idpregunta=829",

					"update preguntas set alias='consultorio,medico' where idpregunta=830",

					"update preguntas set alias='consultorio,medico' where idpregunta=831",

					"update preguntas set alias='nutriologos' where idpregunta=832",

					"update preguntas set alias='nutriologos' where idpregunta=833",

					"update preguntas set alias='consultorio,medico' where idpregunta=834",

					"update preguntas set alias='consultorio,medico' where idpregunta=835",

					"update preguntas set alias='planificacion,familiar' where idpregunta=836",

					"update preguntas set alias='planificacion,familiar' where idpregunta=837",

					"update preguntas set alias='laboratorios,medicos' where idpregunta=838",

					"update preguntas set alias='hospitales' where idpregunta=839",

					"update preguntas set alias='hospitales' where idpregunta=840",

					"update preguntas set alias='hospitales' where idpregunta=841",

					"update preguntas set alias='hospitales' where idpregunta=842",

					"update preguntas set alias='laboratorios,medicos' where idpregunta=843",

					"update preguntas set alias='enfermeras' where idpregunta=844",

					"update preguntas set alias='ambulancias' where idpregunta=845",

					"update preguntas set alias='banco,sangre' where idpregunta=846",

					"update preguntas set alias='banco,sangre' where idpregunta=847",

					"update preguntas set alias='hospitales' where idpregunta=848",

					"update preguntas set alias='hospitales' where idpregunta=849",

					"update preguntas set alias='hospital,psiquiatrico' where idpregunta=850",

					"update preguntas set alias='hospital,psiquiatrico' where idpregunta=851",

					"update preguntas set alias='hospitales' where idpregunta=852",

					"update preguntas set alias='hospitales' where idpregunta=853",

					"update preguntas set alias='hospitales' where idpregunta=854",

					"update preguntas set alias='guarderias' where idpregunta=855",

					"update preguntas set alias='compañía,teatral' where idpregunta=856",

					"update preguntas set alias='compañía,teatral' where idpregunta=857",

					"update preguntas set alias='compañía,danza' where idpregunta=858",

					"update preguntas set alias='asilo' where idpregunta=859",

					"update preguntas set alias='asilo' where idpregunta=860",

					"update preguntas set alias='asilo' where idpregunta=861",

					"update preguntas set alias='asilo' where idpregunta=862",

					"update preguntas set alias='asilo' where idpregunta=863",

					"update preguntas set alias='asilo' where idpregunta=864",

					"update preguntas set alias='asilo' where idpregunta=865",

					"update preguntas set alias='orfanato' where idpregunta=866",

					"update preguntas set alias='orfanato' where idpregunta=867",

					"update preguntas set alias='trabajo,social' where idpregunta=868",

					"update preguntas set alias='trabajo,social' where idpregunta=869",

					"update preguntas set alias='trabajo,social' where idpregunta=870",

					"update preguntas set alias='trabajo,social' where idpregunta=871",

					"update preguntas set alias='refugios' where idpregunta=872",

					"update preguntas set alias='refugios' where idpregunta=873",

					"update preguntas set alias='emergencias' where idpregunta=874",

					"update preguntas set alias='guarderias' where idpregunta=875",

					"update preguntas set alias='aa' where idpregunta=876",

					"update preguntas set alias='trabajo,social' where idpregunta=877",

					"update preguntas set alias='trabajo,social' where idpregunta=878",

					"update preguntas set alias='comedor,comunitario' where idpregunta=879",

					"update preguntas set alias='comedor,comunitario' where idpregunta=880",

					"update preguntas set alias='emergencias' where idpregunta=881",

					"update preguntas set alias='grupos,musicales' where idpregunta=884",

					"update preguntas set alias='grupos,musicales' where idpregunta=885",

					"update preguntas set alias='shows' where idpregunta=886",

					"update preguntas set alias='shows' where idpregunta=887",

					"update preguntas set alias='deportistas' where idpregunta=888",

					"update preguntas set alias='tiendas,deportivas' where idpregunta=889",

					"update preguntas set alias='promotores' where idpregunta=890",

					"update preguntas set alias='boliches' where idpregunta=891",

					"update preguntas set alias='billares' where idpregunta=892",

					"update preguntas set alias='entretenimiento' where idpregunta=894",

					"update preguntas set alias='entretenimiento' where idpregunta=895",

					"update preguntas set alias='managers' where idpregunta=898",

					"update preguntas set alias='museos' where idpregunta=900",

					"update preguntas set alias='museos' where idpregunta=901",

					"update preguntas set alias='zoologicos' where idpregunta=903",

					"update preguntas set alias='zoologicos' where idpregunta=904",

					"update preguntas set alias='grutas' where idpregunta=905",

					"update preguntas set alias='parque,diversiones' where idpregunta=906",

					"update preguntas set alias='parque,diversiones' where idpregunta=907",

					"update preguntas set alias='parque,acuatico' where idpregunta=908",

					"update preguntas set alias='parque,acuatico' where idpregunta=909",

					"update preguntas set alias='casinos' where idpregunta=910",

					"update preguntas set alias='casinos' where idpregunta=911",

					"update preguntas set alias='locales,loteria' where idpregunta=912",

					"update preguntas set alias='casinos' where idpregunta=913",

					"update preguntas set alias='golf' where idpregunta=914",

					"update preguntas set alias='club' where idpregunta=916",

					"update preguntas set alias='club' where idpregunta=917",

					"update preguntas set alias='gimnasio' where idpregunta=918",

					"update preguntas set alias='gimnasio' where idpregunta=919",

					"update preguntas set alias='hotel' where idpregunta=920",

					"update preguntas set alias='hotel' where idpregunta=921",

					"update preguntas set alias='motel' where idpregunta=922",

					"update preguntas set alias='cabañas,villas' where idpregunta=923",

					"update preguntas set alias='albergues' where idpregunta=924",

					"update preguntas set alias='pensiones' where idpregunta=925",

					"update preguntas set alias='suites' where idpregunta=926",

					"update preguntas set alias='restaurante' where idpregunta=927",

					"update preguntas set alias='restaurante' where idpregunta=928",

					"update preguntas set alias='restaurante' where idpregunta=929",

					"update preguntas set alias='restaurante' where idpregunta=930",

					"update preguntas set alias='comedores' where idpregunta=931",

					"update preguntas set alias='comedores' where idpregunta=932",

					"update preguntas set alias='comedores' where idpregunta=933",

					"update preguntas set alias='antros' where idpregunta=934",

					"update preguntas set alias='cantinas' where idpregunta=935",

					"update preguntas set alias='taller,mecánico,reparación,autos' where idpregunta=936",

					"update preguntas set alias='taller,mecánico,electrico' where idpregunta=937",

					"update preguntas set alias='taller,mecánico' where idpregunta=938",

					"update preguntas set alias='taller,mecánico' where idpregunta=939",

					"update preguntas set alias='taller,mecánico' where idpregunta=940",

					"update preguntas set alias='taller,mecánico' where idpregunta=941",

					"update preguntas set alias='taller,mecánico' where idpregunta=942",

					"update preguntas set alias='taller,mecánico' where idpregunta=943",

					"update preguntas set alias='taller,mecánico' where idpregunta=944",

					"update preguntas set alias='taller,mecánico' where idpregunta=945",

					"update preguntas set alias='vulcanizadoras' where idpregunta=946",

					"update preguntas set alias='lavado,autos' where idpregunta=947",

					"update preguntas set alias='taller,mecánico' where idpregunta=948",

					"update preguntas set alias='tapiceria' where idpregunta=956",

					"update preguntas set alias='zapatero,zapatos' where idpregunta=957",

					"update preguntas set alias='cerrajero' where idpregunta=958",

					"update preguntas set alias='vulcanizadoras' where idpregunta=959",

					"update preguntas set alias='vulcanizadoras' where idpregunta=960",

					"update preguntas set alias='esteticas' where idpregunta=961",

					"update preguntas set alias='baños,publicos' where idpregunta=962",

					"update preguntas set alias='baños,publicos' where idpregunta=963",

					"update preguntas set alias='lavanderias' where idpregunta=964",

					"update preguntas set alias='funerarias' where idpregunta=966",

					"update preguntas set alias='panteones' where idpregunta=967",

					"update preguntas set alias='pensiones,carros' where idpregunta=968",

					"update preguntas set alias='revelado' where idpregunta=969",

				 );

				 	for ( $i = 0; $i < count($arrayAlias); $i++ )

						mysql_query ( $arrayAlias[$i] ) or die ( 'Error: ' . $arrayAlias[$i] . "\n Error MySQL: " . mysql_error() );

				// Script para agregar los datos de la tabla catalogoActividades

				$arrayCatalogoActividades = array( 

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111151', 'Cultivo De Maíz Grano', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Maíz Grano', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111152', 'Cultivo De Maíz Forrajero', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Maíz Forrajero', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111211', 'Cultivo De Jitomate O Tomate Rojo', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Jitomate O Tomate Rojo', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111321', 'Cultivo De Limón', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Limón', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111331', 'Cultivo De Café', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Café', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111332', 'Cultivo De Plátano', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Plátano', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111334', 'Cultivo De Aguacate', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Aguacate', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111339', 'Cultivo De Otros Frutales No Cítricos Y De Nueces', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Otros Frutales No Cítricos Y De Nueces', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111410', 'Cultivo De Productos Alimenticios En Invernaderos', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Agricultura', 'Cultivo De Productos Alimenticios En Invernaderos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111422', 'Floricultura En Invernadero', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Floricultura En Invernadero', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111423', 'Cultivo De Arboles De Ciclo Productivo De 10 Años O Menos', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Cultivo De Arboles De Ciclo Productivo De 10 Años O Menos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111429', 'Otros Cultivos No Alimenticios En Invernaderos Y Viveros', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Otros Cultivos No Alimenticios En Invernaderos Y Viveros', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111942', 'Cultivo De Pastos Y Zacates', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Cultivo De Pastos Y Zacates', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '111999', 'Otros Cultivos', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Otros Cultivos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112120', 'Explotación De Bovinos Para La Producción De Leche', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Bovinos Para La Producción De Leche', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112211', 'Explotación De Porcinos En Granja', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Porcinos En Granja', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112312', 'Explotación De Gallinas Para La Producción De Huevo Para Plato', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Gallinas Para La Producción De Huevo Para Plato', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112320', 'Explotación De Pollos Para La Producción De Carne', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Pollos Para La Producción De Carne', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112330', 'Explotación De Guajolotes O Pavos', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Guajolotes O Pavos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112390', 'Explotación De Otras Aves Para Producción De Carne Y Huevo', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Otras Aves Para Producción De Carne Y Huevo', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112410', 'Explotación De Ovinos', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Ganadería', 'Explotación De Ovinos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112511', 'Camaronicultura', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Pesca', 'Camaronicultura', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112512', 'Piscicultura Y Otra Acuicultura Excepto Camaronicultura', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Pesca', 'Piscicultura Y Otra Acuicultura Excepto Camaronicultura', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '112910', 'Apicultura', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Apicultura', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '113211', 'Viveros Forestales', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Viveros Forestales', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '113212', 'Recolección De Productos Forestales', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Recolección De Productos Forestales', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '113310', 'Tala De Arboles', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Tala De Arboles', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '114111', 'Pesca De Camarón', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Pesca', 'Pesca De Camarón', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '114112', 'Pesca De Tunidos', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Pesca', 'Pesca De Tunidos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '114113', 'Pesca De Sardina Y Anchoveta', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Pesca', 'Pesca De Sardina Y Anchoveta', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '114119', 'Pesca Y Captura De Peces Crustáceos Moluscos Y Otras Especies', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Pesca', 'Pesca Y Captura De Peces Crustáceos Moluscos Y Otras Especies', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '115111', 'Servicios De Fumigación Agrícola', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Servicios Agrícolas', 'Servicios De Fumigación Agrícola', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '115112', 'Despepite De Algodón', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Servicios Agrícolas', 'Despepite De Algodón', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '115113', 'Beneficio De Productos Agrícolas', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Servicios Agrícolas', 'Beneficio De Productos Agrícolas', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '115119', 'Otros Servicios Relacionados Con La Agricultura', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Servicios Agrícolas', 'Otros Servicios Relacionados Con La Agricultura', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '115210', 'Servicios Relacionados Con La Cría Y Explotación De Animales', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Servicios Agrícolas', 'Servicios Relacionados Con La Cría Y Explotación De Animales', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '115310', 'Servicios Relacionados Con El Aprovechamiento Forestal', 'Agricultura Y Minería', 'Actividad Del Campo Y Pesca', 'Silvicultura Y Apicultura', 'Servicios Relacionados Con El Aprovechamiento Forestal', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '211110', 'Extracción De Petróleo Y Gas', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Extracción De Petróleo Y Gas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212110', 'Minería De Carbón Mineral', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Carbón Mineral', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212210', 'Minería De Hierro', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Hierro', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212221', 'Minería De Oro', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Oro', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212222', 'Minería De Plata', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Plata', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212231', 'Minería De Cobre', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Cobre', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212232', 'Minería De Plomo Y Zinc', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Plomo Y Zinc', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212291', 'Minería De Manganeso', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Manganeso', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212292', 'Minería De Mercurio Y Antimonio', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Mercurio Y Antimonio', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212299', 'Minería De Otros Minerales Metálicos', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales Metálicos', 'Minería De Otros Minerales Metálicos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212311', 'Minería De Piedra Caliza', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Piedra Caliza', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212312', 'Minería De Mármol', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Mármol', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212319', 'Minería De Otras Piedras Dimensionadas', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Otras Piedras Dimensionadas', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212321', 'Minería De Arena Y Grava Para La Construcción', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Arena Y Grava Para La Construcción', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212322', 'Minería De Tezontle Y Tepetate', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Tezontle Y Tepetate', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212323', 'Minería De Feldespato', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Feldespato', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212324', 'Minería De Sílice', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Sílice', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212325', 'Minería De Caolín', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Caolín', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212329', 'Minería De Otras Arcillas Y De Otros Minerales Refractarios', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Otras Arcillas Y De Otros Minerales Refractarios', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212391', 'Minería De Sal', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Sal', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212392', 'Minería De Piedra De Yeso', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Piedra De Yeso', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212393', 'Minería De Barita', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Barita', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212394', 'Minería De Roca Fosfórica', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Roca Fosfórica', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212395', 'Minería De Fluorita', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Fluorita', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212396', 'Minería De Grafito', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Grafito', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212397', 'Minería De Azufre', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Azufre', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212398', 'Minería De Minerales No Metálicos Para Productos Químicos', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Minerales No Metálicos Para Productos Químicos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '212399', 'Minería De Otros Minerales No Metálicos', 'Agricultura Y Minería', 'Actividad Minera', 'Minerales No Metálicos', 'Minería De Otros Minerales No Metálicos', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '213111', 'Perforación De Pozos Petroleros Y De Gas', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Perforación De Pozos Petroleros Y De Gas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '213119', 'Otros Servicios Relacionados Con La Minería', 'Agricultura Y Minería', 'Actividad Minera', 'Otros Minería', 'Otros Servicios Relacionados Con La Minería', 1, 'Otros > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '221110', 'Generación Transmisión Y Distribución De Energía Eléctrica', 'Servicios', 'Gobierno Y Servicios Primarios', 'Suministro Agua Y Electricidad', 'Generación Transmisión Y Distribución De Energía Eléctrica', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '221120', 'Transmisión Y Distribución De Energía Eléctrica', 'Servicios', 'Gobierno Y Servicios Primarios', 'Suministro Agua Y Electricidad', 'Transmisión Y Distribución De Energía Eléctrica', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '222111', 'Captación Tratamiento Y Suministro De Agua Realizados Por El Sector Publico', 'Servicios', 'Gobierno Y Servicios Primarios', 'Suministro Agua Y Electricidad', 'Captación Tratamiento Y Suministro De Agua Realizados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '222112', 'Captación Tratamiento Y Suministro De Agua Realizados Por El Sector Privado', 'Servicios', 'Gobierno Y Servicios Primarios', 'Suministro Agua Y Electricidad', 'Captación Tratamiento Y Suministro De Agua Realizados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '222210', 'Suministro De Gas Por Ductos Al Consumidor Final', 'Servicios', 'Gobierno Y Servicios Primarios', 'Suministro Agua Y Electricidad', 'Suministro De Gas Por Ductos Al Consumidor Final', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236111', 'Edificación De Vivienda Unifamiliar', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Edificación De Vivienda Unifamiliar', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236112', 'Edificación De Vivienda Multifamiliar', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Edificación De Vivienda Multifamiliar', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236113', 'Supervisión De Edificación Residencial', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Supervisión De Edificación Residencial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236211', 'Edificación De Naves Y Plantas Industriales Excepto La Supervisión', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Edificación De Naves Y Plantas Industriales Excepto La Supervisión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236212', 'Supervisión De Edificación De Naves Y Plantas Industriales', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Supervisión De Edificación De Naves Y Plantas Industriales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236222', 'Supervisión De Edificación De Inmuebles Comerciales Y De Servicios', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Supervisión De Edificación De Inmuebles Comerciales Y De Servicios', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237112', 'Construcción De Sistemas De Riego Agrícola', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Sistemas De Riego Agrícola', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237121', 'Construcción De Sistemas De Distribución De Petróleo Y Gas', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Construcción De Sistemas De Distribución De Petróleo Y Gas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237122', 'Construcción De Plantas De Refinería Y Petroquímica', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Construcción De Plantas De Refinería Y Petroquímica', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '236221', 'Edificación De Inmuebles Comerciales Y De Servicios Excepto La Supervisión', 'Industria', 'Construcción E Infraestructura', 'Edificación', 'Edificación De Inmuebles Comerciales Y De Servicios Excepto La Supervisión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237111', 'Construcción De Obras Para El Tratamiento Distribución Y Suministro De Agua Y Drenaje', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Obras Para El Tratamiento Distribución Y Suministro De Agua Y Drenaje', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237113', 'Supervisión De Construcción De Obras Para El Tratamiento Distribución Y Suministro De Agua Drenaje Y Riego', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Supervisión De Construcción De Obras Para El Tratamiento Distribución Y Suministro De Agua Drenaje Y Riego', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237123', 'Supervisión De Construcción De Obras Para Petróleo Y Gas', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Supervisión De Construcción De Obras Para Petróleo Y Gas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237131', 'Construcción De Obras De Generación Y Conducción De Energía Eléctrica', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Obras De Generación Y Conducción De Energía Eléctrica', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237132', 'Construcción De Obras Para Telecomunicaciones', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Obras Para Telecomunicaciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237133', 'Supervisión De Construcción De Obras De Generación Y Conducción De Energía Eléctrica Y De Obras Para Telecomunicaciones', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Supervisión De Construcción De Obras De Generación Y Conducción De Energía Eléctrica Y De Obras Para Telecomunicaciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237211', 'División De Terrenos', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'División De Terrenos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237212', 'Construcción De Obras De Urbanización', 'Industria', 'Construcción E Infraestructura', 'Urbanización', 'Construcción De Obras De Urbanización', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237213', 'Supervisión De División De Terrenos Y De Construcción De Obras De Urbanización', 'Industria', 'Construcción E Infraestructura', 'Urbanización', 'Supervisión De División De Terrenos Y De Construcción De Obras De Urbanización', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237311', 'Instalación De Señalamientos Y Protecciones En Obras Viales', 'Industria', 'Construcción E Infraestructura', 'Urbanización', 'Instalación De Señalamientos Y Protecciones En Obras Viales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237312', 'Construcción De Carreteras Puentes Y Similares', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Carreteras Puentes Y Similares', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237313', 'Supervisión De Construcción De Vías De Comunicación', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Supervisión De Construcción De Vías De Comunicación', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237991', 'Construcción De Presas Y Represas', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Presas Y Represas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237992', 'Construcción De Obras Marítimas Fluviales Y Subacuáticas', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Obras Marítimas Fluviales Y Subacuáticas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237993', 'Construcción De Obras Para Transporte Eléctrico Y Ferroviario', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Construcción De Obras Para Transporte Eléctrico Y Ferroviario', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237994', 'Supervisión De Construcción De Otras Obras De Ingeniería Civil', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Supervisión De Construcción De Otras Obras De Ingeniería Civil', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '237999', 'Otras Construcciones De Ingeniería Civil', 'Industria', 'Construcción E Infraestructura', 'Infraestructura', 'Otras Construcciones De Ingeniería Civil', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238110', 'Trabajos De Cimentaciones', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Trabajos De Cimentaciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238121', 'Montaje De Estructuras De Concreto Prefabricadas', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Montaje De Estructuras De Concreto Prefabricadas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238122', 'Montaje De Estructuras De Acero Prefabricadas', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Montaje De Estructuras De Acero Prefabricadas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238130', 'Trabajos De Albañilería', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Trabajos De Albañilería', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238190', 'Otros Trabajos En Exteriores', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Otros Trabajos En Exteriores', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238210', 'Instalaciones Eléctricas En Construcciones', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Instalaciones Eléctricas En Construcciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238221', 'Instalaciones Hidrosanitarias Y De Gas', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Instalaciones Hidrosanitarias Y De Gas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238222', 'Instalaciones De Sistemas Centrales De Aire Acondicionado Y Calefacción', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Instalaciones De Sistemas Centrales De Aire Acondicionado Y Calefacción', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238290', 'Otras Instalaciones Y Equipamiento En Construcciones', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Otras Instalaciones Y Equipamiento En Construcciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238311', 'Colocación De Muros Falsos Y Aislamiento', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Colocación De Muros Falsos Y Aislamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238312', 'Trabajos De Enyesado Empastado Y Tiroleado', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Trabajos De Enyesado Empastado Y Tiroleado', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238320', 'Trabajos De Pintura Y Otros Cubrimientos De Paredes', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Trabajos De Pintura Y Otros Cubrimientos De Paredes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238330', 'Colocación De Pisos Flexibles Y De Madera', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Colocación De Pisos Flexibles Y De Madera', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238340', 'Colocación De Pisos Cerámicos Y Azulejos', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Colocación De Pisos Cerámicos Y Azulejos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238350', 'Realización De Trabajos De Carpintería En El Lugar De La Construcción', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Realización De Trabajos De Carpintería En El Lugar De La Construcción', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238390', 'Otros Trabajos De Acabados En Edificaciones', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Otros Trabajos De Acabados En Edificaciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238910', 'Preparación De Terrenos Para La Construcción', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Preparación De Terrenos Para La Construcción', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '238990', 'Otros Trabajos Especializados Para La Construcción', 'Industria', 'Construcción E Infraestructura', 'Trabajos E Instalaciones', 'Otros Trabajos Especializados Para La Construcción', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311110', 'Elaboración De Alimentos Para Animales', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Alimentos Para Animales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311211', 'Beneficio Del Arroz', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Beneficio Del Arroz', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311212', 'Elaboración De Harina De Trigo', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Harina De Trigo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311213', 'Elaboración De Harina De Maíz', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Harina De Maíz', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311214', 'Elaboración De Harina De Otros Productos Agrícolas', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Harina De Otros Productos Agrícolas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311215', 'Elaboración De Malta', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Malta', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311221', 'Elaboración De Féculas Y Otros Almidones Y Sus Derivados', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Féculas Y Otros Almidones Y Sus Derivados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311222', 'Elaboración De Aceites Y Grasas Vegetales Comestibles', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Aceites Y Grasas Vegetales Comestibles', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311230', 'Elaboración De Cereales Para El Desayuno', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Cereales Para El Desayuno', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311311', 'Elaboración De Azúcar De Caña', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Azúcar De Caña', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311319', 'Elaboración De Otros Azucares', 'Industria', 'Elaboración De Alimentos', 'Insumos Vegetales', 'Elaboración De Otros Azucares', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311320', 'Elaboración De Chocolate Y Productos De Chocolate A Partir De Cacao', 'Industria', 'Elaboración De Alimentos', 'Dulces Y Botanas', 'Elaboración De Chocolate Y Productos De Chocolate A Partir De Cacao', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311330', 'Elaboración De Productos De Chocolate A Partir De Chocolate', 'Industria', 'Elaboración De Alimentos', 'Dulces Y Botanas', 'Elaboración De Productos De Chocolate A Partir De Chocolate', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311340', 'Elaboración De Dulces Chicles Y Productos De Confitería Que No Sean De Chocolate', 'Industria', 'Elaboración De Alimentos', 'Dulces Y Botanas', 'Elaboración De Dulces Chicles Y Productos De Confitería Que No Sean De Chocolate', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311411', 'Congelación De Frutas Y Verduras', 'Industria', 'Elaboración De Alimentos', 'Conservación De Alimentos', 'Congelación De Frutas Y Verduras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311412', 'Congelación De Alimentos Preparados', 'Industria', 'Elaboración De Alimentos', 'Conservación De Alimentos', 'Congelación De Alimentos Preparados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311421', 'Deshidratación De Frutas Y Verduras', 'Industria', 'Elaboración De Alimentos', 'Conservación De Alimentos', 'Deshidratación De Frutas Y Verduras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311422', 'Conservación De Frutas Y Verduras Por Procesos Distintos A La Congelación Y La Deshidratación', 'Industria', 'Elaboración De Alimentos', 'Conservación De Alimentos', 'Conservación De Frutas Y Verduras Por Procesos Distintos A La Congelación Y La Deshidratación', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311423', 'Conservación De Alimentos Preparados Por Procesos Distintos A La Congelación', 'Industria', 'Elaboración De Alimentos', 'Conservación De Alimentos', 'Conservación De Alimentos Preparados Por Procesos Distintos A La Congelación', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311511', 'Elaboración De Leche Liquida', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Leche Liquida', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311512', 'Elaboración De Leche En Polvo Condensada Y Evaporada', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Leche En Polvo Condensada Y Evaporada', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311513', 'Elaboración De Derivados Y Fermentos Lácteos', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Derivados Y Fermentos Lácteos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311520', 'Elaboración De Helados Y Paletas', 'Industria', 'Elaboración De Alimentos', 'Dulces Y Botanas', 'Elaboración De Helados Y Paletas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311611', 'Matanza De Ganado Aves Y Otros Animales Comestibles', 'Industria', 'Elaboración De Alimentos', 'Insumos Animales', 'Matanza De Ganado Aves Y Otros Animales Comestibles', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311612', 'Corte Y Empacado De Carne De Ganado Aves Y Otros Animales Comestibles', 'Industria', 'Elaboración De Alimentos', 'Insumos Animales', 'Corte Y Empacado De Carne De Ganado Aves Y Otros Animales Comestibles', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311613', 'Preparación De Embutidos Y Otras Conservas De Carne De Ganado Aves Y Otros Animales Comestibles', 'Industria', 'Elaboración De Alimentos', 'Insumos Animales', 'Preparación De Embutidos Y Otras Conservas De Carne De Ganado Aves Y Otros Animales Comestibles', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311614', 'Elaboración De Manteca Y Otras Grasas Animales Comestibles', 'Industria', 'Elaboración De Alimentos', 'Insumos Animales', 'Elaboración De Manteca Y Otras Grasas Animales Comestibles', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311710', 'Preparación Y Envasado De Pescados Y Mariscos', 'Industria', 'Elaboración De Alimentos', 'Insumos Animales', 'Preparación Y Envasado De Pescados Y Mariscos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311811', 'Panificación Industrial', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Panificación Industrial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311812', 'Panificación Tradicional', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Panificación Tradicional', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311820', 'Elaboración De Galletas Y Pastas Para Sopa', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Galletas Y Pastas Para Sopa', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311830', 'Elaboración De Tortillas De Maíz Y Molienda De Nixtamal', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Tortillas De Maíz Y Molienda De Nixtamal', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311910', 'Elaboración De Botanas', 'Industria', 'Elaboración De Alimentos', 'Dulces Y Botanas', 'Elaboración De Botanas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311921', 'Beneficio Del Café', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Beneficio Del Café', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311922', 'Elaboración De Café Tostado Y Molido', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Café Tostado Y Molido', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311923', 'Elaboración De Café Instantáneo', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Café Instantáneo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311924', 'Preparación Y Envasado De Te', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Preparación Y Envasado De Te', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311930', 'Elaboración De Concentrados Polvos Jarabes Y Esencias De Sabor Para Bebidas', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Concentrados Polvos Jarabes Y Esencias De Sabor Para Bebidas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311940', 'Elaboración De Condimentos Y Aderezos', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Condimentos Y Aderezos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311991', 'Elaboración De Gelatinas Y Otros Postres En Polvo', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Gelatinas Y Otros Postres En Polvo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311992', 'Elaboración De Levadura', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Levadura', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311993', 'Elaboración De Alimentos Frescos Para Consumo Inmediato', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Alimentos Frescos Para Consumo Inmediato', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '311999', 'Elaboración De Otros Alimentos', 'Industria', 'Elaboración De Alimentos', 'Alimentos Procesados', 'Elaboración De Otros Alimentos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312111', 'Elaboración De Refrescos Y Otras Bebidas No Alcohólicas', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Refrescos Y Otras Bebidas No Alcohólicas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312112', 'Purificación Y Embotellado De Agua', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Purificación Y Embotellado De Agua', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312113', 'Elaboración De Hielo', 'Industria', 'Elaboración De Bebidas', 'No Alcohólicas', 'Elaboración De Hielo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312120', 'Elaboración De Cerveza', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Cerveza', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312131', 'Elaboración De Bebidas Alcohólicas A Base De Uva', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Bebidas Alcohólicas A Base De Uva', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312132', 'Elaboración De Pulque', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Pulque', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312139', 'Elaboración De Sidra Y Otras Bebidas Fermentadas', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Sidra Y Otras Bebidas Fermentadas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312141', 'Elaboración De Ron Y Otras Bebidas Destiladas De Caña', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Ron Y Otras Bebidas Destiladas De Caña', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312142', 'Elaboración De Bebidas Destiladas De Agave', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Bebidas Destiladas De Agave', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312143', 'Obtención De Alcohol Etílico Potable', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Obtención De Alcohol Etílico Potable', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312149', 'Elaboración De Otras Bebidas Destiladas', 'Industria', 'Elaboración De Bebidas', 'Bebidas Alcohólicas', 'Elaboración De Otras Bebidas Destiladas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312210', 'Beneficio Del Tabaco', 'Industria', 'Elaboración Productos De Tabaco', 'Otros Elaboración Productos De Tabaco', 'Beneficio Del Tabaco', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312221', 'Elaboración De Cigarros', 'Industria', 'Elaboración Productos De Tabaco', 'Otros Elaboración Productos De Tabaco', 'Elaboración De Cigarros', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '312222', 'Elaboración De Puros Y Otros Productos De Tabaco', 'Industria', 'Elaboración Productos De Tabaco', 'Otros Elaboración Productos De Tabaco', 'Elaboración De Puros Y Otros Productos De Tabaco', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313111', 'Preparación E Hilado De Fibras Duras Naturales', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Preparación E Hilado De Fibras Duras Naturales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313112', 'Preparación E Hilado De Fibras Blandas Naturales', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Preparación E Hilado De Fibras Blandas Naturales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313113', 'Fabricación De Hilos Para Coser Y Bordar', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Hilos Para Coser Y Bordar', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313210', 'Fabricación De Telas Anchas De Trama', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Telas Anchas De Trama', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313220', 'Fabricación De Telas Angostas De Trama Y Pasamanería', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Telas Angostas De Trama Y Pasamanería', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313230', 'Fabricación De Telas No Tejidas (Comprimidas)', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Telas No Tejidas (Comprimidas)', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313240', 'Fabricación De Telas De Punto', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Telas De Punto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313310', 'Acabado De Productos Textiles', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Acabado De Productos Textiles', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '313320', 'Fabricación De Telas Recubiertas', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Telas Recubiertas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314110', 'Fabricación De Alfombras Y Tapetes', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Alfombras Y Tapetes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314120', 'Confección De Cortinas Blancos Y Similares', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Cortinas Blancos Y Similares', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314911', 'Confección De Costales', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Costales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314912', 'Confección De Productos De Textiles Recubiertos Y De Materiales Sucedáneos', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Productos De Textiles Recubiertos Y De Materiales Sucedáneos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314991', 'Confección Bordado Y Deshilado De Productos Textiles', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección Bordado Y Deshilado De Productos Textiles', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314992', 'Fabricación De Redes Y Otros Productos De Cordelería', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Redes Y Otros Productos De Cordelería', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314993', 'Fabricación De Productos Textiles Reciclados', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Productos Textiles Reciclados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '314999', 'Fabricación De Banderas Y Otros Productos Textiles No Clasificados En Otra Parte', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Banderas Y Otros Productos Textiles No Clasificados En Otra Parte', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315110', 'Fabricación De Calcetines Y Medias De Punto', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Calcetines Y Medias De Punto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315191', 'Fabricación De Ropa Interior De Punto', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Ropa Interior De Punto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315192', 'Fabricación De Ropa Exterior De Punto', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Ropa Exterior De Punto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315210', 'Confección De Prendas De Vestir De Cuero Piel Y De Materiales Sucedáneos', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Prendas De Vestir De Cuero Piel Y De Materiales Sucedáneos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315221', 'Confección En Serie De Ropa Interior Y De Dormir', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección En Serie De Ropa Interior Y De Dormir', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315222', 'Confección En Serie De Camisas', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección En Serie De Camisas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315223', 'Confección En Serie De Uniformes', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección En Serie De Uniformes', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315224', 'Confección En Serie De Disfraces Y Trajes Típicos', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección En Serie De Disfraces Y Trajes Típicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315225', 'Confección De Prendas De Vestir Sobre Medida', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Prendas De Vestir Sobre Medida', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315229', 'Confección En Serie De Otra Ropa Exterior De Materiales Textiles', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección En Serie De Otra Ropa Exterior De Materiales Textiles', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315991', 'Confección De Sombreros Y Gorras', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Sombreros Y Gorras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '315999', 'Confección De Otros Accesorios Y Prendas De Vestir No Clasificados En Otra Parte', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Confección De Otros Accesorios Y Prendas De Vestir No Clasificados En Otra Parte', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316110', 'Curtido Y Acabado De Cuero Y Piel', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Curtido Y Acabado De Cuero Y Piel', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316211', 'Fabricación De Calzado Con Corte De Piel Y Cuero', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Calzado Con Corte De Piel Y Cuero', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316212', 'Fabricación De Calzado Con Corte De Tela', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Calzado Con Corte De Tela', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316213', 'Fabricación De Calzado De Plástico', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Calzado De Plástico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316214', 'Fabricación De Calzado De Hule', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Calzado De Hule', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316219', 'Fabricación De Huaraches Y Calzado De Otro Tipo De Materiales', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Huaraches Y Calzado De Otro Tipo De Materiales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316991', 'Fabricación De Bolsos De Mano Maletas Y Similares', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Bolsos De Mano Maletas Y Similares', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '316999', 'Fabricación De Otros Productos De Cuero Piel Y Materiales Sucedáneos', 'Industria', 'Insumos Primarios', 'Productos Textil Y Calzado', 'Fabricación De Otros Productos De Cuero Piel Y Materiales Sucedáneos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321111', 'Aserraderos Integrados', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Aserraderos Integrados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321112', 'Aserrado De Tablas Y Tablones', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Aserrado De Tablas Y Tablones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321113', 'Tratamiento De La Madera Y Fabricación De Postes Y Durmientes', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Tratamiento De La Madera Y Fabricación De Postes Y Durmientes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321210', 'Fabricación De Laminados Y Aglutinados De Madera', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Laminados Y Aglutinados De Madera', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321910', 'Fabricación De Productos De Madera Para La Construcción', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Productos De Madera Para La Construcción', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321920', 'Fabricación De Productos Para Embalaje Y Envases De Madera', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Productos Para Embalaje Y Envases De Madera', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321991', 'Fabricación De Productos De Materiales Trenzables Excepto Palma', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Productos De Materiales Trenzables Excepto Palma', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321992', 'Fabricación De Artículos Y Utensilios De Madera Para El Hogar', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Artículos Y Utensilios De Madera Para El Hogar', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321993', 'Fabricación De Productos De Madera De Uso Industrial', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Productos De Madera De Uso Industrial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '321999', 'Fabricación De Otros Productos De Madera', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Otros Productos De Madera', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322110', 'Fabricación De Pulpa', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Pulpa', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322121', 'Fabricación De Papel En Plantas Integradas', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Papel En Plantas Integradas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322122', 'Fabricación De Papel A Partir De Pulpa', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Papel A Partir De Pulpa', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322131', 'Fabricación De Cartón En Plantas Integradas', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Cartón En Plantas Integradas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322132', 'Fabricación De Cartón Y Cartoncillo A Partir De Pulpa', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Cartón Y Cartoncillo A Partir De Pulpa', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322210', 'Fabricación De Envases De Cartón', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Envases De Cartón', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322220', 'Fabricación De Bolsas De Papel Y Productos Celulósicos Recubiertos Y Tratados', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Bolsas De Papel Y Productos Celulósicos Recubiertos Y Tratados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322230', 'Fabricación De Productos De Papelería', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Productos De Papelería', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322291', 'Fabricación De Pañales Desechables Y Productos Sanitarios', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Pañales Desechables Y Productos Sanitarios', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '322299', 'Fabricación De Otros Productos De Cartón Y Papel', 'Industria', 'Insumos Primarios', 'Productos De Madera Y Papel', 'Fabricación De Otros Productos De Cartón Y Papel', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '323111', 'Impresión De Libros Periódicos Y Revistas', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Impresión De Libros Periódicos Y Revistas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '323119', 'Impresión De Formas Continuas Y Otros Impresos', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Impresión De Formas Continuas Y Otros Impresos', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '323120', 'Industrias Conexas A La Impresión', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Industrias Conexas A La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '324110', 'Refinación De Petróleo', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Refinación De Petróleo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '324120', 'Fabricación De Productos De Asfalto', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Productos De Asfalto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '324191', 'Fabricación De Aceites Y Grasas Lubricantes', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Aceites Y Grasas Lubricantes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '324199', 'Fabricación De Coque Y Otros Productos Derivados Del Petróleo Refinado Y Del Carbón Mineral', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Coque Y Otros Productos Derivados Del Petróleo Refinado Y Del Carbón Mineral', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325110', 'Fabricación De Petroquímicos Básicos Del Gas Natural Y Del Petróleo Refinado', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Fabricación De Petroquímicos Básicos Del Gas Natural Y Del Petróleo Refinado', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325120', 'Fabricación De Gases Industriales', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Gases Industriales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325130', 'Fabricación De Pigmentos Y Colorantes Sintéticos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Pigmentos Y Colorantes Sintéticos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325180', 'Fabricación De Otros Productos Químicos Básicos Inorgánicos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos Químicos Básicos Inorgánicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325190', 'Fabricación De Otros Productos Químicos Básicos Orgánicos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos Químicos Básicos Orgánicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325211', 'Fabricación De Resinas Sintéticas', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Resinas Sintéticas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325212', 'Fabricación De Hules Sintéticos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Hules Sintéticos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325220', 'Fabricación De Fibras Químicas', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Fibras Químicas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325310', 'Fabricación De Fertilizantes', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Fertilizantes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325320', 'Fabricación De Pesticidas Y Otros Agroquímicos Excepto Fertilizantes', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Pesticidas Y Otros Agroquímicos Excepto Fertilizantes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325411', 'Fabricación De Materias Primas Para La Industria Farmacéutica', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Materias Primas Para La Industria Farmacéutica', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325412', 'Fabricación De Preparaciones Farmacéuticas', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Preparaciones Farmacéuticas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325510', 'Fabricación De Pinturas Y Recubrimientos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Pinturas Y Recubrimientos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325520', 'Fabricación De Adhesivos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Adhesivos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325610', 'Fabricación De Jabones Limpiadores Y Dentífricos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Jabones Limpiadores Y Dentífricos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325620', 'Fabricación De Cosméticos Perfumes Y Otras Preparaciones De Tocador', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Cosméticos Perfumes Y Otras Preparaciones De Tocador', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325910', 'Fabricación De Tintas Para Impresión', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Tintas Para Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325920', 'Fabricación De Explosivos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Explosivos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325991', 'Fabricación De Cerillos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Cerillos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325992', 'Fabricación De Películas Placas Y Papel Fotosensible Para Fotografía', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Películas Placas Y Papel Fotosensible Para Fotografía', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325993', 'Fabricación De Resinas De Plásticos Reciclados', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Resinas De Plásticos Reciclados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '325999', 'Fabricación De Otros Productos Químicos', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos Químicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326110', 'Fabricación De Bolsas Y Películas De Plástico Flexible', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Bolsas Y Películas De Plástico Flexible', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326120', 'Fabricación De Tubería Y Conexiones Y Tubos Para Embalaje', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Tubería Y Conexiones Y Tubos Para Embalaje', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326130', 'Fabricación De Laminados De Plástico Rígido', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Laminados De Plástico Rígido', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326140', 'Fabricación De Espumas Y Productos De Poliestireno', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Espumas Y Productos De Poliestireno', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326150', 'Fabricación De Espumas Y Productos De Uretano', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Espumas Y Productos De Uretano', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326160', 'Fabricación De Botellas De Plástico', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Botellas De Plástico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326191', 'Fabricación De Productos De Plástico Para El Hogar Con Y Sin Reforzamiento', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Productos De Plástico Para El Hogar Con Y Sin Reforzamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326192', 'Fabricación De Autopartes De Plástico Con Y Sin Reforzamiento', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Autopartes De Plástico Con Y Sin Reforzamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326193', 'Fabricación De Envases Y Contenedores De Plástico Para Embalaje Con Y Sin Reforzamiento', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Envases Y Contenedores De Plástico Para Embalaje Con Y Sin Reforzamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326194', 'Fabricación De Otros Productos De Plástico De Uso Industrial Sin Reforzamiento', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos De Plástico De Uso Industrial Sin Reforzamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326198', 'Fabricación De Otros Productos De Plástico Con Reforzamiento', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos De Plástico Con Reforzamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326199', 'Fabricación De Otros Productos De Plástico Sin Reforzamiento', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos De Plástico Sin Reforzamiento', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326211', 'Fabricación De Llantas Y Cámaras', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Llantas Y Cámaras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326212', 'Revitalización De Llantas', 'Industria', 'Industria Química', 'Otros Industria Química', 'Revitalización De Llantas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326220', 'Fabricación De Bandas Y Mangueras De Hule Y De Plástico', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Bandas Y Mangueras De Hule Y De Plástico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '326290', 'Fabricación De Otros Productos De Hule', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Otros Productos De Hule', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327111', 'Fabricación De Artículos De Alfarería Porcelana Y Loza', 'Industria', 'Industria Química', 'Otros Industria Química', 'Fabricación De Artículos De Alfarería Porcelana Y Loza', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327112', 'Fabricación De Muebles De Baño', 'Industria', 'Construcción E Infraestructura', 'Muebles Y Recubrimientos Cerámicos', 'Fabricación De Muebles De Baño', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327121', 'Fabricación De Ladrillos No Refractarios', 'Industria', 'Construcción E Infraestructura', 'Muebles Y Recubrimientos Cerámicos', 'Fabricación De Ladrillos No Refractarios', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327122', 'Fabricación De Azulejos Y Losetas No Refractarias', 'Industria', 'Construcción E Infraestructura', 'Muebles Y Recubrimientos Cerámicos', 'Fabricación De Azulejos Y Losetas No Refractarias', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327123', 'Fabricación De Productos Refractarios', 'Industria', 'Construcción E Infraestructura', 'Muebles Y Recubrimientos Cerámicos', 'Fabricación De Productos Refractarios', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327211', 'Fabricación De Vidrio', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Vidrio', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327212', 'Fabricación De Espejos', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Espejos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327213', 'Fabricación De Envases Y Ampolletas De Vidrio', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Envases Y Ampolletas De Vidrio', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327214', 'Fabricación De Fibra De Vidrio', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Fibra De Vidrio', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327215', 'Fabricación De Artículos De Vidrio De Uso Domestico', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Artículos De Vidrio De Uso Domestico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327216', 'Fabricación De Artículos De Vidrio De Uso Industrial Y Comercial', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Artículos De Vidrio De Uso Industrial Y Comercial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327219', 'Fabricación De Otros Productos De Vidrio', 'Industria', 'Insumos Primarios', 'Productos De Vidrio', 'Fabricación De Otros Productos De Vidrio', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327310', 'Fabricación De Cemento Y Productos A Base De Cemento En Plantas Integradas', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Cemento Y Productos A Base De Cemento En Plantas Integradas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327320', 'Fabricación De Concreto', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Concreto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327330', 'Fabricación De Tubos Y Bloques De Cemento Y Concreto', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Tubos Y Bloques De Cemento Y Concreto', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327391', 'Fabricación De Productos Preesforzados De Concreto', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Productos Preesforzados De Concreto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327399', 'Fabricación De Otros Productos De Cemento Y Concreto', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Otros Productos De Cemento Y Concreto', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327410', 'Fabricación De Cal', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Cal', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327420', 'Fabricación De Yeso Y Productos De Yeso', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Yeso Y Productos De Yeso', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327910', 'Fabricación De Productos Abrasivos', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Productos Abrasivos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327991', 'Fabricación De Productos A Base De Piedras De Cantera', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Productos A Base De Piedras De Cantera', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '327999', 'Fabricación De Otros Productos A Base De Minerales No Metálicos', 'Industria', 'Construcción E Infraestructura', 'Cementantes Y Productos De Concreto', 'Fabricación De Otros Productos A Base De Minerales No Metálicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331111', 'Complejos Siderúrgicos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Complejos Siderúrgicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331112', 'Fabricación De Desbastes Primarios Y Ferroaleaciones', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Desbastes Primarios Y Ferroaleaciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331210', 'Fabricación De Tubos Y Postes De Hierro Y Acero', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Tubos Y Postes De Hierro Y Acero', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331220', 'Fabricación De Otros Productos De Hierro Y Acero', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Otros Productos De Hierro Y Acero', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331310', 'Industria Básica Del Aluminio', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Industria Básica Del Aluminio', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331411', 'Fundición Y Refinación De Cobre', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fundición Y Refinación De Cobre', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331412', 'Fundición Y Refinación De Metales Preciosos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fundición Y Refinación De Metales Preciosos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331419', 'Fundición Y Refinación De Otros Metales No Ferrosos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fundición Y Refinación De Otros Metales No Ferrosos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331420', 'Laminación Secundaria De Cobre', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Laminación Secundaria De Cobre', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331490', 'Laminación Secundaria De Otros Metales No Ferrosos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Laminación Secundaria De Otros Metales No Ferrosos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331510', 'Moldeo Por Fundición De Piezas De Hierro Y Acero', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Moldeo Por Fundición De Piezas De Hierro Y Acero', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '331520', 'Moldeo Por Fundición De Piezas Metálicas No Ferrosas', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Moldeo Por Fundición De Piezas Metálicas No Ferrosas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332110', 'Fabricación De Productos Metálicos Forjados Y Troquelados', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Productos Metálicos Forjados Y Troquelados', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332211', 'Fabricación De Herramientas De Mano Metálicas Sin Motor', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Herramientas De Mano Metálicas Sin Motor', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332212', 'Fabricación De Utensilios De Cocina Metálicos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Utensilios De Cocina Metálicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332310', 'Fabricación De Estructuras Metálicas', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Estructuras Metálicas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332320', 'Fabricación De Productos De Herrería', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Productos De Herrería', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332410', 'Fabricación De Calderas Industriales', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Calderas Industriales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332420', 'Fabricación De Tanques Metálicos De Calibre Grueso', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Tanques Metálicos De Calibre Grueso', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332430', 'Fabricación De Envases Metálicos De Calibre Ligero', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Envases Metálicos De Calibre Ligero', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332510', 'Fabricación De Herrajes Y Cerraduras', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Herrajes Y Cerraduras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332610', 'Fabricación De Alambre Productos De Alambre Y Resortes', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Alambre Productos De Alambre Y Resortes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332710', 'Maquinado De Piezas Metálicas Para Maquinaria Y Equipo En General', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Maquinado De Piezas Metálicas Para Maquinaria Y Equipo En General', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332720', 'Fabricación De Tornillos Tuercas Remaches Y Similares', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Tornillos Tuercas Remaches Y Similares', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332810', 'Recubrimientos Y Terminados Metálicos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Recubrimientos Y Terminados Metálicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332910', 'Fabricación De Válvulas Metálicas', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Válvulas Metálicas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332991', 'Fabricación De Baleros Y Rodamientos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Baleros Y Rodamientos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '332999', 'Fabricación De Otros Productos Metálicos', 'Industria', 'Insumos Primarios', 'Productos De Acero Y Metales', 'Fabricación De Otros Productos Metálicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333111', 'Fabricación De Maquinaria Y Equipo Agrícola', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Agrícola', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333112', 'Fabricación De Maquinaria Y Equipo Pecuario', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Pecuario', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333120', 'Fabricación De Maquinaria Y Equipo Para La Construcción', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Construcción', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333130', 'Fabricación De Maquinaria Y Equipo Para La Industria Extractiva', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria Extractiva', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333210', 'Fabricación De Maquinaria Y Equipo Para La Industria De La Madera', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria De La Madera', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333220', 'Fabricación De Maquinaria Y Equipo Para La Industria Del Hule Y Del Plástico', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria Del Hule Y Del Plástico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333291', 'Fabricación De Maquinaria Y Equipo Para La Industria Alimentaria Y De Las Bebidas', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria Alimentaria Y De Las Bebidas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333292', 'Fabricación De Maquinaria Y Equipo Para La Industria Textil', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria Textil', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333293', 'Fabricación De Maquinaria Y Equipo Para La Industria De La Impresión', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria De La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333294', 'Fabricación De Maquinaria Y Equipo Para La Industria Del Vidrio Y Otros Minerales No Metálicos', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria Del Vidrio Y Otros Minerales No Metálicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333299', 'Fabricación De Maquinaria Y Equipo Para Otras Industrias Manufactureras', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para Otras Industrias Manufactureras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333312', 'Fabricación De Maquinas Fotocopiadoras', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinas Fotocopiadoras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333319', 'Fabricación De Otra Maquinaria Y Equipo Para El Comercio Y Los Servicios', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Otra Maquinaria Y Equipo Para El Comercio Y Los Servicios', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333411', 'Fabricación De Equipo De Aire Acondicionado Y Calefacción', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Equipo De Aire Acondicionado Y Calefacción', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333412', 'Fabricación De Equipo De Refrigeración Industrial Y Comercial', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Equipo De Refrigeración Industrial Y Comercial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333510', 'Fabricación De Maquinaria Y Equipo Para La Industria Metalmecánica', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para La Industria Metalmecánica', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333610', 'Fabricación De Motores De Combustión Interna Turbinas Y Transmisiones', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Motores De Combustión Interna Turbinas Y Transmisiones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333910', 'Fabricación De Bombas Y Sistemas De Bombeo', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Bombas Y Sistemas De Bombeo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333920', 'Fabricación De Maquinaria Y Equipo Para Levantar Y Trasladar', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para Levantar Y Trasladar', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333991', 'Fabricación De Equipo Para Soldar Y Soldaduras', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Equipo Para Soldar Y Soldaduras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333992', 'Fabricación De Maquinaria Y Equipo Para Envasar Y Empacar', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Maquinaria Y Equipo Para Envasar Y Empacar', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333993', 'Fabricación De Aparatos E Instrumentos Para Pesar', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Aparatos E Instrumentos Para Pesar', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '333999', 'Fabricación De Otra Maquinaria Y Equipo Para La Industria En General', 'Industria', 'Transformación', 'Fabricación De Maquinaria Y Equipo', 'Fabricación De Otra Maquinaria Y Equipo Para La Industria En General', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334110', 'Fabricación De Computadoras Y Equipo Periférico', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Computadoras Y Equipo Periférico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334210', 'Fabricación De Equipo Telefónico', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Equipo Telefónico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334220', 'Fabricación De Equipo De Transmisión Y Recepción De Señales De Radio Y Televisión Y Equipo De Comunicación Inalámbrico', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Equipo De Transmisión Y Recepción De Señales De Radio Y Televisión Y Equipo De Comunicación Inalámbrico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334290', 'Fabricación De Otros Equipos De Comunicación', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Otros Equipos De Comunicación', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334310', 'Fabricación De Equipo De Audio Y De Video', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Equipo De Audio Y De Video', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334410', 'Fabricación De Componentes Electrónicos', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Componentes Electrónicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334511', 'Fabricación De Relojes', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Relojes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334519', 'Fabricación De Otros Instrumentos De Medición Control Navegación Y Equipo Medico Electrónico', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Otros Instrumentos De Medición Control Navegación Y Equipo Medico Electrónico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '334610', 'Fabricación Y Reproducción De Medios Magnéticos Y Ópticos', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación Y Reproducción De Medios Magnéticos Y Ópticos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335110', 'Fabricación De Focos', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Focos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335120', 'Fabricación De Lámparas Ornamentales', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Lámparas Ornamentales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335210', 'Fabricación De Enseres Electrodomésticos Menores', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Enseres Electrodomésticos Menores', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335220', 'Fabricación De Aparatos De Línea Blanca', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Aparatos De Línea Blanca', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335311', 'Fabricación De Motores Y Generadores Eléctricos', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Motores Y Generadores Eléctricos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335312', 'Fabricación De Equipo Y Aparatos De Distribución De Energía Eléctrica', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Equipo Y Aparatos De Distribución De Energía Eléctrica', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335910', 'Fabricación De Acumuladores Y Pilas', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Acumuladores Y Pilas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335920', 'Fabricación De Cables De Conducción Eléctrica', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Cables De Conducción Eléctrica', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335930', 'Fabricación De Enchufes Contactos Fusibles Y Otros Accesorios Para Instalaciones Eléctricas', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Enchufes Contactos Fusibles Y Otros Accesorios Para Instalaciones Eléctricas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335991', 'Fabricación De Productos Eléctricos De Carbón Y Grafito', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Productos Eléctricos De Carbón Y Grafito', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '335999', 'Fabricación De Otros Productos Eléctricos', 'Industria', 'Transformación', 'Componentes Y Productos Electrónicos', 'Fabricación De Otros Productos Eléctricos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336110', 'Fabricación De Automóviles Y Camionetas', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Automóviles Y Camionetas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336120', 'Fabricación De Camiones Y Tractocamiones', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Camiones Y Tractocamiones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336210', 'Fabricación De Carrocerías Y Remolques', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Carrocerías Y Remolques', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336310', 'Fabricación De Motores De Gasolina Y Sus Partes Para Vehículos Automotrices', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Motores De Gasolina Y Sus Partes Para Vehículos Automotrices', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336320', 'Fabricación De Equipo Eléctrico Y Electrónico Y Sus Partes Para Vehículos Automotores', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Equipo Eléctrico Y Electrónico Y Sus Partes Para Vehículos Automotores', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336330', 'Fabricación De Partes De Sistemas De Dirección Y De Suspensión Para Vehículos Automotrices', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Partes De Sistemas De Dirección Y De Suspensión Para Vehículos Automotrices', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336340', 'Fabricación De Partes De Sistemas De Frenos Para Vehículos Automotrices', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Partes De Sistemas De Frenos Para Vehículos Automotrices', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336350', 'Fabricación De Partes De Sistemas De Transmisión Para Vehículos Automotores', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Partes De Sistemas De Transmisión Para Vehículos Automotores', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336360', 'Fabricación De Asientos Y Accesorios Interiores Para Vehículos Automotores', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Asientos Y Accesorios Interiores Para Vehículos Automotores', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336370', 'Fabricación De Piezas Metálicas Troqueladas Para Vehículos Automotrices', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Piezas Metálicas Troqueladas Para Vehículos Automotrices', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336390', 'Fabricación De Otras Partes Para Vehículos Automotrices', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Otras Partes Para Vehículos Automotrices', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336410', 'Fabricación De Equipo Aeroespacial', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Equipo Aeroespacial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336510', 'Fabricación De Equipo Ferroviario', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Equipo Ferroviario', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336610', 'Fabricación De Embarcaciones', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Embarcaciones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336991', 'Fabricación De Motocicletas', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Motocicletas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336992', 'Fabricación De Bicicletas Y Triciclos', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Bicicletas Y Triciclos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '336999', 'Fabricación De Otro Equipo De Transporte', 'Industria', 'Transformación', 'Vehiculos Y Equipos Para Transporte', 'Fabricación De Otro Equipo De Transporte', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '337110', 'Fabricación De Cocinas Integrales Y Muebles Modulares De Baño', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Cocinas Integrales Y Muebles Modulares De Baño', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '337120', 'Fabricación De Muebles Excepto Cocinas Integrales Muebles Modulares De Baño Y Muebles De Oficina Y Estantería', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Muebles Excepto Cocinas Integrales Muebles Modulares De Baño Y Muebles De Oficina Y Estantería', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '337210', 'Fabricación De Muebles De Oficina Y Estantería', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Muebles De Oficina Y Estantería', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '337910', 'Fabricación De Colchones', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Colchones', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '337920', 'Fabricación De Persianas Y Cortineros', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Persianas Y Cortineros', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339111', 'Fabricación De Equipo No Electrónico Para Uso Medico Dental Y Para Laboratorio', 'Industria', 'Transformación', 'Equipamiento Médico', 'Fabricación De Equipo No Electrónico Para Uso Medico Dental Y Para Laboratorio', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339112', 'Fabricación De Material Desechable De Uso Medico', 'Industria', 'Transformación', 'Equipamiento Médico', 'Fabricación De Material Desechable De Uso Medico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339113', 'Fabricación De Artículos Oftálmicos', 'Industria', 'Transformación', 'Equipamiento Médico', 'Fabricación De Artículos Oftálmicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339911', 'Acuñación E Impresión De Monedas', 'Industria', 'Transformación', 'Otros Transformación', 'Acuñación E Impresión De Monedas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339912', 'Orfebrería Y Joyería De Metales Y Piedras Preciosos', 'Industria', 'Transformación', 'Accesorios Personales Y Recreativos', 'Orfebrería Y Joyería De Metales Y Piedras Preciosos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339913', 'Joyería De Metales Y Piedras No Preciosos Y De Otros Materiales', 'Industria', 'Transformación', 'Accesorios Personales Y Recreativos', 'Joyería De Metales Y Piedras No Preciosos Y De Otros Materiales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339914', 'Metalistería De Metales No Preciosos', 'Industria', 'Transformación', 'Accesorios Personales Y Recreativos', 'Metalistería De Metales No Preciosos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339920', 'Fabricación De Artículos Deportivos', 'Industria', 'Transformación', 'Accesorios Personales Y Recreativos', 'Fabricación De Artículos Deportivos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339930', 'Fabricación De Juguetes', 'Industria', 'Transformación', 'Accesorios Personales Y Recreativos', 'Fabricación De Juguetes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339940', 'Fabricación De Artículos Y Accesorios Para Escritura Pintura Dibujo Y Actividades De Oficina', 'Industria', 'Transformación', 'Accesorios Personales Y Recreativos', 'Fabricación De Artículos Y Accesorios Para Escritura Pintura Dibujo Y Actividades De Oficina', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339950', 'Fabricación De Anuncios Y Señalamientos', 'Industria', 'Construcción E Infraestructura', 'Urbanización', 'Fabricación De Anuncios Y Señalamientos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339991', 'Fabricación De Instrumentos Musicales', 'Industria', 'Transformación', 'Otros Transformación', 'Fabricación De Instrumentos Musicales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339992', 'Fabricación De Cierres Botones Y Agujas', 'Industria', 'Transformación', 'Otros Transformación', 'Fabricación De Cierres Botones Y Agujas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339993', 'Fabricación De Escobas Cepillos Y Similares', 'Industria', 'Transformación', 'Otros Transformación', 'Fabricación De Escobas Cepillos Y Similares', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339994', 'Fabricación De Velas Y Veladoras', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Fabricación De Velas Y Veladoras', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339995', 'Fabricación De Ataúdes', 'Industria', 'Transformación', 'Equipamiento Médico', 'Fabricación De Ataúdes', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '339999', 'Otras Industrias Manufactureras', 'Industria', 'Transformación', 'Equipamiento Del Hogar Y Oficina', 'Otras Industrias Manufactureras', 11, 'Empresas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431110', 'Comercio Al Por Mayor De Abarrotes', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Abarrotes', 4, 'T. de Conveniencia > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431121', 'Comercio Al Por Mayor De Carnes Rojas', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Carnes Rojas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431122', 'Comercio Al Por Mayor De Carne De Aves', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Carne De Aves', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431123', 'Comercio Al Por Mayor De Pescados Y Mariscos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Pescados Y Mariscos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431130', 'Comercio Al Por Mayor De Frutas Y Verduras Frescas', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Frutas Y Verduras Frescas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431140', 'Comercio Al Por Mayor De Huevo', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Huevo', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431160', 'Comercio Al Por Mayor De Leche Y Otros Productos Lácteos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Leche Y Otros Productos Lácteos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431170', 'Comercio Al Por Mayor De Embutidos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Embutidos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431150', 'Comercio Al Por Mayor De Semillas Y Granos Alimenticios Especias Y Chiles Secos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Semillas Y Granos Alimenticios Especias Y Chiles Secos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431180', 'Comercio Al Por Mayor De Dulces Y Materias Primas Para Repostería', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Dulces Y Materias Primas Para Repostería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431191', 'Comercio Al Por Mayor De Pan Y Pasteles', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Pan Y Pasteles', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431192', 'Comercio Al Por Mayor De Botanas Y Frituras', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Botanas Y Frituras', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431193', 'Comercio Al Por Mayor De Conservas Alimenticias', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Conservas Alimenticias', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431194', 'Comercio Al Por Mayor De Miel', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Miel', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431199', 'Comercio Al Por Mayor De Otros Alimentos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Otros Alimentos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431211', 'Comercio Al Por Mayor De Bebidas No Alcohólicas Y Hielo', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Bebidas No Alcohólicas Y Hielo', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431212', 'Comercio Al Por Mayor De Vinos Y Licores', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Vinos Y Licores', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431213', 'Comercio Al Por Mayor De Cerveza', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Cerveza', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '431220', 'Comercio Al Por Mayor De Cigarros Puros Y Tabaco', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Cigarros Puros Y Tabaco', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '432111', 'Comercio Al Por Mayor De Fibras Hilos Y Telas', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Fibras Hilos Y Telas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '432112', 'Comercio Al Por Mayor De Blancos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Blancos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '432113', 'Comercio Al Por Mayor De Cueros Y Pieles', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Cueros Y Pieles', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '432119', 'Comercio Al Por Mayor De Otros Productos Textiles', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Otros Productos Textiles', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '432120', 'Comercio Al Por Mayor De Ropa Bisutería Y Accesorios De Vestir', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Ropa Bisutería Y Accesorios De Vestir', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '432130', 'Comercio Al Por Mayor De Calzado', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Calzado', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433110', 'Comercio Al Por Mayor De Productos Farmacéuticos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Productos Salud', 'Comercio Al Por Mayor De Productos Farmacéuticos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433210', 'Comercio Al Por Mayor De Artículos De Perfumería Y Cosméticos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Artículos De Perfumería Y Cosméticos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433220', 'Comercio Al Por Mayor De Artículos De Joyería Y Relojes', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Artículos De Joyería Y Relojes', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433311', 'Comercio Al Por Mayor De Discos Y Casetes', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Discos Y Casetes', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433312', 'Comercio Al Por Mayor De Juguetes Y Bicicletas', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Juguetes Y Bicicletas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433313', 'Comercio Al Por Mayor De Artículos Y Aparatos Deportivos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Artículos Y Aparatos Deportivos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433410', 'Comercio Al Por Mayor De Artículos De Papelería', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Artículos De Papelería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433420', 'Comercio Al Por Mayor De Libros', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Libros', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433430', 'Comercio Al Por Mayor De Revistas Y Periódicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Revistas Y Periódicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '433510', 'Comercio Al Por Mayor De Electrodomésticos Menores Y Aparatos De Línea Blanca', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Electrodomésticos Menores Y Aparatos De Línea Blanca', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434111', 'Comercio Al Por Mayor De Fertilizantes Plaguicidas Y Semillas Para Siembra', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Fertilizantes Plaguicidas Y Semillas Para Siembra', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434112', 'Comercio Al Por Mayor De Medicamentos Veterinarios Y Alimentos Para Animales Excepto Mascotas', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Productos Salud', 'Comercio Al Por Mayor De Medicamentos Veterinarios Y Alimentos Para Animales Excepto Mascotas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434211', 'Comercio Al Por Mayor De Cemento Tabique Y Grava', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Cemento Tabique Y Grava', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434219', 'Comercio Al Por Mayor De Otros Materiales Para La Construcción Excepto De Madera Y Metálicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Otros Materiales Para La Construcción Excepto De Madera Y Metálicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434221', 'Comercio Al Por Mayor De Materiales Metálicos Para La Construcción Y La Manufactura', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Materiales Metálicos Para La Construcción Y La Manufactura', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434222', 'Comercio Al Por Mayor De Productos Químicos Para La Industria Farmacéutica Y Para Otro Uso Industrial', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Productos Químicos Para La Industria Farmacéutica Y Para Otro Uso Industrial', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434223', 'Comercio Al Por Mayor De Envases En General Papel Y Cartón Para La Industria', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Envases En General Papel Y Cartón Para La Industria', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434224', 'Comercio Al Por Mayor De Madera Para La Construcción Y La Industria', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Madera Para La Construcción Y La Industria', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434225', 'Comercio Al Por Mayor De Equipo Y Material Eléctrico', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Equipo Y Material Eléctrico', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434226', 'Comercio Al Por Mayor De Pintura', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Pintura', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434227', 'Comercio Al Por Mayor De Vidrios Y Espejos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Vidrios Y Espejos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434228', 'Comercio Al Por Mayor De Ganado Y Aves En Pie', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Mayor De Ganado Y Aves En Pie', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434229', 'Comercio Al Por Mayor De Otras Materias Primas Para Otras Industrias', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Otras Materias Primas Para Otras Industrias', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434230', 'Comercio Al Por Mayor De Combustibles De Uso Industrial', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Combustibles De Uso Industrial', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434240', 'Comercio Al Por Mayor De Artículos Desechables', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Artículos Desechables', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434311', 'Comercio Al Por Mayor De Desechos Metálicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Desechos', 'Comercio Al Por Mayor De Desechos Metálicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434312', 'Comercio Al Por Mayor De Desechos De Papel Y De Cartón', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Desechos', 'Comercio Al Por Mayor De Desechos De Papel Y De Cartón', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434313', 'Comercio Al Por Mayor De Desechos De Vidrio', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Desechos', 'Comercio Al Por Mayor De Desechos De Vidrio', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434314', 'Comercio Al Por Mayor De Desechos De Plástico', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Desechos', 'Comercio Al Por Mayor De Desechos De Plástico', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '434319', 'Comercio Al Por Mayor De Otros Materiales De Desecho', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Desechos', 'Comercio Al Por Mayor De Otros Materiales De Desecho', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435110', 'Comercio Al Por Mayor De Maquinaria Y Equipo Agropecuario Forestal Y Para La Pesca', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Maquinaria Y Equipo Agropecuario Forestal Y Para La Pesca', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435210', 'Comercio Al Por Mayor De Maquinaria Y Equipo Para La Construcción Y La Minería', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Maquinaria Y Equipo Para La Construcción Y La Minería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435220', 'Comercio Al Por Mayor De Maquinaria Y Equipo Para La Industria Manufacturera', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Maquinaria Y Equipo Para La Industria Manufacturera', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435311', 'Comercio Al Por Mayor De Equipo De Telecomunicaciones Fotografía Y Cinematografía', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Equipo De Telecomunicaciones Fotografía Y Cinematografía', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435312', 'Comercio Al Por Mayor De Artículos Y Accesorios Para Diseño Y Pintura Artística', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Comercio Al Por Mayor De Artículos Y Accesorios Para Diseño Y Pintura Artística', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435313', 'Comercio Al Por Mayor De Mobiliario Equipo E Instrumental Medico Y De Laboratorio', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Productos Salud', 'Comercio Al Por Mayor De Mobiliario Equipo E Instrumental Medico Y De Laboratorio', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435319', 'Comercio Al Por Mayor De Maquinaria Y Equipo Para Otros Servicios Y Para Actividades Comerciales', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Maquinaria Y Equipo Para Otros Servicios Y Para Actividades Comerciales', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435411', 'Comercio Al Por Mayor De Mobiliario Equipo Y Accesorios De Computo', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Mobiliario Equipo Y Accesorios De Computo', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435412', 'Comercio Al Por Mayor De Mobiliario Y Equipo De Oficina', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Mobiliario Y Equipo De Oficina', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '435419', 'Comercio Al Por Mayor De Otra Maquinaria Y Equipo De Uso General', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Maquinaria Equipo Y Vehiculos', 'Comercio Al Por Mayor De Otra Maquinaria Y Equipo De Uso General', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '436111', 'Comercio Al Por Mayor De Camiones', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Camiones', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '436112', 'Comercio Al Por Mayor De Partes Y Refacciones Nuevas Para Automóviles Camionetas Y Camiones', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Comercio Al Por Mayor De Partes Y Refacciones Nuevas Para Automóviles Camionetas Y Camiones', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '437111', 'Intermediación De Comercio Al Por Mayor De Productos Agropecuarios Excepto A Través De Internet Y De Otros Medios Electrónicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Intermediación De Comercio Al Por Mayor De Productos Agropecuarios Excepto A Través De Internet Y De Otros Medios Electrónicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461121', 'Comercio Al Por Menor De Carnes Rojas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Carnes Rojas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461122', 'Comercio Al Por Menor De Carne De Aves', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Carne De Aves', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461123', 'Comercio Al Por Menor De Pescados Y Mariscos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Pescados Y Mariscos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461130', 'Comercio Al Por Menor De Frutas Y Verduras Frescas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Frutas Y Verduras Frescas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '437112', 'Intermediación De Comercio Al Por Mayor De Productos Para La Industria El Comercio Y Los Servicios Excepto A Través De Internet Y De Otros Medios Electrónicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Intermediación De Comercio Al Por Mayor De Productos Para La Industria El Comercio Y Los Servicios Excepto A Través De Internet Y De Otros Medios Electrónicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '437113', 'Intermediación De Comercio Al Por Mayor Para Productos De Uso Domestico Y Personal Excepto A Través De Internet Y De Otros Medios Electrónicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Insumos', 'Intermediación De Comercio Al Por Mayor Para Productos De Uso Domestico Y Personal Excepto A Través De Internet Y De Otros Medios Electrónicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '437210', 'Intermediación De Comercio Al Por Mayor Exclusivamente A Través De Internet Y Otros Medios Electrónicos', 'Comercio', 'Comercio Al Mayoreo', 'Mayoreo Bienes De Consumo', 'Intermediación De Comercio Al Por Mayor Exclusivamente A Través De Internet Y Otros Medios Electrónicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461110', 'Comercio Al Por Menor En Tiendas De Abarrotes Ultramarinos Y Misceláneas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor En Tiendas De Abarrotes Ultramarinos Y Misceláneas', 4, 'T. de Conveniencia > 30')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461140', 'Comercio Al Por Menor De Semillas Y Granos Alimenticios Especias Y Chiles Secos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Semillas Y Granos Alimenticios Especias Y Chiles Secos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461150', 'Comercio Al Por Menor De Leche Otros Productos Lácteos Y Embutidos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Leche Otros Productos Lácteos Y Embutidos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461160', 'Comercio Al Por Menor De Dulces Y Materias Primas Para Repostería', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Dulces Y Materias Primas Para Repostería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461170', 'Comercio Al Por Menor De Paletas De Hielo Y Helados', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Paletas De Hielo Y Helados', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461190', 'Comercio Al Por Menor De Otros Alimentos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Otros Alimentos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461211', 'Comercio Al Por Menor De Vinos Y Licores', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Vinos Y Licores', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461212', 'Comercio Al Por Menor De Cerveza', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Cerveza', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461213', 'Comercio Al Por Menor De Bebidas No Alcohólicas Y Hielo', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Bebidas No Alcohólicas Y Hielo', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '461220', 'Comercio Al Por Menor De Cigarros Puros Y Tabaco', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor De Cigarros Puros Y Tabaco', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '462111', 'Comercio Al Por Menor En Supermercados', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor En Supermercados', 2, 'T. de Autoservicio')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '462112', 'Comercio Al Por Menor En Minisúper', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Comercio Al Por Menor En Minisúper', 2, 'T. de Conveniencia')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '462210', 'Comercio Al Por Menor En Tiendas Departamentales', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor En Tiendas Departamentales', 5, 'T. Departamental')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463111', 'Comercio Al Por Menor De Telas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Telas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463112', 'Comercio Al Por Menor De Blancos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Blancos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463113', 'Comercio Al Por Menor De Artículos De Mercería Y Bonetería', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos De Mercería Y Bonetería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463211', 'Comercio Al Por Menor De Ropa Excepto De Bebe Y Lencería', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Ropa Excepto De Bebe Y Lencería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463212', 'Comercio Al Por Menor De Ropa De Bebe', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Ropa De Bebe', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463213', 'Comercio Al Por Menor De Lencería', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Lencería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463214', 'Comercio Al Por Menor De Disfraces Vestimenta Regional Y Vestidos De Novia', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Disfraces Vestimenta Regional Y Vestidos De Novia', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463215', 'Comercio Al Por Menor De Bisutería Y Accesorios De Vestir', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Bisutería Y Accesorios De Vestir', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463216', 'Comercio Al Por Menor De Ropa De Cuero Y Piel Y De Otros Artículos De Estos Materiales', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Ropa De Cuero Y Piel Y De Otros Artículos De Estos Materiales', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463217', 'Comercio Al Por Menor De Pañales Desechables', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Pañales Desechables', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463218', 'Comercio Al Por Menor De Sombreros', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Sombreros', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '463310', 'Comercio Al Por Menor De Calzado', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Calzado', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '464111', 'Farmacias Sin Minisúper', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Farmacias Sin Minisúper', 4, 'Farmacias')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '464112', 'Farmacias Con Minisúper', 'Comercio', 'Comercio Al Menudeo', 'Menudeo De Alimentos Bebidas Y Tabaco', 'Farmacias Con Minisúper', 4, 'Farmacias')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '464113', 'Comercio Al Por Menor De Productos Naturistas Medicamentos Homeopáticos Y De Complementos Alimenticios', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Productos Naturistas Medicamentos Homeopáticos Y De Complementos Alimenticios', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '464121', 'Comercio Al Por Menor De Lentes', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Lentes', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '464122', 'Comercio Al Por Menor De Artículos Ortopédicos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Ortopédicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465111', 'Comercio Al Por Menor De Artículos De Perfumería Y Cosméticos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos De Perfumería Y Cosméticos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465112', 'Comercio Al Por Menor De Artículos De Joyería Y Relojes', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos De Joyería Y Relojes', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465211', 'Comercio Al Por Menor De Discos Y Casetes', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Discos Y Casetes', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465212', 'Comercio Al Por Menor De Juguetes', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Juguetes', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465213', 'Comercio Al Por Menor De Bicicletas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Bicicletas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465214', 'Comercio Al Por Menor De Equipo Y Material Fotográfico', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Equipo Y Material Fotográfico', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465215', 'Comercio Al Por Menor De Artículos Y Aparatos Deportivos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Y Aparatos Deportivos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465216', 'Comercio Al Por Menor De Instrumentos Musicales', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Instrumentos Musicales', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465311', 'Comercio Al Por Menor De Artículos De Papelería', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos De Papelería', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465312', 'Comercio Al Por Menor De Libros', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Libros', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465313', 'Comercio Al Por Menor De Revistas Y Periódicos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Revistas Y Periódicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465911', 'Comercio Al Por Menor De Mascotas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Mascotas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465912', 'Comercio Al Por Menor De Regalos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Regalos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465913', 'Comercio Al Por Menor De Artículos Religiosos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Religiosos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465914', 'Comercio Al Por Menor De Artículos Desechables', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Desechables', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '465919', 'Comercio Al Por Menor De Otros Artículos De Uso Personal', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Otros Artículos De Uso Personal', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466111', 'Comercio Al Por Menor De Muebles Para El Hogar', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Muebles Para El Hogar', 4, 'Mueblerías')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466112', 'Comercio Al Por Menor De Electrodomésticos Menores Y Aparatos De Línea Blanca', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Electrodomésticos Menores Y Aparatos De Línea Blanca', 5, 'T. Departamental')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466113', 'Comercio Al Por Menor De Muebles Para Jardín', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Muebles Para Jardín', 4, 'Mueblerías')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466114', 'Comercio Al Por Menor De Cristalería Loza Y Utensilios De Cocina', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Cristalería Loza Y Utensilios De Cocina', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466211', 'Comercio Al Por Menor De Mobiliario Equipo Y Accesorios De Computo', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Mobiliario Equipo Y Accesorios De Computo', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466212', 'Comercio Al Por Menor De Teléfonos Y Otros Aparatos De Comunicación', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Teléfonos Y Otros Aparatos De Comunicación', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466311', 'Comercio Al Por Menor De Alfombras Cortinas Tapices Y Similares', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Alfombras Cortinas Tapices Y Similares', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466312', 'Comercio Al Por Menor De Plantas Y Flores Naturales', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Plantas Y Flores Naturales', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466313', 'Comercio Al Por Menor De Antigüedades Y Obras De Arte', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Antigüedades Y Obras De Arte', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466314', 'Comercio Al Por Menor De Lámparas Ornamentales Y Candiles', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Lámparas Ornamentales Y Candiles', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466319', 'Comercio Al Por Menor De Otros Artículos Para La Decoración De Interiores', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Otros Artículos Para La Decoración De Interiores', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '466410', 'Comercio Al Por Menor De Artículos Usados', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Usados', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467111', 'Comercio Al Por Menor En Ferreterías Y Tlapalerías', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor En Ferreterías Y Tlapalerías', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467112', 'Comercio Al Por Menor De Pisos Y Recubrimientos Cerámicos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Pisos Y Recubrimientos Cerámicos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467113', 'Comercio Al Por Menor De Pintura', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Pintura', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467114', 'Comercio Al Por Menor De Vidrios Y Espejos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Vidrios Y Espejos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467115', 'Comercio Al Por Menor De Artículos Para La Limpieza', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Para La Limpieza', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467117', 'Comercio Al Por Menor De Artículos Para Albercas Y Otros Artículos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Artículos Para Albercas Y Otros Artículos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '467116', 'Comercio Al Por Menor De Materiales Para La Construcción En Tiendas De Autoservicio Especializadas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor De Materiales Para La Construcción En Tiendas De Autoservicio Especializadas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468111', 'Comercio Al Por Menor De Automóviles Y Camionetas Nuevos', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Automóviles Y Camionetas Nuevos', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468112', 'Comercio Al Por Menor De Automóviles Y Camionetas Usados', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Automóviles Y Camionetas Usados', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468211', 'Comercio Al Por Menor De Partes Y Refacciones Nuevas Para Automóviles Camionetas Y Camiones', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Partes Y Refacciones Nuevas Para Automóviles Camionetas Y Camiones', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468212', 'Comercio Al Por Menor De Partes Y Refacciones Usadas Para Automóviles Camionetas Y Camiones', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Partes Y Refacciones Usadas Para Automóviles Camionetas Y Camiones', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468213', 'Comercio Al Por Menor De Llantas Y Cámaras Para Automóviles Camionetas Y Camiones', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Llantas Y Cámaras Para Automóviles Camionetas Y Camiones', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468311', 'Comercio Al Por Menor De Motocicletas', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Motocicletas', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468319', 'Comercio Al Por Menor De Otros Vehículos De Motor', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Maquinaria Equipo Y Vehículos', 'Comercio Al Por Menor De Otros Vehículos De Motor', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468411', 'Comercio Al Por Menor De Gasolina Y Diesel', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Gasolina Y Diesel', 8, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468412', 'Comercio Al Por Menor De Gas L. P. En Cilindros Y Para Tanques Estacionarios', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Gas L. P. En Cilindros Y Para Tanques Estacionarios', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468413', 'Comercio Al Por Menor De Gas L. P. En Estaciones De Carburación', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Gas L. P. En Estaciones De Carburación', 8, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468419', 'Comercio Al Por Menor De Otros Combustibles', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Otros Combustibles', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '468420', 'Comercio Al Por Menor De Aceites Y Grasas Lubricantes Aditivos Y Similares Para Vehículos De Motor', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Insumos', 'Comercio Al Por Menor De Aceites Y Grasas Lubricantes Aditivos Y Similares Para Vehículos De Motor', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '469110', 'Comercio Al Por Menor Exclusivamente A Través De Internet Y Catálogos Impresos Televisión Y Similares', 'Comercio', 'Comercio Al Menudeo', 'Menudeo Productos Consumo', 'Comercio Al Por Menor Exclusivamente A Través De Internet Y Catálogos Impresos Televisión Y Similares', 4, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '481111', 'Transporte Aéreo Regular En Líneas Aéreas Nacionales', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Transporte Aéreo Regular En Líneas Aéreas Nacionales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '481112', 'Transporte Aéreo Regular En Líneas Aéreas Extranjeras', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Transporte Aéreo Regular En Líneas Aéreas Extranjeras', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '481210', 'Transporte Aéreo No Regular', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Transporte Aéreo No Regular', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '482110', 'Transporte Por Ferrocarril', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Marítimo Y Ferrocaviario', 'Transporte Por Ferrocarril', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485410', 'Transporte Escolar Y De Personal', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Transporte Escolar Y De Personal', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485510', 'Alquiler De Autobuses Con Chofer', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Alquiler De Autobuses Con Chofer', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '483111', 'Transporte Marítimo De Altura Excepto De Petróleo Y Gas Natural', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Marítimo Y Ferrocaviario', 'Transporte Marítimo De Altura Excepto De Petróleo Y Gas Natural', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '483112', 'Transporte Marítimo De Cabotaje Excepto De Petróleo Y Gas Natural', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Marítimo Y Ferrocaviario', 'Transporte Marítimo De Cabotaje Excepto De Petróleo Y Gas Natural', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '483113', 'Transporte Marítimo De Petróleo Y Gas Natural', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Transporte Marítimo De Petróleo Y Gas Natural', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '483210', 'Transporte Por Aguas Interiores', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Marítimo Y Ferrocaviario', 'Transporte Por Aguas Interiores', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484111', 'Autotransporte Local De Productos Agrícolas Sin Refrigeración', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Local De Productos Agrícolas Sin Refrigeración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484119', 'Otro Autotransporte Local De Carga General', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Otro Autotransporte Local De Carga General', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484121', 'Autotransporte Foráneo De Productos Agrícolas Sin Refrigeración', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Foráneo De Productos Agrícolas Sin Refrigeración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484129', 'Otro Autotransporte Foráneo De Carga General', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Otro Autotransporte Foráneo De Carga General', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484210', 'Servicios De Mudanzas', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Servicios De Mudanzas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484221', 'Autotransporte Local De Materiales Para La Construcción', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Local De Materiales Para La Construcción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484222', 'Autotransporte Local De Materiales Y Residuos Peligrosos', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Local De Materiales Y Residuos Peligrosos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484223', 'Autotransporte Local Con Refrigeración', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Local Con Refrigeración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484224', 'Autotransporte Local De Madera', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Local De Madera', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484229', 'Otro Autotransporte Local De Carga Especializado', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Otro Autotransporte Local De Carga Especializado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484231', 'Autotransporte Foráneo De Materiales Para La Construcción', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Foráneo De Materiales Para La Construcción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484232', 'Autotransporte Foráneo De Materiales Y Residuos Peligrosos', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Foráneo De Materiales Y Residuos Peligrosos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484233', 'Autotransporte Foráneo Con Refrigeración', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Foráneo Con Refrigeración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484234', 'Autotransporte Foráneo De Madera', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Autotransporte Foráneo De Madera', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '484239', 'Otro Autotransporte Foráneo De Carga Especializado', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Otro Autotransporte Foráneo De Carga Especializado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485114', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Metro', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Colectivo Y Taxis', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Metro', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485210', 'Transporte Colectivo Foráneo De Pasajeros De Ruta Fija', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Colectivo Y Taxis', 'Transporte Colectivo Foráneo De Pasajeros De Ruta Fija', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485311', 'Transporte De Pasajeros En Taxis De Sitio', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Colectivo Y Taxis', 'Transporte De Pasajeros En Taxis De Sitio', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485320', 'Alquiler De Automóviles Con Chofer', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Alquiler De Automóviles Con Chofer', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485111', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Autobuses De Ruta Fija', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Autobuses De Ruta Fija', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485112', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Automóviles De Ruta Fija', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Colectivo Y Taxis', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Automóviles De Ruta Fija', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485113', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Trolebuses Y Trenes Ligeros', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Colectivo Y Taxis', 'Transporte Colectivo Urbano Y Suburbano De Pasajeros En Trolebuses Y Trenes Ligeros', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '485990', 'Otro Transporte Terrestre De Pasajeros', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Otro Transporte Terrestre De Pasajeros', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '486110', 'Transporte De Petróleo Crudo Por Ductos', 'Industria', 'Extracción Y Transporte De Petróleo Y Gas', 'Petroleo', 'Transporte De Petróleo Crudo Por Ductos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '487110', 'Transporte Turístico Por Tierra', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Transporte Turístico Por Tierra', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '487210', 'Transporte Turístico Por Agua', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Transporte Turístico Por Agua', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '487990', 'Otro Transporte Turístico', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Otro Transporte Turístico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488111', 'Servicios A La Navegación Aérea', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Servicios A La Navegación Aérea', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488112', 'Administración De Aeropuertos Y Helipuertos', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Administración De Aeropuertos Y Helipuertos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488190', 'Otros Servicios Relacionados Con El Transporte Aéreo', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Y Servicios Aéreo', 'Otros Servicios Relacionados Con El Transporte Aéreo', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488210', 'Servicios Relacionados Con El Transporte Por Ferrocarril', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Servicios Relacionados Con El Transporte Por Ferrocarril', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488310', 'Administración De Puertos Y Muelles', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Marítimo Y Ferrocaviario', 'Administración De Puertos Y Muelles', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488320', 'Servicios De Carga Y Descarga Para El Transporte Por Agua', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Servicios De Carga Y Descarga Para El Transporte Por Agua', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488330', 'Servicios Para La Navegación Por Agua', 'Servicios', 'Transporte Y Alojamiento', 'Transporte Marítimo Y Ferrocaviario', 'Servicios Para La Navegación Por Agua', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488390', 'Otros Servicios Relacionados Con El Transporte Por Agua', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Otros Servicios Relacionados Con El Transporte Por Agua', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488410', 'Servicios De Grúa', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Servicios De Grúa', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488491', 'Servicios De Administración De Centrales Camioneras', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Servicios De Administración De Centrales Camioneras', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488492', 'Servicios De Administración De Carreteras Puentes Y Servicios Auxiliares', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Servicios De Administración De Carreteras Puentes Y Servicios Auxiliares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '524120', 'Fondos De Aseguramiento Campesino', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Fondos De Aseguramiento Campesino', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '524130', 'Compañías Afianzadoras', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Compañías Afianzadoras', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '524210', 'Agentes Ajustadores Y Gestores De Seguros Y Fianzas', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Agentes Ajustadores Y Gestores De Seguros Y Fianzas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488493', 'Servicios De Bascula Para El Transporte Y Otros Servicios Relacionados Con El Transporte Por Carretera', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Servicios De Bascula Para El Transporte Y Otros Servicios Relacionados Con El Transporte Por Carretera', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488511', 'Servicios De Agencias Aduanales', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Servicios De Agencias Aduanales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488519', 'Otros Servicios De Intermediación Para El Transporte De Carga', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Otros Servicios De Intermediación Para El Transporte De Carga', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '488990', 'Otros Servicios Relacionados Con El Transporte', 'Servicios', 'Transporte Y Alojamiento', 'Otros Servicios De Transporte Y Comunicación', 'Otros Servicios Relacionados Con El Transporte', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '491110', 'Servicios Postales', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Servicios Postales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '492110', 'Servicios De Mensajería Y Paquetería Foránea', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Servicios De Mensajería Y Paquetería Foránea', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '492210', 'Servicios De Mensajería Y Paquetería Local', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Servicios De Mensajería Y Paquetería Local', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '493111', 'Almacenes Generales De Deposito', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Almacenes Generales De Deposito', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '493119', 'Otros Servicios De Almacenamiento General Sin Instalaciones Especializadas', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Otros Servicios De Almacenamiento General Sin Instalaciones Especializadas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '493120', 'Almacenamiento Con Refrigeración', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Almacenamiento Con Refrigeración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '493130', 'Almacenamiento De Productos Agrícolas Que No Requieren Refrigeración', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Almacenamiento De Productos Agrícolas Que No Requieren Refrigeración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '493190', 'Otros Servicios De Almacenamiento Con Instalaciones Especializadas', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Otros Servicios De Almacenamiento Con Instalaciones Especializadas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511111', 'Edición De Periódicos', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Periódicos', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511112', 'Edición De Periódicos Integrada Con La Impresión', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Periódicos Integrada Con La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511121', 'Edición De Revistas Y Otras Publicaciones Periódicas', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Revistas Y Otras Publicaciones Periódicas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511122', 'Edición De Revistas Y Otras Publicaciones Periódicas Integrada Con La Impresión', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Revistas Y Otras Publicaciones Periódicas Integrada Con La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511131', 'Edición De Libros', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Libros', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511132', 'Edición De Libros Integrada Con La Impresión', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Libros Integrada Con La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511141', 'Edición De Directorios Y De Listas De Correo', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Directorios Y De Listas De Correo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511191', 'Edición De Otros Materiales', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Otros Materiales', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511192', 'Edición De Otros Materiales Integrada Con La Impresión', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Otros Materiales Integrada Con La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511210', 'Edición De Software Y Edición De Software Integrada Con La Reproducción', 'Servicios', 'Servicios Especializados', 'Software', 'Edición De Software Y Edición De Software Integrada Con La Reproducción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '511142', 'Edición De Directorios Y De Listas De Correo Integrada Con La Impresión', 'Industria', 'Transformación', 'Impresión Textos Y Publicidad', 'Edición De Directorios Y De Listas De Correo Integrada Con La Impresión', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512111', 'Producción De Películas', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Producción De Películas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512112', 'Producción De Programas Para La Televisión', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Producción De Programas Para La Televisión', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512113', 'Producción De Videoclips Comerciales Y Otros Materiales Audiovisuales', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Producción De Videoclips Comerciales Y Otros Materiales Audiovisuales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512120', 'Distribución De Películas Y De Otros Materiales Audiovisuales', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Distribución De Películas Y De Otros Materiales Audiovisuales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512130', 'Exhibición De Películas Y Otros Materiales Audiovisuales', 'Servicios', 'Entretenimiento', 'Cines Y Teatros', 'Exhibición De Películas Y Otros Materiales Audiovisuales', 7, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512190', 'Servicios De Postproducción Y Otros Servicios Para La Industria Fílmica Y Del Video', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Servicios De Postproducción Y Otros Servicios Para La Industria Fílmica Y Del Video', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512210', 'Productoras Discográficas', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Productoras Discográficas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512220', 'Producción De Material Discográfico Integrada Con Su Reproducción Y Distribución', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Producción De Material Discográfico Integrada Con Su Reproducción Y Distribución', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512230', 'Editoras De Música', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Editoras De Música', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512240', 'Grabación De Discos Compactos (Cd) Y De Video Digital (Dvd) O Casetes Musicales', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Grabación De Discos Compactos (Cd) Y De Video Digital (Dvd) O Casetes Musicales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '512290', 'Otros Servicios De Grabación Del Sonido', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Otros Servicios De Grabación Del Sonido', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '515110', 'Transmisión De Programas De Radio', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Medios Masivos De Comunicación', 'Transmisión De Programas De Radio', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '515120', 'Transmisión De Programas De Televisión', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Medios Masivos De Comunicación', 'Transmisión De Programas De Televisión', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '515210', 'Producción De Programación De Canales Para Sistemas De Televisión Por Cable O Satelitales', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Medios Masivos De Comunicación', 'Producción De Programación De Canales Para Sistemas De Televisión Por Cable O Satelitales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '517111', 'Operadores De Telecomunicaciones Alambricas Excepto Por Suscripción', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Servicios De Telecomunicación', 'Operadores De Telecomunicaciones Alambricas Excepto Por Suscripción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '517112', 'Operadores De Telecomunicaciones Alambricas Por Suscripción', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Servicios De Telecomunicación', 'Operadores De Telecomunicaciones Alambricas Por Suscripción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '523910', 'Asesoría En Inversiones', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Asesoría En Inversiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '523990', 'Otros Servicios Relacionados Con La Intermediación Bursátil', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Otros Servicios Relacionados Con La Intermediación Bursátil', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '524110', 'Compañías De Seguros', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Compañías De Seguros', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '517210', 'Operadores De Telecomunicaciones Inalámbricas Excepto Servicios De Satélite', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Servicios De Telecomunicación', 'Operadores De Telecomunicaciones Inalámbricas Excepto Servicios De Satélite', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '517410', 'Servicios De Telecomunicaciones Por Satélite', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Servicios De Telecomunicación', 'Servicios De Telecomunicaciones Por Satélite', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '517910', 'Otros Servicios De Telecomunicaciones', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Servicios De Telecomunicación', 'Otros Servicios De Telecomunicaciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '518210', 'Procesamiento Electrónico De Información Hospedaje Y Otros Servicios Relacionados', 'Servicios', 'Servicios Especializados', 'Software', 'Procesamiento Electrónico De Información Hospedaje Y Otros Servicios Relacionados', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '519110', 'Agencias Noticiosas', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Medios Masivos De Comunicación', 'Agencias Noticiosas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '519121', 'Bibliotecas Y Archivos Del Sector Privado', 'Servicios', 'Educación', 'Otros Educación', 'Bibliotecas Y Archivos Del Sector Privado', 6, 'Cultura')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '519122', 'Bibliotecas Y Archivos Del Sector Publico', 'Servicios', 'Educación', 'Otros Educación', 'Bibliotecas Y Archivos Del Sector Publico', 6, 'Cultura')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '519130', 'Edición Y Difusión De Contenido Exclusivamente A Través De Internet Y Servicios De Búsqueda En La Red', 'Servicios', 'Servicios Especializados', 'Software', 'Edición Y Difusión De Contenido Exclusivamente A Través De Internet Y Servicios De Búsqueda En La Red', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '519190', 'Otros Servicios De Suministro De Información', 'Servicios', 'Servicios Especializados', 'Software', 'Otros Servicios De Suministro De Información', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '521110', 'Banca Central', 'Servicios', 'Servicios Especializados', 'Software', 'Banca Central', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522110', 'Banca Múltiple', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Banca Múltiple', 3, 'Bancos')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522210', 'Banca De Desarrollo', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Banca De Desarrollo', 3, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522220', 'Fondos Y Fideicomisos Financieros', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Fondos Y Fideicomisos Financieros', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522310', 'Uniones De Crédito', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Uniones De Crédito', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522320', 'Cajas De Ahorro Popular', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Cajas De Ahorro Popular', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522390', 'Otras Instituciones De Ahorro Y Préstamo', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Otras Instituciones De Ahorro Y Préstamo', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522410', 'Arrendadoras Financieras', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Arrendadoras Financieras', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522420', 'Compañías De Factoraje Financiero', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Compañías De Factoraje Financiero', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522430', 'Sociedades Financieras De Objeto Limitado', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Sociedades Financieras De Objeto Limitado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522440', 'Compañías De Autofinanciamiento', 'Servicios', 'Servicios Financieros Y Prendarios', 'Arrendadoras Y Afianzadoras', 'Compañías De Autofinanciamiento', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522451', 'Montepíos', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Montepíos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522452', 'Casas De Empeño', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Casas De Empeño', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '523110', 'Casas De Bolsa', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Casas De Bolsa', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '523121', 'Casas De Cambio', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Casas De Cambio', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '523122', 'Centros Cambiarios', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Centros Cambiarios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '523210', 'Bolsa De Valores', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Bolsa De Valores', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522490', 'Otras Instituciones De Intermediación Crediticia Y Financiera No Bursátil', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Otras Instituciones De Intermediación Crediticia Y Financiera No Bursátil', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '522510', 'Servicios Relacionados Con La Intermediación Crediticia No Bursátil', 'Servicios', 'Servicios Financieros Y Prendarios', 'Bancos Y Casas Bolsa', 'Servicios Relacionados Con La Intermediación Crediticia No Bursátil', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '524220', 'Administración De Cajas De Pensión Y De Seguros Independientes', 'Servicios', 'Servicios Financieros Y Prendarios', 'Cajas De Ahorro Préstamo Y Cambiarios', 'Administración De Cajas De Pensión Y De Seguros Independientes', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531111', 'Alquiler Sin Intermediación De Viviendas Amuebladas', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Alquiler Sin Intermediación De Viviendas Amuebladas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531112', 'Alquiler Sin Intermediación De Viviendas No Amuebladas', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Alquiler Sin Intermediación De Viviendas No Amuebladas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531113', 'Alquiler Sin Intermediación De Salones Para Fiestas Y Convenciones', 'Servicios', 'Entretenimiento', 'Renta De Servicios De Entretenimiento', 'Alquiler Sin Intermediación De Salones Para Fiestas Y Convenciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531114', 'Alquiler Sin Intermediación De Oficinas Y Locales Comerciales', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Alquiler Sin Intermediación De Oficinas Y Locales Comerciales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531115', 'Alquiler Sin Intermediación De Teatros Estadios Auditorios Y Similares', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Alquiler Sin Intermediación De Teatros Estadios Auditorios Y Similares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531119', 'Alquiler Sin Intermediación De Otros Bienes Raíces', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Alquiler Sin Intermediación De Otros Bienes Raíces', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531210', 'Inmobiliarias Y Corredores De Bienes Raíces', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Inmobiliarias Y Corredores De Bienes Raíces', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531311', 'Servicios De Administración De Bienes Raíces', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Servicios De Administración De Bienes Raíces', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '531319', 'Otros Servicios Relacionados Con Los Servicios Inmobiliarios', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Inmuebles', 'Otros Servicios Relacionados Con Los Servicios Inmobiliarios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532110', 'Alquiler De Automóviles Sin Chofer', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Alquiler De Automóviles Sin Chofer', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532121', 'Alquiler De Camiones De Carga Sin Chofer', 'Servicios', 'Transporte Y Alojamiento', 'Transporte De Mercancias', 'Alquiler De Camiones De Carga Sin Chofer', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532122', 'Alquiler De Autobuses Minibuses Y Remolques Sin Chofer', 'Servicios', 'Transporte Y Alojamiento', 'Alquiler De Transporte Para Pasajeros', 'Alquiler De Autobuses Minibuses Y Remolques Sin Chofer', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532210', 'Alquiler De Aparatos Eléctricos Y Electrónicos Para El Hogar Y Personales', 'Servicios', 'Entretenimiento', 'Renta De Servicios De Entretenimiento', 'Alquiler De Aparatos Eléctricos Y Electrónicos Para El Hogar Y Personales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532220', 'Alquiler De Prendas De Vestir', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Artículos', 'Alquiler De Prendas De Vestir', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532230', 'Alquiler De Videocasetes Y Discos', 'Servicios', 'Entretenimiento', 'Renta De Servicios De Entretenimiento', 'Alquiler De Videocasetes Y Discos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532291', 'Alquiler De Mesas Sillas Vajillas Y Similares', 'Servicios', 'Entretenimiento', 'Renta De Servicios De Entretenimiento', 'Alquiler De Mesas Sillas Vajillas Y Similares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532292', 'Alquiler De Instrumentos Musicales', 'Servicios', 'Entretenimiento', 'Renta De Servicios De Entretenimiento', 'Alquiler De Instrumentos Musicales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532299', 'Alquiler De Otros Artículos Para El Hogar Y Personales', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Artículos', 'Alquiler De Otros Artículos Para El Hogar Y Personales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532310', 'Centros Generales De Alquiler', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Artículos', 'Centros Generales De Alquiler', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532411', 'Alquiler De Maquinaria Y Equipo Para Construcción Minería Y Actividades Forestales', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Alquiler De Maquinaria Y Equipo Para Construcción Minería Y Actividades Forestales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532412', 'Alquiler De Equipo De Transporte Excepto Terrestre', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Alquiler De Equipo De Transporte Excepto Terrestre', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532420', 'Alquiler De Equipo De Computo Y De Otras Maquinas Y Mobiliario De Oficina', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Alquiler De Equipo De Computo Y De Otras Maquinas Y Mobiliario De Oficina', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532491', 'Alquiler De Maquinaria Y Equipo Agropecuario Pesquero Y Para La Industria Manufacturera', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Alquiler De Maquinaria Y Equipo Agropecuario Pesquero Y Para La Industria Manufacturera', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532492', 'Alquiler De Maquinaria Y Equipo Para Mover Levantar Y Acomodar Materiales', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Alquiler De Maquinaria Y Equipo Para Mover Levantar Y Acomodar Materiales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '532493', 'Alquiler De Maquinaria Y Equipo Comercial Y De Servicios', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Alquiler De Maquinaria Y Equipo Comercial Y De Servicios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '533110', 'Servicios De Alquiler De Marcas Registradas Patentes Y Franquicias', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Alquiler De Artículos', 'Servicios De Alquiler De Marcas Registradas Patentes Y Franquicias', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541110', 'Bufetes Jurídicos', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Bufetes Jurídicos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541120', 'Notarias Publicas', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Notarias Publicas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541190', 'Servicios De Apoyo Para Efectuar Tramites Legales', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Servicios De Apoyo Para Efectuar Tramites Legales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541211', 'Servicios De Contabilidad Y Auditoria', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Servicios De Contabilidad Y Auditoria', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541219', 'Otros Servicios Relacionados Con La Contabilidad', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Otros Servicios Relacionados Con La Contabilidad', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541310', 'Servicios De Arquitectura', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Arquitectura', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541320', 'Servicios De Arquitectura De Paisaje Y Urbanismo', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Arquitectura De Paisaje Y Urbanismo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541330', 'Servicios De Ingeniería', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Ingeniería', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541340', 'Servicios De Dibujo', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Dibujo', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541350', 'Servicios De Inspección De Edificios', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Inspección De Edificios', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541360', 'Servicios De Levantamiento Geofísico', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Levantamiento Geofísico', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541370', 'Servicios De Elaboración De Mapas', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Servicios De Elaboración De Mapas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541380', 'Laboratorios De Pruebas', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Laboratorios De Pruebas', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541410', 'Diseño Y Decoración De Interiores', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Diseño Y Decoración De Interiores', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541420', 'Diseño Industrial', 'Industria', 'Construcción E Infraestructura', 'Servicios Para La Industria', 'Diseño Industrial', 11, 'Empresas > 100')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541430', 'Diseño Grafico', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Diseño Grafico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541490', 'Diseño De Modas Y Otros Diseños Especializados', 'Servicios', 'Servicios Especializados', 'Otros Servicios Especializados', 'Diseño De Modas Y Otros Diseños Especializados', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541510', 'Servicios De Diseño De Sistemas De Computo Y Servicios Relacionados', 'Servicios', 'Servicios Especializados', 'Software', 'Servicios De Diseño De Sistemas De Computo Y Servicios Relacionados', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541610', 'Servicios De Consultoría En Administración', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Servicios De Consultoría En Administración', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541620', 'Servicios De Consultoría En Medio Ambiente', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Servicios De Consultoría En Medio Ambiente', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541690', 'Otros Servicios De Consultoría Científica Y Técnica', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Otros Servicios De Consultoría Científica Y Técnica', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541711', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Naturales Y Exactas Ingeniería Y Ciencias De La Vida Prestados Por El Sector Privado', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Naturales Y Exactas Ingeniería Y Ciencias De La Vida Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541712', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Naturales Y Exactas Ingeniería Y Ciencias De La Vida Prestados Por El Sector Publico', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Naturales Y Exactas Ingeniería Y Ciencias De La Vida Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541721', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Sociales Y Humanidades Prestados Por El Sector Privado', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Sociales Y Humanidades Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541722', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Sociales Y Humanidades Prestados Por El Sector Publico', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Servicios De Investigación Científica Y Desarrollo En Ciencias Sociales Y Humanidades Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541810', 'Agencias De Publicidad', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Agencias De Publicidad', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541820', 'Agencias De Relaciones Publicas', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Agencias De Relaciones Publicas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541830', 'Agencias De Compra De Medios A Petición Del Cliente', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Agencias De Compra De Medios A Petición Del Cliente', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541840', 'Agencias De Representación De Medios', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Agencias De Representación De Medios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541850', 'Agencias De Anuncios Publicitarios', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Agencias De Anuncios Publicitarios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541860', 'Agencias De Correo Directo', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Agencias De Correo Directo', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541870', 'Distribución De Material Publicitario', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Distribución De Material Publicitario', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541890', 'Servicios De Rotulación Y Otros Servicios De Publicidad', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Servicios De Rotulación Y Otros Servicios De Publicidad', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541920', 'Servicios De Fotografía Y Videograbación', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Servicios De Fotografía Y Videograbación', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541930', 'Servicios De Traducción E Interpretación', 'Servicios', 'Servicios Especializados', 'Otros Servicios Especializados', 'Servicios De Traducción E Interpretación', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541990', 'Otros Servicios Profesionales Científicos Y Técnicos', 'Servicios', 'Servicios Especializados', 'Investigación Científica', 'Otros Servicios Profesionales Científicos Y Técnicos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541910', 'Servicios De Investigación De Mercados Y Encuestas De Opinión Publica', 'Servicios', 'Servicios Especializados', 'Servicios De Mercadotecnia', 'Servicios De Investigación De Mercados Y Encuestas De Opinión Publica', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541941', 'Servicios Veterinarios Para Mascotas Prestados Por El Sector Privado', 'Servicios', 'Servicios Especializados', 'Servicios Veterinarios', 'Servicios Veterinarios Para Mascotas Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541942', 'Servicios Veterinarios Para Mascotas Prestados Por El Sector Publico', 'Servicios', 'Servicios Especializados', 'Servicios Veterinarios', 'Servicios Veterinarios Para Mascotas Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541943', 'Servicios Veterinarios Para La Ganadería Prestados Por El Sector Privado', 'Servicios', 'Servicios Especializados', 'Servicios Veterinarios', 'Servicios Veterinarios Para La Ganadería Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '541944', 'Servicios Veterinarios Para La Ganadería Prestados Por El Sector Publico', 'Servicios', 'Servicios Especializados', 'Servicios Veterinarios', 'Servicios Veterinarios Para La Ganadería Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '551111', 'Corporativos', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Corporativos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '551112', 'Tenedoras De Acciones', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Tenedoras De Acciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561110', 'Servicios De Administración De Negocios', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Servicios De Administración De Negocios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561210', 'Servicios Combinados De Apoyo En Instalaciones', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Servicios Combinados De Apoyo En Instalaciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561310', 'Agencias De Colocación', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Agencias De Colocación', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561320', 'Agencias De Empleo Temporal', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Agencias De Empleo Temporal', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561330', 'Suministro De Personal Permanente', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Suministro De Personal Permanente', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561410', 'Servicios De Preparación De Documentos', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Servicios De Preparación De Documentos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561421', 'Servicios De Casetas Telefónicas', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Servicios De Casetas Telefónicas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561422', 'Servicios De Recepción De Llamadas Telefónicas Y Promoción Por Teléfono', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Servicios De Recepción De Llamadas Telefónicas Y Promoción Por Teléfono', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561431', 'Servicios De Fotocopiado Fax Y Afines', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Servicios De Fotocopiado Fax Y Afines', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561432', 'Servicios De Acceso A Computadoras', 'Servicios', 'Alquiler De Inmuebles Equipo Y Otros', 'Maquinaria Y Equipo', 'Servicios De Acceso A Computadoras', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561440', 'Agencias De Cobranza', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Agencias De Cobranza', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561450', 'Despachos De Investigación De Solvencia Financiera', 'Servicios', 'Servicios Especializados', 'Tercerización De Servicios', 'Despachos De Investigación De Solvencia Financiera', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561490', 'Otros Servicios De Apoyo Secretarial Y Similares', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Otros Servicios De Apoyo Secretarial Y Similares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561510', 'Agencias De Viajes', 'Servicios', 'Transporte Y Alojamiento', 'Agencias De Viaje Y Eventos', 'Agencias De Viajes', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611142', 'Escuelas De Educación Secundaria Técnica Del Sector Publico', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Secundaria Técnica Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611151', 'Escuelas De Educación Media Técnica Terminal Del Sector Privado', 'Servicios', 'Educación', 'Educación Media', 'Escuelas De Educación Media Técnica Terminal Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611152', 'Escuelas De Educación Media Técnica Terminal Del Sector Publico', 'Servicios', 'Educación', 'Educación Media', 'Escuelas De Educación Media Técnica Terminal Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561520', 'Organización De Excursiones Y Paquetes Turísticos Para Agencias De Viajes', 'Servicios', 'Transporte Y Alojamiento', 'Agencias De Viaje Y Eventos', 'Organización De Excursiones Y Paquetes Turísticos Para Agencias De Viajes', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561590', 'Otros Servicios De Reservaciones', 'Servicios', 'Transporte Y Alojamiento', 'Agencias De Viaje Y Eventos', 'Otros Servicios De Reservaciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561610', 'Servicios De Investigación Y De Protección Y Custodia Excepto Mediante Monitoreo', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Servicios De Investigación Y De Protección Y Custodia Excepto Mediante Monitoreo', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561620', 'Servicios De Protección Y Custodia Mediante El Monitoreo De Sistemas De Seguridad', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Servicios De Protección Y Custodia Mediante El Monitoreo De Sistemas De Seguridad', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561710', 'Servicios De Control Y Exterminación De Plagas', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Servicios De Control Y Exterminación De Plagas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561720', 'Servicios De Limpieza De Inmuebles', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Servicios De Limpieza De Inmuebles', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561730', 'Servicios De Instalación Y Mantenimiento De Aéreas Verdes', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Servicios De Instalación Y Mantenimiento De Aéreas Verdes', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561740', 'Servicios De Limpieza De Tapicería Alfombras Y Muebles', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Servicios De Limpieza De Tapicería Alfombras Y Muebles', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561790', 'Otros Servicios De Limpieza', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Otros Servicios De Limpieza', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561910', 'Servicios De Empacado Y Etiquetado', 'Servicios', 'Servicios Especializados', 'Otros Servicios Especializados', 'Servicios De Empacado Y Etiquetado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561920', 'Organizadores De Convenciones Y Ferias Comerciales E Industriales', 'Servicios', 'Transporte Y Alojamiento', 'Agencias De Viaje Y Eventos', 'Organizadores De Convenciones Y Ferias Comerciales E Industriales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '561990', 'Otros Servicios De Apoyo A Los Negocios', 'Servicios', 'Servicios Especializados', 'Servicios Administrativos Y De Recursos Humanos', 'Otros Servicios De Apoyo A Los Negocios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '562111', 'Manejo De Residuos Peligrosos Y Servicios De Remediación A Zonas Dañadas Por Materiales O Residuos Peligrosos', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Manejo De Residuos Peligrosos Y Servicios De Remediación A Zonas Dañadas Por Materiales O Residuos Peligrosos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '562112', 'Manejo De Desechos No Peligrosos Y Servicios De Remediación A Zonas Dañadas Por Desechos No Peligrosos', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Manejo De Desechos No Peligrosos Y Servicios De Remediación A Zonas Dañadas Por Desechos No Peligrosos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611111', 'Escuelas De Educación Preescolar Del Sector Privado', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Preescolar Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611112', 'Escuelas De Educación Preescolar Del Sector Publico', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Preescolar Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611121', 'Escuelas De Educación Primaria Del Sector Privado', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Primaria Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611122', 'Escuelas De Educación Primaria Del Sector Publico', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Primaria Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611131', 'Escuelas De Educación Secundaria General Del Sector Privado', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Secundaria General Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611132', 'Escuelas De Educación Secundaria General Del Sector Publico', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Secundaria General Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611141', 'Escuelas De Educación Secundaria Técnica Del Sector Privado', 'Servicios', 'Educación', 'Educación Básica', 'Escuelas De Educación Secundaria Técnica Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611161', 'Escuelas De Educación Media Superior Del Sector Privado', 'Servicios', 'Educación', 'Educación Media', 'Escuelas De Educación Media Superior Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611162', 'Escuelas De Educación Media Superior Del Sector Publico', 'Servicios', 'Educación', 'Educación Media', 'Escuelas De Educación Media Superior Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611171', 'Escuelas Del Sector Privado Que Combinan Diversos Niveles De Educación', 'Servicios', 'Educación', 'Otros Educación', 'Escuelas Del Sector Privado Que Combinan Diversos Niveles De Educación', 12, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611172', 'Escuelas Del Sector Publico Que Combinan Diversos Niveles De Educación', 'Servicios', 'Educación', 'Otros Educación', 'Escuelas Del Sector Publico Que Combinan Diversos Niveles De Educación', 12, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611181', 'Escuelas Del Sector Privado De Educación Para Necesidades Especiales', 'Servicios', 'Educación', 'Otros Educación', 'Escuelas Del Sector Privado De Educación Para Necesidades Especiales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611182', 'Escuelas Del Sector Publico De Educación Para Necesidades Especiales', 'Servicios', 'Educación', 'Otros Educación', 'Escuelas Del Sector Publico De Educación Para Necesidades Especiales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611211', 'Escuelas De Educación Postbachillerato Del Sector Privado', 'Servicios', 'Educación', 'Educación Media', 'Escuelas De Educación Postbachillerato Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611212', 'Escuelas De Educación Postbachillerato Del Sector Publico', 'Servicios', 'Educación', 'Educación Media', 'Escuelas De Educación Postbachillerato Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611311', 'Escuelas De Educación Superior Del Sector Privado', 'Servicios', 'Educación', 'Educación Superior', 'Escuelas De Educación Superior Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611312', 'Escuelas De Educación Superior Del Sector Publico', 'Servicios', 'Educación', 'Educación Superior', 'Escuelas De Educación Superior Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611411', 'Escuelas Comerciales Y Secretariales Del Sector Privado', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas Comerciales Y Secretariales Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611412', 'Escuelas Comerciales Y Secretariales Del Sector Publico', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas Comerciales Y Secretariales Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611421', 'Escuelas De Computación Del Sector Privado', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Computación Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611422', 'Escuelas De Computación Del Sector Publico', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Computación Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611431', 'Escuelas Para La Capacitación De Ejecutivos Del Sector Privado', 'Servicios', 'Educación', 'Educación Superior', 'Escuelas Para La Capacitación De Ejecutivos Del Sector Privado', 6, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611432', 'Escuelas Para La Capacitación De Ejecutivos Del Sector Publico', 'Servicios', 'Educación', 'Educación Superior', 'Escuelas Para La Capacitación De Ejecutivos Del Sector Publico', 6, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611511', 'Escuelas Del Sector Privado Dedicadas A La Enseñanza De Oficios', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas Del Sector Privado Dedicadas A La Enseñanza De Oficios', 6, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611512', 'Escuelas Del Sector Publico Dedicadas A La Enseñanza De Oficios', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas Del Sector Publico Dedicadas A La Enseñanza De Oficios', 6, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611611', 'Escuelas De Arte Del Sector Privado', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Arte Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611612', 'Escuelas De Arte Del Sector Publico', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Arte Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611621', 'Escuelas De Deporte Del Sector Privado', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Deporte Del Sector Privado', 12, 'Otros')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611622', 'Escuelas De Deporte Del Sector Publico', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Deporte Del Sector Publico', 12, 'Otros')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611631', 'Escuelas De Idiomas Del Sector Privado', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Idiomas Del Sector Privado', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611632', 'Escuelas De Idiomas Del Sector Publico', 'Servicios', 'Educación', 'Educación Técnica', 'Escuelas De Idiomas Del Sector Publico', 6, 'Escuelas')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611691', 'Servicios De Profesores Particulares', 'Servicios', 'Educación', 'Educación Técnica', 'Servicios De Profesores Particulares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611698', 'Otros Servicios Educativos Proporcionados Por El Sector Privado', 'Servicios', 'Educación', 'Educación Técnica', 'Otros Servicios Educativos Proporcionados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611699', 'Otros Servicios Educativos Proporcionados Por El Sector Publico', 'Servicios', 'Educación', 'Educación Técnica', 'Otros Servicios Educativos Proporcionados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '611710', 'Servicios De Apoyo A La Educación', 'Servicios', 'Educación', 'Otros Educación', 'Servicios De Apoyo A La Educación', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621111', 'Consultorios De Medicina General Del Sector Privado', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Medicina General Del Sector Privado', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621112', 'Consultorios De Medicina General Del Sector Publico', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Medicina General Del Sector Publico', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621113', 'Consultorios De Medicina Especializada Del Sector Privado', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Medicina Especializada Del Sector Privado', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621114', 'Consultorios De Medicina Especializada Del Sector Publico', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Medicina Especializada Del Sector Publico', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621211', 'Consultorios Dentales Del Sector Privado', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios Dentales Del Sector Privado', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621212', 'Consultorios Dentales Del Sector Publico', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios Dentales Del Sector Publico', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621311', 'Consultorios De Quiropráctica Del Sector Privado', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Quiropráctica Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621312', 'Consultorios De Quiropráctica Del Sector Publico', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Quiropráctica Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621320', 'Consultorios De Optometría', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Optometría', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621331', 'Consultorios De Psicología Del Sector Privado', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Psicología Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621332', 'Consultorios De Psicología Del Sector Publico', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Psicología Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621341', 'Consultorios Del Sector Privado De Audiología Y De Terapia Ocupacional Física Y Del Lenguaje', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios Del Sector Privado De Audiología Y De Terapia Ocupacional Física Y Del Lenguaje', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621342', 'Consultorios Del Sector Publico De Audiología Y De Terapia Ocupacional Física Y Del Lenguaje', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios Del Sector Publico De Audiología Y De Terapia Ocupacional Física Y Del Lenguaje', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621391', 'Consultorios De Nutriólogos Y Dietistas Del Sector Privado', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Nutriólogos Y Dietistas Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621392', 'Consultorios De Nutriólogos Y Dietistas Del Sector Publico', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Consultorios De Nutriólogos Y Dietistas Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621398', 'Otros Consultorios Del Sector Privado Para El Cuidado De La Salud', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Otros Consultorios Del Sector Privado Para El Cuidado De La Salud', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621399', 'Otros Consultorios Del Sector Publico Para El Cuidado De La Salud', 'Servicios', 'Servicios Salud', 'Consultorios Médicos', 'Otros Consultorios Del Sector Publico Para El Cuidado De La Salud', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621411', 'Centros De Planificación Familiar Del Sector Privado', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Centros De Planificación Familiar Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621412', 'Centros De Planificación Familiar Del Sector Publico', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Centros De Planificación Familiar Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621511', 'Laboratorios Médicos Y De Diagnostico Del Sector Privado', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Laboratorios Médicos Y De Diagnostico Del Sector Privado', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621421', 'Centros Del Sector Privado De Atención Medica Externa Para Enfermos Mentales Y Adictos', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Centros Del Sector Privado De Atención Medica Externa Para Enfermos Mentales Y Adictos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621422', 'Centros Del Sector Publico De Atención Medica Externa Para Enfermos Mentales Y Adictos', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Centros Del Sector Publico De Atención Medica Externa Para Enfermos Mentales Y Adictos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621491', 'Otros Centros Del Sector Privado Para La Atención De Pacientes Que No Requieren Hospitalización', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Otros Centros Del Sector Privado Para La Atención De Pacientes Que No Requieren Hospitalización', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621492', 'Otros Centros Del Sector Publico Para La Atención De Pacientes Que No Requieren Hospitalización', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Otros Centros Del Sector Publico Para La Atención De Pacientes Que No Requieren Hospitalización', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621512', 'Laboratorios Médicos Y De Diagnostico Del Sector Publico', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Laboratorios Médicos Y De Diagnostico Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621610', 'Servicios De Enfermería A Domicilio', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios De Enfermería A Domicilio', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621910', 'Servicios De Ambulancias', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios De Ambulancias', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621991', 'Servicios De Bancos De Órganos Bancos De Sangre Y Otros Servicios Auxiliares Al Tratamiento Medico Prestados Por El Sector Privado', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios De Bancos De Órganos Bancos De Sangre Y Otros Servicios Auxiliares Al Tratamiento Medico Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '621992', 'Servicios De Bancos De Órganos Bancos De Sangre Y Otros Servicios Auxiliares Al Tratamiento Medico Prestados Por El Sector Publico', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios De Bancos De Órganos Bancos De Sangre Y Otros Servicios Auxiliares Al Tratamiento Medico Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '622111', 'Hospitales Generales Del Sector Privado', 'Servicios', 'Servicios Salud', 'Hospitales', 'Hospitales Generales Del Sector Privado', 9, 'Hospitales')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '622112', 'Hospitales Generales Del Sector Publico', 'Servicios', 'Servicios Salud', 'Hospitales', 'Hospitales Generales Del Sector Publico', 9, 'Hospitales')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '622211', 'Hospitales Psiquiátricos Y Para El Tratamiento Por Adicción Del Sector Privado', 'Servicios', 'Servicios Salud', 'Hospitales', 'Hospitales Psiquiátricos Y Para El Tratamiento Por Adicción Del Sector Privado', 9, 'Hospitales')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '622212', 'Hospitales Psiquiátricos Y Para El Tratamiento Por Adicción Del Sector Publico', 'Servicios', 'Servicios Salud', 'Hospitales', 'Hospitales Psiquiátricos Y Para El Tratamiento Por Adicción Del Sector Publico', 9, 'Hospitales')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '622311', 'Hospitales Del Sector Privado De Otras Especialidades Medicas', 'Servicios', 'Servicios Salud', 'Hospitales', 'Hospitales Del Sector Privado De Otras Especialidades Medicas', 9, 'Hospitales')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '622312', 'Hospitales Del Sector Publico De Otras Especialidades Medicas', 'Servicios', 'Servicios Salud', 'Hospitales', 'Hospitales Del Sector Publico De Otras Especialidades Medicas', 9, 'Hospitales')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623111', 'Residencias Del Sector Privado Con Cuidados De Enfermeras Para Enfermos Convalecientes En Rehabilitación Incurables Y Terminales', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Residencias Del Sector Privado Con Cuidados De Enfermeras Para Enfermos Convalecientes En Rehabilitación Incurables Y Terminales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624412', 'Guarderías Del Sector Publico', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Guarderías Del Sector Publico', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711111', 'Compañías De Teatro Del Sector Privado', 'Servicios', 'Entretenimiento', 'Cines Y Teatros', 'Compañías De Teatro Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711112', 'Compañías De Teatro Del Sector Publico', 'Servicios', 'Entretenimiento', 'Cines Y Teatros', 'Compañías De Teatro Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711121', 'Compañías De Danza Del Sector Privado', 'Servicios', 'Entretenimiento', 'Cines Y Teatros', 'Compañías De Danza Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623112', 'Residencias Del Sector Publico Con Cuidados De Enfermeras Para Enfermos Convalecientes En Rehabilitación Incurables Y Terminales', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Residencias Del Sector Publico Con Cuidados De Enfermeras Para Enfermos Convalecientes En Rehabilitación Incurables Y Terminales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623211', 'Residencias Del Sector Privado Para El Cuidado De Personas Con Problemas De Retardo Mental', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Residencias Del Sector Privado Para El Cuidado De Personas Con Problemas De Retardo Mental', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623212', 'Residencias Del Sector Publico Para El Cuidado De Personas Con Problemas De Retardo Mental', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Residencias Del Sector Publico Para El Cuidado De Personas Con Problemas De Retardo Mental', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623221', 'Residencias Del Sector Privado Para El Cuidado De Personas Con Problemas De Trastorno Mental Y Adicción', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Residencias Del Sector Privado Para El Cuidado De Personas Con Problemas De Trastorno Mental Y Adicción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623222', 'Residencias Del Sector Publico Para El Cuidado De Personas Con Problemas De Trastorno Mental Y Adicción', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Residencias Del Sector Publico Para El Cuidado De Personas Con Problemas De Trastorno Mental Y Adicción', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623311', 'Asilos Y Otras Residencias Del Sector Privado Para El Cuidado De Ancianos', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Asilos Y Otras Residencias Del Sector Privado Para El Cuidado De Ancianos', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623312', 'Asilos Y Otras Residencias Del Sector Publico Para El Cuidado De Ancianos', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Asilos Y Otras Residencias Del Sector Publico Para El Cuidado De Ancianos', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623991', 'Orfanatos Y Otras Residencias De Asistencia Social Del Sector Privado', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Orfanatos Y Otras Residencias De Asistencia Social Del Sector Privado', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '623992', 'Orfanatos Y Otras Residencias De Asistencia Social Del Sector Publico', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Orfanatos Y Otras Residencias De Asistencia Social Del Sector Publico', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624111', 'Servicios De Orientación Y Trabajo Social Para La Niñez Y La Juventud Prestados Por El Sector Privado', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Servicios De Orientación Y Trabajo Social Para La Niñez Y La Juventud Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624112', 'Servicios De Orientación Y Trabajo Social Para La Niñez Y La Juventud Prestados Por El Sector Publico', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Servicios De Orientación Y Trabajo Social Para La Niñez Y La Juventud Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624121', 'Centros Del Sector Privado Dedicados A La Atención Y Cuidado Diurno De Ancianos Y Discapacitados', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Centros Del Sector Privado Dedicados A La Atención Y Cuidado Diurno De Ancianos Y Discapacitados', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624122', 'Centros Del Sector Publico Dedicados A La Atención Y Cuidado Diurno De Ancianos Y Discapacitados', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Centros Del Sector Publico Dedicados A La Atención Y Cuidado Diurno De Ancianos Y Discapacitados', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624221', 'Refugios Temporales Comunitarios Del Sector Privado', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Refugios Temporales Comunitarios Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624222', 'Refugios Temporales Comunitarios Del Sector Publico', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Refugios Temporales Comunitarios Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624231', 'Servicios De Emergencia Comunitarios Prestados Por El Sector Privado', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios De Emergencia Comunitarios Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624411', 'Guarderías Del Sector Privado', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Guarderías Del Sector Privado', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624191', 'Agrupaciones De Autoayuda Para Alcohólicos Y Personas Con Otras Adicciones', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Agrupaciones De Autoayuda Para Alcohólicos Y Personas Con Otras Adicciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624198', 'Otros Servicios De Orientación Y Trabajo Social Prestados Por El Sector Privado', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Otros Servicios De Orientación Y Trabajo Social Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624199', 'Otros Servicios De Orientación Y Trabajo Social Prestados Por El Sector Publico', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Otros Servicios De Orientación Y Trabajo Social Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624211', 'Servicios De Alimentación Comunitarios Prestados Por El Sector Privado', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Servicios De Alimentación Comunitarios Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624212', 'Servicios De Alimentación Comunitarios Prestados Por El Sector Publico', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Servicios De Alimentación Comunitarios Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624232', 'Servicios De Emergencia Comunitarios Prestados Por El Sector Publico', 'Servicios', 'Servicios Salud', 'Centros De Atención Y Cuidado', 'Servicios De Emergencia Comunitarios Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624311', 'Servicios De Capacitación Para El Trabajo Prestados Por El Sector Privado Para Personas Desempleadas Subempleadas O Discapacitadas', 'Servicios', 'Servicios Salud', 'Servicios De Orientación', 'Servicios De Capacitación Para El Trabajo Prestados Por El Sector Privado Para Personas Desempleadas Subempleadas O Discapacitadas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '624312', 'Servicios De Capacitación Para El Trabajo Prestados Por El Sector Publico Para Personas Desempleadas Subempleadas O Discapacitadas', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios De Capacitación Para El Trabajo Prestados Por El Sector Publico Para Personas Desempleadas Subempleadas O Discapacitadas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711131', 'Cantantes Y Grupos Musicales Del Sector Privado', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Cantantes Y Grupos Musicales Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711132', 'Grupos Musicales Del Sector Publico', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Grupos Musicales Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711191', 'Otras Compañías Y Grupos De Espectáculos Artísticos Del Sector Privado', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Otras Compañías Y Grupos De Espectáculos Artísticos Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711192', 'Otras Compañías Y Grupos De Espectáculos Artísticos Del Sector Publico', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Otras Compañías Y Grupos De Espectáculos Artísticos Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711211', 'Deportistas Profesionales', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Deportistas Profesionales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711212', 'Equipos Deportivos Profesionales', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Equipos Deportivos Profesionales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711311', 'Promotores Del Sector Privado De Espectáculos Artísticos Culturales Deportivos Y Similares Que Cuentan Con Instalaciones Para Presentarlos', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Promotores Del Sector Privado De Espectáculos Artísticos Culturales Deportivos Y Similares Que Cuentan Con Instalaciones Para Presentarlos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713950', 'Boliches', 'Servicios', 'Entretenimiento', 'Juegos De Habilidad Y Azar', 'Boliches', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713991', 'Billares', 'Servicios', 'Entretenimiento', 'Juegos De Habilidad Y Azar', 'Billares', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713992', 'Clubes O Ligas De Aficionados', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Clubes O Ligas De Aficionados', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713998', 'Otros Servicios Recreativos Prestados Por El Sector Privado', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Otros Servicios Recreativos Prestados Por El Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713999', 'Otros Servicios Recreativos Prestados Por El Sector Publico', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Otros Servicios Recreativos Prestados Por El Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711312', 'Promotores Del Sector Publico De Espectáculos Artísticos Culturales Deportivos Y Similares Que Cuentan Con Instalaciones Para Presentarlos', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Promotores Del Sector Publico De Espectáculos Artísticos Culturales Deportivos Y Similares Que Cuentan Con Instalaciones Para Presentarlos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711320', 'Promotores De Espectáculos Artísticos Culturales Deportivos Y Similares Que No Cuentan Con Instalaciones Para Presentarlos', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Promotores De Espectáculos Artísticos Culturales Deportivos Y Similares Que No Cuentan Con Instalaciones Para Presentarlos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711410', 'Agentes Y Representantes De Artistas Deportistas Y Similares', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Agentes Y Representantes De Artistas Deportistas Y Similares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '711510', 'Artistas Escritores Y Técnicos Independientes', 'Servicios', 'Entretenimiento', 'Espectáculos Y Exhibiciones', 'Artistas Escritores Y Técnicos Independientes', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '712111', 'Museos Del Sector Privado', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Museos Del Sector Privado', 7, 'Cultura')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '712112', 'Museos Del Sector Publico', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Museos Del Sector Publico', 7, 'Cultura')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '712120', 'Sitios Históricos', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Sitios Históricos', 12, 'Cultura')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '712131', 'Jardines Botánicos Y Zoológicos Del Sector Privado', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Jardines Botánicos Y Zoológicos Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '712132', 'Jardines Botánicos Y Zoológicos Del Sector Publico', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Jardines Botánicos Y Zoológicos Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '712190', 'Grutas Parques Naturales Y Otros Sitios Del Patrimonio Cultural De La Nación', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Grutas Parques Naturales Y Otros Sitios Del Patrimonio Cultural De La Nación', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713111', 'Parques De Diversiones Y Temáticos Del Sector Privado', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Parques De Diversiones Y Temáticos Del Sector Privado', 7, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713112', 'Parques De Diversiones Y Temáticos Del Sector Publico', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Parques De Diversiones Y Temáticos Del Sector Publico', 7, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713113', 'Parques Acuáticos Y Balnearios Del Sector Privado', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Parques Acuáticos Y Balnearios Del Sector Privado', 7, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713114', 'Parques Acuáticos Y Balnearios Del Sector Publico', 'Servicios', 'Entretenimiento', 'Parques Y Museos', 'Parques Acuáticos Y Balnearios Del Sector Publico', 7, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713120', 'Casas De Juegos Electrónicos', 'Servicios', 'Entretenimiento', 'Juegos De Habilidad Y Azar', 'Casas De Juegos Electrónicos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713210', 'Casinos', 'Servicios', 'Entretenimiento', 'Juegos De Habilidad Y Azar', 'Casinos', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713291', 'Venta De Billetes De Lotería Pronósticos Deportivos Y Otros Boletos De Sorteo', 'Servicios', 'Entretenimiento', 'Juegos De Habilidad Y Azar', 'Venta De Billetes De Lotería Pronósticos Deportivos Y Otros Boletos De Sorteo', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713299', 'Otros Juegos De Azar', 'Servicios', 'Entretenimiento', 'Juegos De Habilidad Y Azar', 'Otros Juegos De Azar', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713910', 'Campos De Golf', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Campos De Golf', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713930', 'Marinas Turísticas', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Marinas Turísticas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713941', 'Clubes Deportivos Del Sector Privado', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Clubes Deportivos Del Sector Privado', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713942', 'Clubes Deportivos Del Sector Publico', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Clubes Deportivos Del Sector Publico', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713943', 'Centros De Acondicionamiento Físico Del Sector Privado', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Centros De Acondicionamiento Físico Del Sector Privado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '713944', 'Centros De Acondicionamiento Físico Del Sector Publico', 'Servicios', 'Entretenimiento', 'Clubes Deportivos Y Recreativos', 'Centros De Acondicionamiento Físico Del Sector Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721111', 'Hoteles Con Otros Servicios Integrados', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Hoteles Con Otros Servicios Integrados', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721112', 'Hoteles Sin Otros Servicios Integrados', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Hoteles Sin Otros Servicios Integrados', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721113', 'Moteles', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Moteles', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721190', 'Cabañas Villas Y Similares', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Cabañas Villas Y Similares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721210', 'Campamentos Y Albergues Recreativos', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Campamentos Y Albergues Recreativos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721311', 'Pensiones Y Casas De Huéspedes', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Pensiones Y Casas De Huéspedes', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '721312', 'Departamentos Y Casas Amueblados Con Servicios De Hotelería', 'Servicios', 'Transporte Y Alojamiento', 'Servicio De Hospedaje Temporal', 'Departamentos Y Casas Amueblados Con Servicios De Hotelería', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722110', 'Restaurantes Con Servicio Completo', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Restaurantes Con Servicio Completo', 12, 'Alimentos')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722211', 'Restaurantes De Autoservicio', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Restaurantes De Autoservicio', 12, 'Alimentos')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722212', 'Restaurantes De Comida Para Llevar', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Restaurantes De Comida Para Llevar', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722219', 'Otros Restaurantes Con Servicio Limitado', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Otros Restaurantes Con Servicio Limitado', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722310', 'Servicios De Comedor Para Empresas E Instituciones', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Servicios De Comedor Para Empresas E Instituciones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722320', 'Servicios De Preparación De Alimentos Para Ocasiones Especiales', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Servicios De Preparación De Alimentos Para Ocasiones Especiales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722330', 'Servicios De Preparación De Alimentos En Unidades Móviles', 'Servicios', 'Servicios De Alimentos Y Bebidas', 'Otros Servicios De Alimentos Y Bebidas', 'Servicios De Preparación De Alimentos En Unidades Móviles', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722411', 'Centros Nocturnos Discotecas Y Similares', 'Servicios', 'Entretenimiento', 'Otros Entretenimiento', 'Centros Nocturnos Discotecas Y Similares', 12, 'Entretenimiento')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '722412', 'Bares Cantinas Y Similares', 'Servicios', 'Entretenimiento', 'Otros Entretenimiento', 'Bares Cantinas Y Similares', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811111', 'Reparación Mecánica En General De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Reparación Mecánica En General De Automóviles Y Camiones', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811112', 'Reparación Del Sistema Eléctrico De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Reparación Del Sistema Eléctrico De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811113', 'Rectificación De Partes De Motor De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Rectificación De Partes De Motor De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811114', 'Reparación De Transmisiones De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Reparación De Transmisiones De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811115', 'Reparación De Suspensiones De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Reparación De Suspensiones De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811116', 'Alineación Y Balanceo De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Alineación Y Balanceo De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811119', 'Otras Reparaciones Mecánicas De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Otras Reparaciones Mecánicas De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811121', 'Hojalatería Y Pintura De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Hojalatería Y Pintura De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811122', 'Tapicería De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Tapicería De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811129', 'Instalación De Cristales Y Otras Reparaciones A La Carrocería De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Instalación De Cristales Y Otras Reparaciones A La Carrocería De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811191', 'Reparación Menor De Llantas', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Reparación Menor De Llantas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811192', 'Lavado Y Lubricado De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Lavado Y Lubricado De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811199', 'Otros Servicios De Reparación Y Mantenimiento De Automóviles Y Camiones', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Otros Servicios De Reparación Y Mantenimiento De Automóviles Y Camiones', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811211', 'Reparación Y Mantenimiento De Equipo Electrónico De Uso Domestico', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación Y Mantenimiento De Equipo Electrónico De Uso Domestico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811219', 'Reparación Y Mantenimiento De Otro Equipo Electrónico Y De Equipo De Precisión', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación Y Mantenimiento De Otro Equipo Electrónico Y De Equipo De Precisión', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811311', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Agropecuario Y Forestal', 'Servicios', 'Servicios Especializados', 'Reparacion Maquinaria Equipo', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Agropecuario Y Forestal', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811312', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Industrial', 'Servicios', 'Servicios Especializados', 'Reparacion Maquinaria Equipo', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Industrial', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811313', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Para Mover Levantar Y Acomodar Materiales', 'Servicios', 'Servicios Especializados', 'Reparacion Maquinaria Equipo', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Para Mover Levantar Y Acomodar Materiales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811314', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Comercial Y De Servicios', 'Servicios', 'Servicios Especializados', 'Reparacion Maquinaria Equipo', 'Reparación Y Mantenimiento De Maquinaria Y Equipo Comercial Y De Servicios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811410', 'Reparación Y Mantenimiento De Aparatos Eléctricos Para El Hogar Y Personales', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación Y Mantenimiento De Aparatos Eléctricos Para El Hogar Y Personales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811420', 'Reparación De Tapicería De Muebles Para El Hogar', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación De Tapicería De Muebles Para El Hogar', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811430', 'Reparación De Calzado Y Otros Artículos De Piel Y Cuero', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación De Calzado Y Otros Artículos De Piel Y Cuero', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811491', 'Cerrajerías', 'Servicios', 'Servicios Especializados', 'Otros Servicios Especializados', 'Cerrajerías', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811492', 'Reparación Y Mantenimiento De Motocicletas', 'Servicios', 'Servicios Especializados', 'Mantenimiento De Vehículos', 'Reparación Y Mantenimiento De Motocicletas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811493', 'Reparación Y Mantenimiento De Bicicletas', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación Y Mantenimiento De Bicicletas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812110', 'Salones Y Clínicas De Belleza Y Peluquerías', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Salones Y Clínicas De Belleza Y Peluquerías', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812120', 'Baños Públicos', 'Servicios', 'Servicios Especializados', 'Otros Servicios Especializados', 'Baños Públicos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812130', 'Sanitarios Públicos Y Bolerias', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Sanitarios Públicos Y Bolerias', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812210', 'Lavanderías Y Tintorerías', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Lavanderías Y Tintorerías', 12, 'Servicios')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '811499', 'Reparación Y Mantenimiento De Otros Artículos Para El Hogar Y Personales', 'Servicios', 'Servicios Especializados', 'Reparacion  Bienes De Consumo', 'Reparación Y Mantenimiento De Otros Artículos Para El Hogar Y Personales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812310', 'Servicios Funerarios', 'Servicios', 'Servicios Salud', 'Otros Servicios De Salud', 'Servicios Funerarios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812320', 'Administración De Cementerios', 'Servicios', 'Servicios Especializados', 'Seguridad Higiene Y Mantenimiento', 'Administración De Cementerios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812410', 'Estacionamientos Y Pensiones Para Vehículos Automotores', 'Servicios', 'Transporte Y Alojamiento', 'Almacenamiento Mensajería Y Mudanza', 'Estacionamientos Y Pensiones Para Vehículos Automotores', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812910', 'Servicios De Revelado E Impresión De Fotografías', 'Servicios', 'Medios De Comunicación Y Telecomunicaciones', 'Producción Visual Y Audio', 'Servicios De Revelado E Impresión De Fotografías', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '812990', 'Otros Servicios Personales', 'Servicios', 'Servicios Especializados', 'Otros Servicios Especializados', 'Otros Servicios Personales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813110', 'Asociaciones Organizaciones Y Cámaras De Productores Comerciantes Y Prestadores De Servicios', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Organizaciones Y Cámaras De Productores Comerciantes Y Prestadores De Servicios', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813120', 'Asociaciones Y Organizaciones Laborales Y Sindicales', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Y Organizaciones Laborales Y Sindicales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813130', 'Asociaciones Y Organizaciones De Profesionistas', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Y Organizaciones De Profesionistas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813140', 'Asociaciones Regulatorias De Actividades Recreativas', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Regulatorias De Actividades Recreativas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813210', 'Asociaciones Y Organizaciones Religiosas', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Y Organizaciones Religiosas', 10, 'Otros')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813220', 'Asociaciones Y Organizaciones Políticas', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Y Organizaciones Políticas', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '813230', 'Asociaciones Y Organizaciones Civiles', 'Servicios', 'Organizaciones No Gubernamentales', 'Asociaciones Civiles Y Religiosas', 'Asociaciones Y Organizaciones Civiles', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931110', 'Órganos Legislativos', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Órganos Legislativos', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931210', 'Administración Publica En General', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Administración Publica En General', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931310', 'Regulación Y Fomento Del Desarrollo Económico', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Regulación Y Fomento Del Desarrollo Económico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931410', 'Impartición De Justicia Y Mantenimiento De La Seguridad Y El Orden Publico', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Impartición De Justicia Y Mantenimiento De La Seguridad Y El Orden Publico', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931510', 'Regulación Y Fomento De Actividades Para Mejorar Y Preservar El Medio Ambiente', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Regulación Y Fomento De Actividades Para Mejorar Y Preservar El Medio Ambiente', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931610', 'Actividades Administrativas De Instituciones De Bienestar Social', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Actividades Administrativas De Instituciones De Bienestar Social', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931710', 'Relaciones Exteriores', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Relaciones Exteriores', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '931810', 'Actividades De Seguridad Nacional', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Actividades De Seguridad Nacional', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '932110', 'Organismos Internacionales', 'Servicios', 'Gobierno Y Servicios Primarios', 'Oficinas De Gobierno Y Poderes', 'Organismos Internacionales', 12, 'Servicios > 50')",

					"insert into catalogoActividades (clase_act, desc_act, act_econ, tipo_act, sub_act, detalle_act, icono, clasificacion)values( '999999', 'No Especificado', 'No Específico', 'Sin Clasificación', 'Sin Descripción', 'No Especificado', 13, 'Otros > 30')", 

				);

				 	for ( $i = 0; $i < count($arrayCatalogoActividades); $i++ )

						@mysql_query ( $arrayCatalogoActividades[$i] );

				// Se Agrega el usuario rosariovalencia como administrador en todos los dominios

				$resultado = mysql_query ( 'select idusuario from usuario where idusuario = "rosariovalencia"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( "insert into usuario (idusuario, contrasena, tipo, generador) values ( 'rosariovalencia', 'rv4l3nc14_1', 'Supervisor', 1 )" ) or die ( "Error: " . mysql_error() );

						// Se Agrega el usuario del programador en todos los dominios

				$resultado = mysql_query ( 'select idusuario from usuario where idusuario = "Ara Garduno"' );

					if ( mysql_num_rows ( $resultado ) == 0 )

						mysql_query ( "insert into usuario (idusuario, contrasena, tipo, generador) values ( 'admin.ara', 'garduno01', 'Programador', 0 )" ) or die ( "Error: " . mysql_error() );

				// Se agrega la pagina de carrito y el derecho de liberar

				$resultado = mysql_query ( 'select * from paginas where nombre = "Carrito"' );

					if ( mysql_num_rows ( $resultado ) != 0 ){

						mysql_query ( 'insert into paginas (idpagina, nombre, descripcion) values(default, "Carrito", "")' );

						$idpagina = mysql_insert_id();

						mysql_query ( 'insert into derechos (idpagina, nombre, descripcion) values ( ' . $idpagina . ', "Liberar", "" )' );

					}

				// Centro America

				// Derechos para parte de negocios

				$resultado = mysql_query ( 'select idpagina from paginas where nombre = "Puntos"' );

					if ( mysql_num_rows ( $resultado ) == 1 ){

						$datos = mysql_fetch_assoc( $resultado );

						$idpagina = $datos['idpagina'];

						@mysql_query ( "insert into derechos( idpagina, nombre )values( $idpagina, 'Negocios' );" );

						@mysql_query ( "insert into derechos( idpagina, nombre )values( $idpagina, 'NegociosCompras' );" );

						@mysql_query ( "insert into derechos( idpagina, nombre )values( $idpagina, 'NegociosInfraExt' );" );

						@mysql_query ( "insert into derechos( idpagina, nombre )values( $idpagina, 'NegociosInfraInt' );" );

						@mysql_query ( "insert into derechos( idpagina, nombre )values( $idpagina, 'NegociosVentas' );" );

					}

				@mysql_query ( "insert into derechos( idpagina, nombre )values( 27, 'Negocios' );" );

				// Tablas

				mysql_query( 'CREATE  TABLE IF NOT EXISTS negocios (

								  idPunto INT NOT NULL ,

								  folio INT NULL ,

								  fechaLevantamiento DATETIME NULL ,

								  fechaActualizacion DATETIME NULL ,

								  tipoNegocio VARCHAR(50) NULL ,

								  statusNegocio VARCHAR(50) NULL ,

								  statusLevantamiento VARCHAR(50) NULL ,

								  giro VARCHAR(50) NULL ,

								  antiguedad VARCHAR(50) NULL ,

								  clase VARCHAR(50) NULL ,

								  horario VARCHAR(100) NULL,

								  totalInventario DECIMAL(10,2) NULL ,

								  totalEstimados DECIMAL(10,2) NULL ,

								  procesoVtaIni VARCHAR(100) NULL ,

								  procesoVtaSegui VARCHAR(100) NULL ,

								  procesoVtaFin VARCHAR(100) NULL ,

								  tipoComprador VARCHAR(50) NULL ,

								  totalCompras DECIMAL(10,2) NULL ,

								  encargado VARCHAR(200) NULL ,

								  email VARCHAR(100) NULL ,

								  procesoCpraIni VARCHAR(100) NULL ,

								  procesoCpraSegui VARCHAR(100) NULL ,

								  procesoCpraFin VARCHAR(100) NULL ,

								  servicioDomicilio TINYINT(1) NULL ,

								  ventaMostrador TINYINT(1) NULL ,

								  distribuyeOtros TINYINT(1) NULL ,

								  entregado TINYINT(1) NULL ,

								  retirado TINYINT(1) NULL ,

								  cheque TINYINT(1) NULL ,

								  credito TINYINT(1) NULL ,

								  contado TINYINT(1) NULL ,

								  transBancaria TINYINT(1) NULL ,

								  efectivo TINYINT(1) NULL ,

								  observaciones VARCHAR(100) NULL ,

								  PRIMARY KEY (idPunto) ,

								  INDEX fk_negocios_puntos (idPunto ASC) ,

								  CONSTRAINT fk_negocios_puntos

									FOREIGN KEY (idPunto)

									REFERENCES puntos (idpunto)

									ON DELETE CASCADE

									ON UPDATE CASCADE)

								ENGINE = InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				mysql_query( 'CREATE  TABLE IF NOT EXISTS negociosVentas (

								  idPunto INT(11) NOT NULL ,

								  idVentas INT(11) NOT NULL AUTO_INCREMENT ,

								  categoria VARCHAR(100) NULL ,

								  marca VARCHAR(100) NULL ,

								  usoProducto VARCHAR(100) NULL ,

								  proveedor VARCHAR(100) NULL ,

								  precioUnitario DECIMAL(10,2) NULL ,

								  estimadosVtaMensual DECIMAL(10,2) default "0.00",

								  precioInventario DECIMAL(10,2) default "0.00",

								  unidades VARCHAR(20) NULL ,

								  CAMPO6 VARCHAR(20) NULL ,

								  CAMPO7 VARCHAR(20) NULL ,

								  CAMPO8 VARCHAR(20) NULL ,

								  CAMPO9 VARCHAR(20) NULL ,

								  CAMPO10 VARCHAR(20) NULL ,

								  PRIMARY KEY (idVentas, idPunto) ,

								  INDEX fk_puntos_negociosVentas (idPunto ASC) ,

								  CONSTRAINT fk_puntos_negociosVentas

									FOREIGN KEY (idPunto)

									REFERENCES puntos (idpunto)

									ON DELETE CASCADE

									ON UPDATE CASCADE)

								ENGINE = InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				mysql_query( 'CREATE  TABLE IF NOT EXISTS negociosCompras (

								  idPunto INT(11) NOT NULL ,

								  idCompras INT NOT NULL AUTO_INCREMENT ,

								  categoria VARCHAR(100) NULL ,

								  marca VARCHAR(100) NULL ,

								  proveedor VARCHAR(100) NULL ,

								  tipoProveedor VARCHAR(20) NULL ,

								  precioUnitario DECIMAL(10,8) NULL ,

								  CAMPO1 VARCHAR(20) NULL ,

								  CAMPO2 VARCHAR(20) NULL ,

								  CAMPO3 VARCHAR(20) NULL ,

								  CAMPO4 VARCHAR(20) NULL ,

								  CAMPO5 VARCHAR(20) NULL ,

								  PRIMARY KEY (idCompras, idPunto) ,

								  INDEX fk_puntos_negociosCompras (idPunto ASC) ,

								  CONSTRAINT fk_puntos_negociosCompras

									FOREIGN KEY (idPunto)

									REFERENCES puntos (idpunto)

									ON DELETE CASCADE

									ON UPDATE CASCADE)

								ENGINE = InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				mysql_query( 'CREATE  TABLE IF NOT EXISTS negociosInfraExt (

								  idPunto INT(11) NOT NULL ,

								  idInfraExt INT NOT NULL AUTO_INCREMENT ,

								  tipo VARCHAR(50) NULL ,

								  descripcion VARCHAR(200) NULL ,

								  dimension VARCHAR(50) NULL ,

								  CAMPO11 VARCHAR(20) NULL ,

								  CAMPO12 VARCHAR(20) NULL ,

								  CAMPO13 VARCHAR(20) NULL ,

								  CAMPO14 VARCHAR(20) NULL ,

								  CAMPO15 VARCHAR(20) NULL ,

								  PRIMARY KEY (idInfraExt, idPunto) ,

								  INDEX fk_puntos_negociosInfraExt (idPunto ASC) ,

								  CONSTRAINT fk_puntos_negociosInfraExt

									FOREIGN KEY (idPunto)

									REFERENCES puntos (idpunto)

									ON DELETE CASCADE

									ON UPDATE CASCADE)

								ENGINE = InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				mysql_query( 'CREATE  TABLE IF NOT EXISTS negociosInfraInt (

								  idPunto INT(11) NOT NULL ,

								  idInfraInt INT NOT NULL AUTO_INCREMENT ,

								  nombre VARCHAR(200) NULL ,

								  tamano VARCHAR(50) NULL ,

								  cantidad VARCHAR(20) NULL ,

								  PRIMARY KEY (idInfraInt, idPunto) ,

								  INDEX fk_puntos_negociosInfraInt (idPunto ASC) ,

								  CONSTRAINT fk_puntos_negociosInfraInt

									FOREIGN KEY (idPunto)

									REFERENCES puntos (idpunto)

									ON DELETE CASCADE

									ON UPDATE CASCADE)

								ENGINE = InnoDB DEFAULT CHARSET=utf8;' ) or die ( 'Error al crear la tabla: ' . mysql_error() );

				// ========================================================================================================================

				// *****************  ESTAS TIENEN QUE SER LAS ULTIMAS INSTRUCCIONES DEL SCRIPT DE ACTUALIZACION  *************************

				// ========================================================================================================================

				// ACTUALIZACION DE LA VERSION DEL SCRIPT EN LA TABLA DEL SISTEMA

				$resultado = mysql_query ( 'select * from sistema' );

					if ( mysql_num_rows( $resultado ) == 0 )

						mysql_query ( "insert into sistema(versionSistema, versionScript) values('$versionSistema', '$versionScript')" );

				mysql_query ( "update sistema set versionScript = '$versionScript'" ) or die ( 'Error: ' . mysql_error() );

				// ========================================================================================================================

			}

	}

}



function dameIdsPorAlias( $alias ){

	if ( conectar() ){

		$consulta = "select idCategoria from categorias where ";

		$where = "";

		$buscar = explode ( '|', $alias );

			for ( $i = 0; $i < count($buscar); $i++ ){

					if ( $buscar[$i] != "" )

						$where .= ( $where != "" ? " or " : "" ) . "(alias like '%{$buscar[$i]}%') or (descripcion like '%{$buscar[$i]}%')";

			}

		$consulta .= $where;

		$consulta .= " union select distinct(concat(p.idPregunta, '$', e.idEtiqueta)) idCategoria

							 from preguntas p, etiqueta e where p.idpregunta = e.idpregunta and ";

		$consulta .= $where;

		$ids       = "";

		$resultado = mysql_query ( $consulta ) or die ( $consulta );

			while ( $datos = mysql_fetch_assoc( $resultado ) ){

				$ids .= ( $ids != "" ? "|" : "" ) . $datos['idCategoria'];

			}

		return ( $ids == "" ? "-1" : $ids );

	}

}



function esGenerador (){

	$usuario = $_SESSION['user'];

	$consulta = 'select generador from usuario where idusuario = "' . $usuario . '"';

	$resultado = mysql_query ( $consulta );

	$datos = mysql_fetch_assoc( $resultado );

	return ($datos['generador'] == 1 ? true : false );

}



function getUsuariosSubPoligonos(){

	$usuario = $_SESSION['user'];

	$consulta = 'select u.idusuario, concat(pr.apellidoPaterno, " ", pr.apellidoMaterno, " ", pr.nombre) nombre ' .

				'from usuario u, usuarioxpersona up, personarepositorio pr ' .

				'where u.idusuario = up.idusuario and up.idpersonaRepositorio = pr.idpersonaRepositorio and ' .

	 			'u.idusuario <> "' . $usuario . '" and u.grupoSubPoligono = (select grupoSubPoligono from usuario where idusuario = "' . $usuario . '")';

	$resultado = mysql_query ( $consulta ) or die ( $consulta );

	$regresa = '<option value=""></option>';

		while ( $datos = mysql_fetch_assoc ( $resultado ) ){

			$regresa .= '<option value="' . $datos['idusuario'] . '">(' . $datos['idusuario'] . ') ' . $datos['nombre'] . '</option>';

		}

	return $regresa;

}



function getSubPoligonosDelUsuarioActual($idCompra){

	$usuario = $_SESSION['user'];

	$consulta = 'select idSubPoligono, DATE_FORMAT(fecha, "%d/%m/%Y %h:%i:%s %p" ) fecha, nombre from subPoligonos where idUsuario = "' . $usuario . '" and idCompra = ' . $idCompra;

	$resultado = mysql_query ( $consulta ) or die ( $consulta . '; Message: ' . mysql_error() );

	$regresa = '<option value="-1">NINGUNO</option>';

		while ( $datos = mysql_fetch_assoc ( $resultado ) ){

			$regresa .= '<option value="' . $datos [ 'idSubPoligono' ] . '">' . $datos [ 'fecha' ] . ' - ' . $datos[ 'nombre' ] . '</option>';

		}

	return $regresa;

}



function getCategoriaRespuesta ($idPregunta){

	// tienes que sacar la ruta de la respuesta

    $consulta = 'select idcategoria, pregunta from preguntas where idpregunta = ' . $idPregunta;

    $resultado = mysql_query($consulta) or die ($consulta);

        if (mysql_num_rows($resultado) > 0){

            $row = mysql_fetch_assoc($resultado);

            $consulta = 'select idcategoria, descripcion, categoriaPadre from categorias';

            $resultado = mysql_query($consulta);

            $datos = mysql_fetch_assoc($resultado);

            $idcategoria = $row['idcategoria'];

            $ruta = $row['pregunta'] . ',';

                while ( $idcategoria != null && $datos = mysql_fetch_assoc($resultado) ){

                        if ($idcategoria == $datos['idcategoria']){

                            $ruta .= $datos['descripcion'] . ',';

                            $idcategoria = ($datos['categoriaPadre'] == null || $datos['categoriaPadre'] == '' ? null : $datos['categoriaPadre'] );

                            mysql_data_seek($resultado, 0);

                        }

                }

//            die ( 'ruta = ' . $ruta );    

            return $ruta;

        }else{

            return "";

        }

    

}



function panelControl ( $usuario, $cmd, $nota ){

	//$geo = new geoLocateIp();

	//$ip = $geo->getIpAdress();

	//$info = $geo->getLocationFromIp();

	$ip = "NoResuelto";

	$info['City'] = '';

	// Comandos

	/*

			1(2,		cuantas personas entran y de donde son.... revisar el sacar el lugar de donde es por la ip

			3)			cuantos usuario se logean

			4			cuantas personas se registran

			5 			cuantas visitas tiene el chat

			6			consultas a la base de datos

			7			consultas gratuitas

			8			consulta pagadas

			9 			consultas liberadas

	*/

	switch ( $cmd ){

		case ( 1 ):{

			// se revisa si la ip ya existe no se agrega otro registro, se considerara usuario por ip

			$consulta = 'select ip from panelcontrol where ip = "' . $ip . '"';

			$resultado = mysql_query ( $consulta ) or die ( $consulta );

				if ( mysql_num_rows( $resultado ) == 0 ){

					$consulta = 'insert into panelcontrol(fecha, usuario, ip, ipciudad, cmd, nota)values( now(), "' . $usuario . '", "' . $ip . '", "' . $info['City'] . '", ' . $cmd . ', "' . $nota . '" );';

					mysql_query ( $consulta ) or die ( $consulta );

				}

			break;

		}

		case ( 5 ):{

			// se contara solo una visita por usuario al dia

			$consulta = 'select ip from panelcontrol where usuario = "' . $usuario . '" and extract( YEAR_MONTH FROM fecha) = extract( YEAR_MONTH FROM now()) and extract( DAY FROM fecha) = extract( DAY FROM now())';

			$resultado = mysql_query ( $consulta ) or die ( $consulta );

				if ( mysql_num_rows ( $resultado ) == 0 ){

					$consulta = 'insert into panelcontrol(fecha, usuario, ip, ipciudad, cmd, nota)values( now(), "' . $usuario . '", "' . $ip . '", "' . $info['City'] . '", ' . $cmd . ', "' . $nota . '" );';

					mysql_query ( $consulta ) or die ( $consulta );

				}

		}

		default:{

			$consulta = 'insert into panelcontrol(fecha, usuario, ip, ipciudad, cmd, nota)values( now(), "' . $usuario . '", "' . $ip . '", "' . $info['City'] . '", ' . $cmd . ', "' . $nota . '" );';

			mysql_query ( $consulta ) or die ( $consulta );

			break;

		}

	}

}



function esCorporativo() {

    //return true;

    if ((stripos($_SERVER["HTTP_HOST"], 'pruebas.') === 0) ||

            (stripos($_SERVER["HTTP_HOST"], 'localhost') === 0) ||

            (stripos($_SERVER["HTTP_HOST"], 'censosmkd.com') === 0) ||

			(stripos($_SERVER["HTTP_HOST"], 'www.oomovil.com.mx') === 0) ||

            (stripos($_SERVER["HTTP_HOST"], '﻿10.0.32.202') === 0)) {

        return false;

    } else {

        return true;

    }

}



function esPyme() {

    //return true;

    if (stripos($_SERVER["HTTP_HOST"], 'pyme.') === 0)

        return true;

    else

        return false;

}



function esRoalCom() {

    if (stripos($_SERVER["HTTP_HOST"], 'roalcom.') === 0)

        return true;

    else

        return false;

}



function esAdministrador() {

//    if (_derechos($_SESSION ['user'], 29) != '')

	$der = _derechos ( $_SESSION['user'], 29 );

	if( $der[0] != '' )

        return true;

    else

        return false;

}



// @Isra

function conectar() {

    // -----------------------------------------------------------------

    //			   SERVIDOR  -  USUARIO - PASS - BD

    // -----------------------------------------------------------------

    if (!(stripos(php_uname('n'), 'Zanatta') === false)) {

        $login_database = array('localhost', 'root', 'root', 'inmega');

    }

    else if ( ( stripos($_SERVER["HTTP_HOST"], 'localhost') === 0 ) || ( stripos($_SERVER['HTTP_HOST'], 'www.oomovil.com.mx' ) === 0 ) || ( stripos($_SERVER['HTTP_HOST'], '192.168.32.4' ) === 0 ) ) {

          $login_database = array('localhost:8889', 'root', 'root', 'inmega');

//        $login_database = array('localhost', 'root', 'mysql', 'inmega');

    	}else {

			if (esCorporativo()) {

				$SAux = $_SERVER["HTTP_HOST"];

				$tmp = explode('.', $SAux);

				$SAux = 'censosmk_' . $tmp[0];

				//die ('base de datos:'.$SAux);

				//$login_database = array('localhost', 'root', 'deimosx00', $SAux);

				$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', $SAux);

			}else{

				if (stripos($_SERVER["HTTP_HOST"], 'pruebas') === false) {

				//	$login_database = array('localhost', 'root', 'deimosx00', 'censosmk_db');

					$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', 'censosmk_db');

				}else{

					//$login_database = array('localhost', 'root', 'deimosx00', 'censosmk_pruebas');

					$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', 'censosmk_pruebas');

				}

			}

    }

    // $login_database[3];

    //echo $_SERVER["HTTP_HOST"];

    $link = @mysql_connect($login_database[0], $login_database[1], $login_database[2]) or die("<font face=Verdana size=4 color=#004080>MENSAJE DE ACCESO AL SERVICIO<br><br><small>Su computadora no ha podido conectarse apropiadamente al servicio.<br><br>Le sugerimos revisar las siguientes posibles causas.<br><br>Dirección Incorrecta<br>Para ingresar a la página, NO es indispensable teclear la dirección con usando los términos <b>“www”</b> o <b>“http”</b>. Unicamente ingrese indicando el nombre de la página (ej. censosmkd.com).<br><br>Limpieza de Caché<br>Es recomendable ejecutar una limpieza de la memoria Caché de su navegador con cierta periodicidad debido a que se van desarrollando actualizaciones del sistema y su computadora puede estar buscando el acceso mediante un archivo de complemento no vigente. Le sugerimos limpiar el historial de navegación de internet, incluyendo los archivos Cookies.<br><br>Complementos<br>Debe asegurarse que su navegador permita el uso de Cookies y la ejecución de JavaScript ya que son indispensables para el uso adecuado de la herramienta.<br><br>Navegadores Recomendados<br>La página funciona con mayor agilidad y precisión con los siguientes navegadores:<br>Google Chrome<br>Safari<br>Internet Explorer<br>FireFox<br><br><br>En caso de que siga teniendo dificultades para acceder apropiadamente al sitio, le recomendamos contactarse al número 01800-MICENSO (01800-6423676) o bien mediante el correo electrónico <a href=\"mailto:contacto@censosmkd.com\">contacto@censosmkd.com</a></small></font><br>" . mysql_error());

    @mysql_select_db($login_database[3], $link) or die('<font face=Verdana size=4 color=#004080>MENSAJE DE ACCESO AL SERVICIO<br><br><small>Su computadora no ha podido conectarse apropiadamente al servicio.<br><br>Le sugerimos revisar las siguientes posibles causas.<br><br>Dirección Incorrecta<br>Para ingresar a la página, NO es indispensable teclear la dirección con usando los términos <b>“www”</b> o <b>“http”</b>. Unicamente ingrese indicando el nombre de la página (ej. censosmkd.com).<br><br>Limpieza de Caché<br>Es recomendable ejecutar una limpieza de la memoria Caché de su navegador con cierta periodicidad debido a que se van desarrollando actualizaciones del sistema y su computadora puede estar buscando el acceso mediante un archivo de complemento no vigente. Le sugerimos limpiar el historial de navegación de internet, incluyendo los archivos Cookies.<br><br>Complementos<br>Debe asegurarse que su navegador permita el uso de Cookies y la ejecución de JavaScript ya que son indispensables para el uso adecuado de la herramienta.<br><br>Navegadores Recomendados<br>La página funciona con mayor agilidad y precisión con los siguientes navegadores:<br>Google Chrome<br>Safari<br>Internet Explorer<br>FireFox<br><br><br>En caso de que siga teniendo dificultades para acceder apropiadamente al sitio, le recomendamos contactarse al número 01800-MICENSO (01800-6423676) o bien mediante el correo electrónico <a href="mailto:contacto@censosmkd.com">contacto@censosmkd.com</a></small></font><br>' . mysql_error());

    @mysql_query("SET NAMES 'utf8'");

    return true;

}
function conectar2() {

    // -----------------------------------------------------------------

    //			   SERVIDOR  -  USUARIO - PASS - BD

    // -----------------------------------------------------------------

    if (!(stripos(php_uname('n'), 'Zanatta') === false)) {

        $login_database = array('localhost', 'root', 'root', 'inmega');

    }

    else if ( ( stripos($_SERVER["HTTP_HOST"], 'localhost') === 0 ) || ( stripos($_SERVER['HTTP_HOST'], 'www.oomovil.com.mx' ) === 0 ) || ( stripos($_SERVER['HTTP_HOST'], '192.168.32.4' ) === 0 ) ) {

        $login_database = array('localhost:8889', 'root', 'root', 'inmega');

//        $login_database = array('localhost', 'root', 'mysql', 'inmega');

    	}else {

			if (esCorporativo()) {

				$SAux = $_SERVER["HTTP_HOST"];

				$tmp = explode('.', $SAux);

				$SAux = 'censosmk_fa1';

				//die ('base de datos:'.$SAux);

				//$login_database = array('localhost', 'root', 'deimosx00', $SAux);

				$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', $SAux);

			}else{

				if (stripos($_SERVER["HTTP_HOST"], 'pruebas') === false) {

				//	$login_database = array('localhost', 'root', 'deimosx00', 'censosmk_db');

					$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', 'censosmk_fa1');

				}else{

					//$login_database = array('localhost', 'root', 'deimosx00', 'censosmk_pruebas');

					$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', 'censosmk_fa1');

				}

			}

    }

    // $login_database[3];

    //echo $_SERVER["HTTP_HOST"];

    $link = @mysql_connect($login_database[0], $login_database[1], $login_database[2]) or die("<font face=Verdana size=4 color=#004080>MENSAJE DE ACCESO AL SERVICIO<br><br><small>Su computadora no ha podido conectarse apropiadamente al servicio.<br><br>Le sugerimos revisar las siguientes posibles causas.<br><br>Dirección Incorrecta<br>Para ingresar a la página, NO es indispensable teclear la dirección con usando los términos <b>“www”</b> o <b>“http”</b>. Unicamente ingrese indicando el nombre de la página (ej. censosmkd.com).<br><br>Limpieza de Caché<br>Es recomendable ejecutar una limpieza de la memoria Caché de su navegador con cierta periodicidad debido a que se van desarrollando actualizaciones del sistema y su computadora puede estar buscando el acceso mediante un archivo de complemento no vigente. Le sugerimos limpiar el historial de navegación de internet, incluyendo los archivos Cookies.<br><br>Complementos<br>Debe asegurarse que su navegador permita el uso de Cookies y la ejecución de JavaScript ya que son indispensables para el uso adecuado de la herramienta.<br><br>Navegadores Recomendados<br>La página funciona con mayor agilidad y precisión con los siguientes navegadores:<br>Google Chrome<br>Safari<br>Internet Explorer<br>FireFox<br><br><br>En caso de que siga teniendo dificultades para acceder apropiadamente al sitio, le recomendamos contactarse al número 01800-MICENSO (01800-6423676) o bien mediante el correo electrónico <a href=\"mailto:contacto@censosmkd.com\">contacto@censosmkd.com</a></small></font><br>" . mysql_error());

    @mysql_select_db($login_database[3], $link) or die('<font face=Verdana size=4 color=#004080>MENSAJE DE ACCESO AL SERVICIO<br><br><small>Su computadora no ha podido conectarse apropiadamente al servicio.<br><br>Le sugerimos revisar las siguientes posibles causas.<br><br>Dirección Incorrecta<br>Para ingresar a la página, NO es indispensable teclear la dirección con usando los términos <b>“www”</b> o <b>“http”</b>. Unicamente ingrese indicando el nombre de la página (ej. censosmkd.com).<br><br>Limpieza de Caché<br>Es recomendable ejecutar una limpieza de la memoria Caché de su navegador con cierta periodicidad debido a que se van desarrollando actualizaciones del sistema y su computadora puede estar buscando el acceso mediante un archivo de complemento no vigente. Le sugerimos limpiar el historial de navegación de internet, incluyendo los archivos Cookies.<br><br>Complementos<br>Debe asegurarse que su navegador permita el uso de Cookies y la ejecución de JavaScript ya que son indispensables para el uso adecuado de la herramienta.<br><br>Navegadores Recomendados<br>La página funciona con mayor agilidad y precisión con los siguientes navegadores:<br>Google Chrome<br>Safari<br>Internet Explorer<br>FireFox<br><br><br>En caso de que siga teniendo dificultades para acceder apropiadamente al sitio, le recomendamos contactarse al número 01800-MICENSO (01800-6423676) o bien mediante el correo electrónico <a href="mailto:contacto@censosmkd.com">contacto@censosmkd.com</a></small></font><br>' . mysql_error());

    @mysql_query("SET NAMES 'utf8'");

    return true;

}


function conectar33($bd) {

    // -----------------------------------------------------------------

    //			   SERVIDOR  -  USUARIO - PASS - BD

    // -----------------------------------------------------------------

    if (!(stripos(php_uname('n'), 'Zanatta') === false)) {

        $login_database = array('localhost', 'root', 'root', 'inmega');

    }

    else if ( ( stripos($_SERVER["HTTP_HOST"], 'localhost') === 0 ) || ( stripos($_SERVER['HTTP_HOST'], 'www.oomovil.com.mx' ) === 0 ) || ( stripos($_SERVER['HTTP_HOST'], '192.168.32.4' ) === 0 ) ) {

        $login_database = array('localhost:8889', 'root', 'root', 'inmega');

//        $login_database = array('localhost', 'root', 'mysql', 'inmega');

    	}else {
				$tmp = explode('.', $SAux);

				$SAux = 'censosmk_' .$bd;

				//die ('base de datos:'.$SAux);

				//$login_database = array('localhost', 'root', 'deimosx00', $SAux);

				 

				$login_database = array('localhost', 'censosmk_dbuser', '1nm3g@dbf4rm4c1454h0rr0/*', $SAux);


    }

    // $login_database[3];

    //echo $_SERVER["HTTP_HOST"];

    $link = @mysql_connect($login_database[0], $login_database[1], $login_database[2]) or die("<font face=Verdana size=4 color=#004080>MENSAJE DE ACCESO AL SERVICIO<br><br><small>Su computadora no ha podido conectarse apropiadamente al servicio.<br><br>Le sugerimos revisar las siguientes posibles causas.<br><br>Dirección Incorrecta<br>Para ingresar a la página, NO es indispensable teclear la dirección con usando los términos <b>“www”</b> o <b>“http”</b>. Unicamente ingrese indicando el nombre de la página (ej. censosmkd.com).<br><br>Limpieza de Caché<br>Es recomendable ejecutar una limpieza de la memoria Caché de su navegador con cierta periodicidad debido a que se van desarrollando actualizaciones del sistema y su computadora puede estar buscando el acceso mediante un archivo de complemento no vigente. Le sugerimos limpiar el historial de navegación de internet, incluyendo los archivos Cookies.<br><br>Complementos<br>Debe asegurarse que su navegador permita el uso de Cookies y la ejecución de JavaScript ya que son indispensables para el uso adecuado de la herramienta.<br><br>Navegadores Recomendados<br>La página funciona con mayor agilidad y precisión con los siguientes navegadores:<br>Google Chrome<br>Safari<br>Internet Explorer<br>FireFox<br><br><br>En caso de que siga teniendo dificultades para acceder apropiadamente al sitio, le recomendamos contactarse al número 01800-MICENSO (01800-6423676) o bien mediante el correo electrónico <a href=\"mailto:contacto@censosmkd.com\">contacto@censosmkd.com</a></small></font><br>" . mysql_error());

    @mysql_select_db($login_database[3], $link) or die('<font face=Verdana size=4 color=#004080>MENSAJE DE ACCESO AL SERVICIO<br><br><small>Su computadora no ha podido conectarse apropiadamente al servicio.<br><br>Le sugerimos revisar las siguientes posibles causas.<br><br>Dirección Incorrecta<br>Para ingresar a la página, NO es indispensable teclear la dirección con usando los términos <b>“www”</b> o <b>“http”</b>. Unicamente ingrese indicando el nombre de la página (ej. censosmkd.com).<br><br>Limpieza de Caché<br>Es recomendable ejecutar una limpieza de la memoria Caché de su navegador con cierta periodicidad debido a que se van desarrollando actualizaciones del sistema y su computadora puede estar buscando el acceso mediante un archivo de complemento no vigente. Le sugerimos limpiar el historial de navegación de internet, incluyendo los archivos Cookies.<br><br>Complementos<br>Debe asegurarse que su navegador permita el uso de Cookies y la ejecución de JavaScript ya que son indispensables para el uso adecuado de la herramienta.<br><br>Navegadores Recomendados<br>La página funciona con mayor agilidad y precisión con los siguientes navegadores:<br>Google Chrome<br>Safari<br>Internet Explorer<br>FireFox<br><br><br>En caso de que siga teniendo dificultades para acceder apropiadamente al sitio, le recomendamos contactarse al número 01800-MICENSO (01800-6423676) o bien mediante el correo electrónico <a href="mailto:contacto@censosmkd.com">contacto@censosmkd.com</a></small></font><br>' . mysql_error());

    @mysql_query("SET NAMES 'utf8'");

    return true;

}







//@Isra

function sesiones($login = false) {

    if (($_SESSION ['user'])) {

        if (conectar()) {

        $configt = @mysql_query('select * from config') or die ('Error en la configuracion del dominio: ' . mysql_error() );

		$conf = @mysql_fetch_array ($configt);		

		$times['times'] = $conf['times'] ;

		

            @mysql_query('SELECT idusuario ' .

                            'FROM log ' .

                            'WHERE idusuario = "' . mysql_real_escape_string($_SESSION ['user']) . '" ' .

                            'and now() <= ADDTIME( conexion, ' . $times['times'] . ')' .

                            'and sesion = "' . mysql_real_escape_string(session_id()) . '"') or

                    die('<font face=Verdana size=5 color=#004080>Error en Base De Datos<br><small>Intente m&aacute;s tarde<br></small></font>'. mysql_error());

                   

                    

                    

            if (@mysql_affected_rows() == 0) {

                if ($login) {

                    return false;

                } else {

                    header('location: logout.php');

                }

            } else {

                @mysql_query('UPDATE log SET conexion = now() WHERE idusuario = "' . mysql_real_escape_string($_SESSION ['user']) . '"');

                return true;

            }

        }

    } else

    if ($login) {

        return false;

    } else {

        header('location: logout.php');

    }

}



function nombre($id, $columna, $idcol, $table) {

    $resultado = @mysql_query('SELECT ' . $columna . ' name FROM ' . $table . ' WHERE ' . $idcol . '= "' . mysql_real_escape_string($id) . '"')

            or die('<font face=Verdana size=5 color=#004080>Error En Base De Datos<br><small>Intente m&aacute;s tarde<br></small></font> ');

    if (mysql_num_rows($resultado) == 0) {

        header('location: nofound.php');

    } else {

        $datos = mysql_fetch_assoc($resultado);

        return $datos['name'];

    }

}



function ejecutar_sql($info_guardar, $id, $exta) {



    $resultado = @mysql_query($info_guardar) or die('<font face=Verdana size=5 color=#004080>Error En Base De Datos<br><small>Intente m&aacute;s tarde<br></small></font>');

    if ($id != '') {

        $resultado = @mysql_query('select max(' . $id . ') id from personaRepositorio') or die('<font face=Verdana size=5 color=#004080>Error En Base De Datos<br><small>Intente m&aacute;s tarde<br></small></font>');

        mysql_data_seek($resultado, 0);

        $row = mysql_fetch_object($resultado);

        $resultado = @mysql_query(str_replace('[--repd--]', $row->id, $exta)) or die('<font face=Verdana size=5 color=#004080>Error En Base De Datos<br><small>Intente m&aacute;s tarde<br></small></font>');

        return $row->id;

    }

    else

        return 1;

}



function makemenu() {

    $menu = '<ul id="jpvmenu">

        <li><a href="index.php" target="_self" title="Ir al inicio" >Inicio</a></li>

        <li><a href="#" title="Buscar y redirigir el mapa a otra localidad." onclick="buscar()" >Ir a</a></li>

        <li><a href="#" target="_self" class="" title="Consultas a la base de datos">Consultas a la Base</a>

        <span width="300px">

        <div><a href="#" target="_self" title="Al adquirir una Consulta Premium obtienes el detalle de establecimientos y el perfil demográfico de la zona en tu cuenta y en un reporte descargable." onclick="cambiar_tab(); opciones_comprar(\'1\');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consultas Premium</a></div>

        <div><a href="#" title="¿Quieres saber cuántos establecimientos y población hay en una zona en específico? Obtenlo con una Consulta Básica." onclick="cambiar_tab(); ver_gratis(); mostrar_puntos_gratis();"><table border="0"><tr><td><img src="' . DIR_TEMA_ACTIVO . '_img/gratis.gif" /></td><td>Consultas Básicas!</td></tr></table></a></div>

        </span>

        </li>' .

            /* <li><a href="#" onclick="opciones_Agebs()" title="Agebs">Agebs</a></li> */

            '<li><a href="#" onclick="opciones_zonas()" title="Zonas">Zonas</a></li>';

    if (!isset($_SESSION ['user'])) {

        $menu .= '<li><a href="chat.php" target="_blank" title="Chat">Chat</a></li>

            <li><a href="login.php" target="_self" title="Login">Login</a></li>

			<li><a href="#" onclick="ayuda(\'7\')" title="Acerca de">Acerca de</a></li></ul>';

    } else {

        $menu .= '<li><a href="#" target="_self" title="Entra para administrar tus compras y visualizarlas. Aqu podrs realizar subconsultas sobre tus compras y hacer combinaciones entre las categorías adquiridas!." onclick="cambiar_tab(); opciones_miscompras(); activar_compra(\'' . (isset($idcompras) ? mysql_real_escape_string($idcompras) : '') . '\');"><img src="https://censosmkd.com/temas/default/_img/compra.png"  width="44" height="44"></a></li>';
        if($dominio=='censosmkd'){
            $menu .= '<li><a href="#" target="_self" class="administrar" title="Administrar"><img src="https://censosmkd.com/temas/default/_img/admin.png"  width="44" height="44"></a>';
        }else{
            $menu .= '<li><a href="#" target="_self" class="administrar" title="Administrar"><img src="https://censosmkd.com/temas/default/_img/adminv2.png"  width="44" height="44"></a>';
        }
            $menu .= '<span>

            <div><a href="clientes.php" title="Clientes">Clientes</a></div>

            <div><a href="censos.php" title="Censos">Censos</a></div>

            <div><a href="categorias.php" title="Categorías">Categoías</a></div>

            <div><a href="preguntas.php" title="Preguntas">Preguntas</a></div>

            <div><a href="puntos.php" title="Puntos">Puntos</a></div>

            <div><a href="grupos.php" title="Grupos">Grupos de Acceso</a> </div>

            <div><a href="usuarios.php" title="Usuario">Usuarios del Sistema</a></div>

            <div><a href="Importaciones.php" title="Importaciones">Importaciones</a>  </div>' .

                //<div><a href="ageb.php" title="Ageb">Ageb</a></div>

                '<div><a href="puntos_cercanos.php" title="Fusionar Puntos">Fusionar Puntos</a></div>

            <div><a href="limpiar_tablas.php" title="Limpiar Compra">Limpieza Compra</a></div>

            <div><a href="categorias_zonas.php" title="Categorías Zonas">Categorías Zonas</a></div>

            <div><a href="#" onclick="opciones_administrar_zonas(\'1\')"  title="Administrar Zonas">Administrar Zonas</a></div>

            <div><a href="rastreabilidad.php" title="Rastreabilidad">Rastreabilidad</a></div>

            </span>

            </li>

            <li><a href="carrito.php" target="_self" title="Carrito de compras" >Carrito</a></li>

            <li><a href="chat.php" target="_blank" title="Tienes alguna duda?, entra al Chat y comntalo con un asesor!.">Chat</a></li>

            <li><a href="logout.php" target="_self" title="Salir de tu cuenta" ><img src="https://censosmkd.com/temas/default/_img/exit.png" width="44" height="44" ">(' . $_SESSION['user'] . ')</a></li>

			<li><a href="#" onclick="ayuda(\'7\')" title="Acerca de...">Acerca de</a></li></ul>';

    }

    return ''; //$menu;

}



function makemenuNVO($clase = '') {

    conectar();

    $resultado = @mysql_query('SELECT * FROM usuario WHERE idusuario = "' . $_SESSION ['user'] . '"');

    $esUsuario = false;

    while ($datos = mysql_fetch_assoc($resultado)) {

        if ($datos['tipo'] == '')

            $esUsuario = true;

    }
$dominio = $_SERVER["HTTP_HOST"];

	$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );
    $menu =

            '<div id="menuNVO" class="' . $clase . '" style="width:100%; display:flex; left:0">

            <ul class="level1">';
            if($dominio=='censosmkd'){

                $menu.='<li class="level1-li bHome"><a class="level1-a" title="Ir al inicio" href="index.php"><img src="https://censosmkd.com/temas/default/_img/home.png" width="44" height="44" "></a></li>';
            
            }else{
                $menu.='<li class="level1-li"><a class="level1-a" title="Ir al inicio" href="index.php"><img src="https://censosmkd.com/temas/default/_img/homev2.png" width="44" height="44" "></a></li>';
            }


    if ($clase != "menuNVOAdmin")

        $menu .='';
                if($dominio=='censosmkd'){
                    $menu.='<li class="level1-li bBusq"><a class="level1-a" title="Buscar y redirigir el mapa a otra localidad." href="#" onClick="buscar()"><img src="https://censosmkd.com/temas/default/_img/busq.png" width="44" height="44" "></a></li>';
                }else{
                    $menu.='<li class="level1-li"><a class="level1-a" title="Buscar y redirigir el mapa a otra localidad." href="#" onClick="buscar()"><img src="https://censosmkd.com/temas/default/_img/busqv2.png" width="44" height="44" "></a></li>';
                }
                (esCorporativo() ? (isset($_SESSION ['user']) ? '<li class="level1-li"><a class="level1-a" href="#" title="Crear una nueva consulta." onclick="cambiar_tab(); opciones_comprar(\'1\')"><img src="https://censosmkd.com/temas/default/_img/qnew.png"  width="44" height="44" "></a></li>' : '') :

                        '<li class="level1-li oscuro"><a class="level1-a drop"  title="Hacer Consultas" href="#"><img src="https://censosmkd.com/temas/default/_img/consult.png" width="44" height="44" "><!--[if gte IE 7]><!--></a><!--<![endif]-->

                <!--[if lte IE 6]><table><tr><td><![endif]-->

                    <ul class="level2" style="background-color: #a1dd8b;border-radius: 20px; z-index:1"> 

                        <li><a href="#" title="Al adquirir una Consulta Premium obtienes el detalle de establecimientos y el perfil demográfico de la zona en tu cuenta y en un reporte descargable." onclick="' . ( isset($_SESSION ['user']) ? 'cambiar_tab(); opciones_comprar(\'1\')' : 'alert(\'No es posible hacer consultas premium sin registrarte!\');window.location=\'login.php\'' ) . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px; position: absolute; left:30px;"><path d="M35.42 188.21l207.75 269.46a16.17 16.17 0 0025.66 0l207.75-269.46a16.52 16.52 0 00.95-18.75L407.06 55.71A16.22 16.22 0 00393.27 48H118.73a16.22 16.22 0 00-13.79 7.71L34.47 169.46a16.52 16.52 0 00.95 18.75zM48 176h416" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M400 64l-48 112-96-128M112 64l48 112 96-128M256 448l-96-272M256 448l96-272"/></svg>Consultas Premium</a></li>

                        <li><a href="#" title="¿Quieres saber cuántos establecimientos y población hay en una zona en específico? Obtenlo con una Consulta Básica." onclick="cambiar_tab(); ver_gratis(1); mostrar_puntos_gratis();"><div><img align="middle" src="' . DIR_TEMA_ACTIVO . '_img/gratis.gif" /> Consultas Básicas</div></a></li>                        

                        <li><a href="201208EjemploResultadosConsultasPremium.pdf" target="nueva" title="Descarga un ejemplo del reporte a obtener en consultas Premium">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px; position: absolute; left:30px;"><path d="M208 64h66.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62V432a48 48 0 01-48 48H192a48 48 0 01-48-48V304" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M288 72v120a32 32 0 0032 32h120" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M160 80v152a23.69 23.69 0 01-24 24c-12 0-24-9.1-24-24V88c0-30.59 16.57-56 48-56s48 24.8 48 55.38v138.75c0 43-27.82 77.87-72 77.87s-72-34.86-72-77.87V144" fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32"/></svg>Ejemplo de consultas Premium</a></li>

						<li><a href="archivos/201208_Manual_Usuario_CensosMkd.com_R2.1.pdf" target="nueva" title="Preguntas y respuestas frecuentes.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px; position: absolute; left:30px;"><path d="M336 176h40a40 40 0 0140 40v208a40 40 0 01-40 40H136a40 40 0 01-40-40V216a40 40 0 0140-40h40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 272l80 80 80-80M256 48v288"/></svg>Descargar Guía de Uso</a></li>

						<li><a href="#" onclick="ayuda(\'8\')" title="Preguntas y respuestas frecuentes">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px; position: absolute; left:30px;" ><path d="M416 221.25V416a48 48 0 01-48 48H144a48 48 0 01-48-48V96a48 48 0 0148-48h98.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path d="M256 56v120a32 32 0 0032 32h120M176 288h160M176 368h160" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>Preguntas y respuestas frecuentes</a></li>

                    </ul>

                <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                </li>')

        . '<li class="level1-li"><a class="level1-a" href="#" onclick="opciones_zonas()" title="Zonas"><img src="https://censosmkd.com/temas/default/_img/zonas.png"  width="44" height="44" "></a></li>'

        ;



    if (!isset($_SESSION ['user'])){

        if($dominio=='censosmkd'){
            $menu .= '<li class="level1-li bLog"><a class="level1-a btnL" href="login.php" target="_self" title="Login"><img src="https://censosmkd.com/temas/default/_img/login.png"  width="44" height="44" "></a></li>';
        }else{
            $menu .= '<li class="level1-li"><a class="level1-a btnL" href="login.php" target="_self" title="Login"><img src="https://censosmkd.com/temas/default/_img/loginv2.png"  width="44" height="44" "></a></li>';
        }
//                (!esCorporativo() && !esPyme() ? '<li class="level1-li oscuro"><a class="level1-a oscuro" href="#" onclick="mostrar_ventana_especial(\'Agregar Usuario\',\'1\');" title="Registro">Registro gratis!</a></li>' : '');

                  (!esCorporativo() && !esPyme() ? $menu.='<li class="level1-li oscuro"><a class="level1-a oscuro btnR" href="login.php" onclick="" title="Registro" style="border-radius: 20px;">Registro gratis!</a></li>' : '');

        /*

          (!esPyme() || isset ($_SESSION [ 'user' ] )?

          '<li class="level1-li"><a class="level1-a" href="href="chat.php" target="_blank" title="Tienes alguna duda?, entra al Chat y coméntalo con un asesor!.">Chat</a></li>':'');

         */

    }else{

        $menu.=

                ($clase != "menuNVOAdmin" ?

                        '<li class="level1-li bCompra"><a class="level1-a" href="#" onclick="cambiar_tab(); opciones_miscompras(); activar_compra(\'' .

                        (isset($idcompras) ? mysql_real_escape_string($idcompras) : '') . '\'); muestraAgebsPolilinea(pcompras);" title="Administra y revisa tus compras">' .

                        (esCorporativo() ? '<img src="https://censosmkd.com/temas/default/_img/'.($dominio=='censos'? 'portafolio.png"' : 'portafoliov2.png"')  .'width="44" height="44">' : '<img src="https://censosmkd.com/temas/default/_img/compra.png"  width="44" height="44">') . '</a></li>' : '') .

                '<li class="level1-li bAdm"><a class="level1-a drop" href="#">';  if($dominio=='censosmkd'){$menu.='<img src="https://censosmkd.com/temas/default/_img/admin.png"  width="44" height="44">';}else{$menu.='<img src="https://censosmkd.com/temas/default/_img/adminv2.png"  width="44" height="44">';}$menu.='<!--[if gte IE 7]><!--></a><!--<![endif]-->

                <!--[if lte IE 6]><table><tr><td><![endif]-->

                    <ul class="level2" style="z-index:1;">' .

                (_derechos($_SESSION ['user'], 1) != '' ? '<li><a href="clientes.php" title="Catálogo de Clientes">Clientes</a></li>' : '') .

                (_derechos($_SESSION ['user'], 21) . _derechos($_SESSION ['user'], 27) != '' ?

                        '<li><a class="fly" href="#">Administración de Censos<!--[if gte IE 7]><!--></a><!--<![endif]-->

                            <!--[if lte IE 6]><table><tr><td><![endif]-->

                                <ul class="level3">' .

                        (_derechos($_SESSION ['user'], 21) != '' ? '<li><a href="censos.php" title="Catálogo de Censos">Censos</a></li>' : '') .

                        (_derechos($_SESSION ['user'], 27) != '' ? '<li><a href="puntos.php" title="Puntos">Puntos</a></li>' : '') .

                        '</ul>

                            <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                            </li>' : '') .

                (_derechos($_SESSION ['user'], 25) . _derechos($_SESSION ['user'], 26) != '' ?

                        '<li><a class="fly" href="#">Administración de Preguntas<!--[if gte IE 7]><!--></a><!--<![endif]-->

                        <!--[if lte IE 6]><table><tr><td><![endif]-->

                            <ul class="level3">' .

                        (_derechos($_SESSION ['user'], 25) != '' ? '<li><a href="categorias.php" title="Categorías">Categorías</a></li>' : '') .

                        (_derechos($_SESSION ['user'], 26) != '' ? '<li><a href="preguntas.php" title="Preguntas">Preguntas</a></li>' : '') .

                        '</ul>

                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                        </li>' : '') .

                (_derechos($_SESSION ['user'], 28) . _derechos($_SESSION ['user'], 29) != '' ?

                        '<li><a class="fly" href="#">Administración de Usuarios<!--[if gte IE 7]><!--></a><!--<![endif]-->

                        <!--[if lte IE 6]><table><tr><td><![endif]-->

                            <ul class="level3">' .

						(_derechos($_SESSION ['user'], 37) != '' ? '<li><a href="gruposSubPoligonos.php" title="Grupos SubPoligonos">Grupos SubPoligonos</a></li>' : '' ) .

                        (_derechos($_SESSION ['user'], 28) != '' ? '<li><a href="grupos.php" title="Grupos">Grupos de Acceso</a></li>' : '') .

                        (_derechos($_SESSION ['user'], 29) != '' ? '<li><a href="usuarios.php" title="Usuario">Usuarios del Sistema</a></li>' : '') .

                        '</ul>

                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                        </li>' : '') .

                (_derechos($_SESSION ['user'], 30) . _derechos($_SESSION ['user'], 20) . _derechos($_SESSION ['user'], 19) . _derechos($_SESSION ['user'], 33) . _derechos($_SESSION ['user'], 34) != '' ?

                        '<li><a class="fly" href="#">Herramientas<!--[if gte IE 7]><!--></a><!--<![endif]-->

                        <!--[if lte IE 6]><table><tr><td><![endif]-->

                            <ul class="level3">' .

                        (_derechos($_SESSION ['user'], 30) != '' ? '<li><a href="Importaciones.php" title="Importaciones">Importaciones</a></li>' : '') .

                        (_derechos($_SESSION ['user'], 31) != '' ? '<li><a href="ageb.php" title="Importaciones de Agebs">Importaciones de Agebs</a></li>' : '') .

                        (_derechos($_SESSION ['user'], 20) != '' ? '<li><a href="puntos_cercanos.php" title="Fusionar Puntos">Fusionar Puntos</a></li>' : '') .

                        (_derechos($_SESSION ['user'], 19) != '' ? '<li><a href="limpiar_tablas.php" title="Limpiar Compra">Limpieza Compra</a></li>' : '') .

                        ($clase != "menuNVOAdmin" ?

                                (_derechos($_SESSION ['user'], 33) != '' ? '<li><a class="fly" href="#">Administracin de Zonas<!--[if gte IE 7]><!--></a><!--<![endif]-->

                                        <!--[if lte IE 6]><table><tr><td><![endif]-->

                                            <ul class="level4">

                                                <li><a href="categorias_zonas.php" title="Categorias Zonas">Categorias para Zonas</a></li>

                                                <li><a href="#" onclick="opciones_administrar_zonas(\'1\')"  title="Administrar Zonas">Administrar Zonas</a></li>

                                            </ul>

                                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                                     </li>' : '') :

                                (_derechos($_SESSION ['user'], 33) != '' ? '<li><a href="categorias_zonas.php" title="Categorías Zonas">Categorías para Zonas</a></li>' : '')) .

                        (_derechos($_SESSION ['user'], 34) != '' ? '<li><a href="rastreabilidad.php" title="Rastreabilidad">Rastreabilidad</a></li>' : '') .

			(_derechos($_SESSION ['user'], 36) != '' ? '<li><a href="panelcontrol.php" title="Panel de Control">Panel de Control</a></li>' : '') .

                        '</ul>

                        <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                        </li>' : '') .

                (_derechos($_SESSION['user'], 35) != '' ? '<li><a href="config.php" title="Parámetros del dominio">Parámetros del dominio</a></li>' : '') .

                '<li><a href="cambiarContrasena.php" title="Cambiar contraseña">Cambiar contraseña</a></li>' .

                '</ul>

                <!--[if lte IE 6]></td></tr></table></a><![endif]-->

                </li>' .

                (esCorporativo() ? '<li class="level1-li"><a class="level1-a" href="carrito.php" target="_self" title="Confirmar Consulta" ><img src="https://censosmkd.com/temas/default/_img/'.($dominio=='censosmkd'? 'qok.png"' : 'qokv2.png"')  .'width="44" height="44" "></a></li>' : '<li class="level1-li bCar"><a class="level1-a" href="carrito.php" target="_self" title="Carrito de compras" ><img src="https://censosmkd.com/temas/default/_img/car.png" width="40" height="40"></a></li>') .

                '<li class="level1-li bChat"><a class="level1-a" href="chat.php" target="_blank" title="Tienes alguna duda?, entra al Chat y coméntalo con un asesor!."><img src="https://censosmkd.com/temas/default/_img/'.($dominio=='censosmkd'? 'chat.png"' : 'chatv2.png"')  .'width="40" height="40"></a></li>' .

                '<li class="level1-li bExit"><a class="level1-a" href="logout.php" target="_self" title="Salir de tu cuenta" ><img src="https://censosmkd.com/temas/default/_img/'.($dominio=='censosmkd'? 'exit.png"' : 'exitv2.png"')  .'width="35" height="35">(' . $_SESSION['user'] . ')</a></li>';

    }



    if ($clase != "menuNVOAdmin")
        
        $menu .= '<li class="level1-li bHelp"><a class="level1-a drop" title="Ayuda" href="#">'; if($dominio=='censosmkd'){$menu.='<img src="https://censosmkd.com/temas/default/_img/helpd.png"  width="44" height="44" ">';}else{$menu.='<img src="https://censosmkd.com/temas/default/_img/helpdv2.png" width="44" height="44" ">';}$menu.='<!--[if gte IE 7]><!--></a><!--<![endif]-->

			                <!--[if lte IE 6]><table><tr><td><![endif]-->

			                    <ul class="level2" style="z-index:1">

			                    	<li><a href="archivos/201208_Manual_Usuario_CensosMkd.com_R2.1.pdf" target="nueva" title="Preguntas y respuestas frecuentes."><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px;"><path d="M336 176h40a40 40 0 0140 40v208a40 40 0 01-40 40H136a40 40 0 01-40-40V216a40 40 0 0140-40h40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 272l80 80 80-80M256 48v288"/></svg>Descargar Guía de Uso</a></li>

			                    	<li><a href="http://www.youtube.com/user/censosmkd?feature=results_main" target="nueva" title="Video Tutorial."><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px;"><rect x="48" y="96" width="416" height="320" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="384" y="336" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="384" y="256" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="384" y="176" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="384" y="96" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="48" y="336" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="48" y="256" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="48" y="176" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="48" y="96" width="80" height="80" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="128" y="96" width="256" height="160" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><rect x="128" y="256" width="256" height="160" rx="28" ry="28" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/></svg>Video Tutorial</a></li>

			                        <li><a href="mailto:contacto@censosmkd.com" title="Preguntas y respuestas frecuentes."><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px;"><rect x="48" y="96" width="416" height="320" rx="40" ry="40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M112 160l144 112 144-112"/></svg>E-mail Contacto</a></li>

			                        <li><a href="#" title="Consulta información de INEGI en línea" onclick="var w=window.open(\'widgets.htm\');"><div><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px;"><rect x="48" y="48" width="176" height="176" rx="20" ry="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="288" y="48" width="176" height="176" rx="20" ry="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="48" y="288" width="176" height="176" rx="20" ry="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><rect x="288" y="288" width="176" height="176" rx="20" ry="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>Widgets INEGI</div></a></li>

			                        <li><a href="#" onclick="ayuda(\'7\')" title="Acerca de"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px; stroke:#fff; fill:#fff"><path d="M248 64C146.39 64 64 146.39 64 248s82.39 184 184 184 184-82.39 184-184S349.61 64 248 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M220 220h32v116"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M208 340h88"/><path d="M248 130a26 26 0 1026 26 26 26 0 00-26-26z"/></svg>Acerca de...</a></li>

                                                <li><a href="#" onclick="ayuda(\'8\')" title="Preguntas y respuestas frecuentes"><svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="color:#fff; width:20px; "><path d="M416 221.25V416a48 48 0 01-48 48H144a48 48 0 01-48-48V96a48 48 0 0148-48h98.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path d="M256 56v120a32 32 0 0032 32h120M176 288h160M176 368h160" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>Preguntas y respuestas frecuentes</a></li>

			                    </ul>

			                <!--[if lte IE 6]></td></tr></table></a><![endif]-->

		                </li>';

    $menu .= '</ul>

        </div>';

    return $menu;

}



function opcion_grupo($id = '') {

    $resultado = @mysql_query('SELECT idgrupo,descripcion ' .

                    'FROM grupo ' . ( $id == '' ? '' : 'WHERE idgrupo not in ( SELECT idgrupo FROM usuarioxgrupo WHERE idusuario = "' . $id . '" ) ' ) .

                    'ORDER BY descripcion');

    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {

        $opcion .= '<option value="' . htmlspecialchars($datos ['idgrupo']) . '">' . htmlspecialchars($datos ['idgrupo']) . '</option>';

    }

    return $opcion;

}



function grupos_usuarios($id) {

    $resultado = @mysql_query('SELECT idgrupo FROM usuarioxgrupo WHERE idusuario = "' . $id . '"');

    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {

        $opcion .= '<option value="' . htmlspecialchars($datos ['idgrupo']) . '">' . htmlspecialchars($datos ['idgrupo']) . '</option>';

    }

    return $opcion;

}



function cats($opct = "", $table = 'categorias') {

    if ($table == 'categorias') {

        $idcat = 'idcategoria';

    } else {

        $idcat = 'idcategorias_zonas';

    }

    $resultado = @mysql_query('SELECT ' . $idcat . ', descripcion FROM ' . $table . ' ORDER BY descripcion desc');

    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {

        $opcion .= '<option value="' . $datos [$idcat] . '"' . ( ( $datos [$idcat] == $opct ) ? ' selected="selected"' : '' ) . '>' .

                htmlspecialchars($datos ['descripcion']) . '</option>';

    }

    return $opcion;

}


function resultados_cats($derechos, $descripcion = "", $padre = "", $ordenar = "desc", $col = "idcategoria", $pag = "0", $filtros = "") {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 20;

        $restab = '<a name="Resultados"></a><table width="100%" border="0" cellspacing="2">

            <tr class="tabla_titulo">

            <td width="56">Opciones</td>

            <td width="25">Id</td>                     

            <td width="391">Descripción</td>

            <td width="392">Categoría Padre</td>

            </tr>';

        $Q = 'FROM categorias left join categorias cat on ( categorias.categoriaPadre = cat.idcategoria ) ' .

                'WHERE categorias.idcategoria ' . $descripcion . $padre .

                ' ORDER BY categorias.' . $col . '  ' . $ordenar;

        $resultado = @mysql_query('SELECT categorias.idcategoria, categorias.descripcion, cat.descripcion cp ' . $Q . ' LIMIT ' . ( $pag * $recxpag ) . ',' . $recxpag);

        $resultadoContador = @mysql_query('Select count(*) as contador ' . $Q);

        $datosContador = @mysql_fetch_assoc($resultadoContador);



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab .= '<tr>

                <td>' . ( in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_cat\',\'' . $datos [idcategoria] .

                            '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '' ) .

                    ( in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Categoría\',\'' . $datos ['idcategoria'] .

                            '\');" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos [idcategoria]) . '</td>         

                <td>' . htmlspecialchars($datos [descripcion]) . '</td>

                <td>' . htmlspecialchars($datos [cp]) . '</td></tr>';

        }

        $res = @mysql_query('SELECT categorias.idcategoria FROM categorias WHERE categorias.idcategoria ' . $descripcion . $padre);

        $total = mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

        for ($i = 0; $i < $total; $i++) {

            if ($pag == $i) {

                $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

            } else {

                $lista .= '<a  href="#Resultados" onclick="paginar(\'' . ( $i ) . '\',\'' . $filtros . '\',\'Cat_lis2\')">&nbsp;' . $i . '&nbsp;</a>';

            }

        }

        $restab .= ' </table><br/><br/><div id="res_enc" align="center">' . ( $lista == '' ? '<span>No se encontraron registros</span>' : $lista ) . '</div>';

        $restab = $datosContador['contador'] . ' registros' . $restab;

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}

function delete_registro($id, $idname, $table, $msj) {

    mysql_query ( 'SELECT ' . $idname . ' FROM ' . $table . ' WHERE ' . $idname . '= "' . mysql_real_escape_string ( $id ) . '"');

		if ( mysql_affected_rows() <= 0 ){

			$res = 'No se encontro ' . $msj;

		}else{

			mysql_query ( 'DELETE FROM ' . $table . ' WHERE ' . $idname . '= "' . mysql_real_escape_string ( $id ) . '"');

			if ( mysql_affected_rows() <= 0 ) {

				$res = 'No se pudo eliminar ' . $msj . ' por restricciones referenciales.';

			}else{

				$res = 'Registro eliminado';

			}

		}

    return $res;

}


function resultados_clientes($derechos, $razon = "", $ordenar = "desc", $pag = "0") {

    if (in_array('Consultar', $derechos)) {

        $restriccion_acceso = tipo_usuario();

        $recxpag = 20;

        $restab = '<a name="Resultados"></a><table width="100%" border="0" cellspacing="2">

            <tr class="tabla_titulo">

            <td width="153">Opciones</td>

            <td width="600">Razón Social</td>

            <td width="100">Representante Comercial</td>

            <td width="100">Asesor de Ventas</td>

            </tr>';

        $q = ( $razon == '' ? ( $restriccion_acceso == '' ? '' : 'WHERE idcliente = "' . mysql_real_escape_string($restriccion_acceso) . '"' ) : 'WHERE razonSocial like "%' . mysql_real_escape_string($razon) . '%"' . ( $restriccion_acceso == '' ? '' :

                                ' and idcliente = "' . mysql_real_escape_string($restriccion_acceso) . '"' ) );

        $resultado = @mysql_query('SELECT idcliente, razonsocial, representanteComercial, asesorVentas ' .

                        'FROM clientes ' . $q .

                        ' ORDER BY razonsocial ' . $ordenar .

                        ' LIMIT ' . ( $pag * $recxpag ) . ',' . $recxpag);



        $resultadoContador = @mysql_query('SELECT count(*) as contador ' .

                        'FROM clientes ' . $q .

                        ' ORDER BY razonsocial ' . $ordenar);

        $datosContador = @mysql_fetch_assoc($resultadoContador);

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab .= '<tr>

                <td>' .

                    ( in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_cliente\',\'' . $datos ['idcliente'] . '\')" ' .

                            'onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" ' .

                            'height="16" alt="Eliminar" /></a>&nbsp;' : '' ) .

                    ( in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Cliente\',\'' . $datos ['idcliente'] . '\')" ' .

                            'title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>&nbsp;' : '' ) .

                    ( in_array('Domicilios', $derechos) ? '<a href="domicilios.php?id=' . $datos ['idcliente'] . '&origen=1" title="Domicilios">' .

                            '<img src="' . DIR_TEMA_ACTIVO . '_img/dom.png" width="16" height="16" alt="Domicilio" /></a>' : '' ) .

                    ( in_array('Personas', $derechos) ? '<a href="personas.php?id=' . $datos ['idcliente'] . '&origen=1" title="Contactos">' .

                            '<img src="' . DIR_TEMA_ACTIVO . '_img/contactos.png" width="16" height="18" alt="Contactos" /></a>' : '' ) .

                    ( in_array('Archivos', $derechos) ? '<a href="archivos.php?id=' . $datos ['idcliente'] . '&origen=1" title="Documentos">' .

                            '<img src="' . DIR_TEMA_ACTIVO . '_img/documentos.png" width="25" height="18" alt="Documentos" /></a>' : '' ) .

                    ( in_array('Usuarios', $derechos) ? '<a href="usuarios.php?id=3&cliente=' . $datos ['idcliente'] . '" title="usuarios">' .

                            '<img src="' . DIR_TEMA_ACTIVO . '_img/user.png" width="18" height="18" alt="Usuarios" /></a>' : '' ) .

                    ( in_array('Censos', $derechos) ? '<a href="censos.php?idcliente=' . $datos ['idcliente'] . '" title="Censos">' .

                            '<img src="' . DIR_TEMA_ACTIVO . '_img/censo.png" width="18" height="18" alt="Censos" /></a>' : '' ) . '</td>

                <td>' . htmlspecialchars($datos ['razonsocial']) . '</td>' .

                    '<td>' . htmlspecialchars($datos ['representanteComercial']) . '</td>' .

                    '<td>' . htmlspecialchars($datos ['asesorVentas']) . '</td>';

        }

        $res = @mysql_query('SELECT idcliente, razonsocial FROM clientes ' . $q);

        $total = mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

        for ($i = 0; $i < $total; $i++) {

            if ($pag == $i) {

                $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

            } else {

                $lista .= '<a  href="#Resultados" onclick="paginar(\'' . ($i) . '\',\'' . $razon . '|' . $ordenar . '\',\'Cliente2\')">&nbsp;' . $i . '&nbsp;</a>';

            }

        }

        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

        $restab = $datosContador['contador'] . ' registros' . $restab;

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function domicilios($derechos, $id, $origen) {

    if (in_array('Consultar', $derechos)) {

        $res = '<table width="100%" border="0" cellspacing="2">

            <tr class="tabla_titulo">

            <td width="30">&nbsp;</td>

            <td width="90">Calle</td>

            <td width="30">Número</td>

            <td width="100">Municipio</td>

            <td width="100">Ciudad</td>

            <td width="30">CP</td>

            <td width="90">Estado</td>

            <td width="30">Vigente</td>

            </tr>

            ';



        switch ($origen) {

            case(1): {

                    $q = 'clientexdomicilio where clientexdomicilio.iddomiciliorepositorio=domiciliorepositorio.iddomiciliorepositorio and clientexdomicilio.idcliente="' . mysql_real_escape_string($id) . '"';

                    break;

                }

            case(2): {

                    $q = 'puntoxdomicilio where puntoxdomicilio.iddomiciliorepositorio=domiciliorepositorio.iddomiciliorepositorio and puntoxdomicilio.idpuntoRepositorio="' . mysql_real_escape_string($id) . '"';

                    break;

                }

            case(3): {

                    $q = 'personaxdomicilio where personaxdomicilio.iddomiciliorepositorio=domiciliorepositorio.iddomiciliorepositorio and personaxdomicilio.idpersonaRepositorio="' . mysql_real_escape_string($id) . '" ';

                    break;

                }

            case(4): {

                    $q = 'puntoxdomicilio where puntoxdomicilio.iddomiciliorepositorio=domiciliorepositorio.iddomiciliorepositorio and puntoxdomicilio.idpuntoRepositorio="' . mysql_real_escape_string($id) . '" ';

                    break;

                }

        }



        //	die('SELECT domiciliorepositorio.iddomiciliorepositorio,domiciliorepositorio.calle,domiciliorepositorio.numero,domiciliorepositorio.municipio,domiciliorepositorio.ciudad,domiciliorepositorio.codigopostal,domiciliorepositorio.estado,if (domiciliorepositorio.vigente="0","No","Si") vig FROM domiciliorepositorio,'.$q.  'order by domiciliorepositorio.iddomiciliorepositorio desc');

        $resultado = @mysql_query('SELECT domiciliorepositorio.iddomiciliorepositorio,domiciliorepositorio.calle,domiciliorepositorio.numero,domiciliorepositorio.municipio,domiciliorepositorio.ciudad,domiciliorepositorio.codigopostal,domiciliorepositorio.estado,if (domiciliorepositorio.vigente="0","No","Si") vig FROM domiciliorepositorio,' . $q . 'order by domiciliorepositorio.iddomiciliorepositorio desc');





        while ($datos = @mysql_fetch_assoc($resultado)) {

            $res.= '<tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_dom\',\'' . $datos['iddomiciliorepositorio'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Domicilio\',\'' . $datos['iddomiciliorepositorio'] . '\')" title="Modificar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos['calle']) . '</td>

                <td>' . htmlspecialchars($datos['numero']) . '</td>

                <td>' . htmlspecialchars($datos['municipio']) . '</td>

                <td>' . htmlspecialchars($datos['ciudad']) . '</td>

                <td>' . htmlspecialchars($datos['codigopostal']) . '</td>

                <td>' . htmlspecialchars($datos['estado']) . '</td>

                <td>' . htmlspecialchars($datos['vig']) . '</td></tr>';

        }



        $res.='</table><div id="res_enc" align="center">' . (@mysql_num_rows($resultado) <= 0 ? '<span>No se encontraron registros</span>' : '<span>Registros encontrados: ' . @mysql_num_rows($resultado) . ' </span>') . '</div>';

    } else {

        $res = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $res;

}



function documento($derechos, $id, $origen, $nombre = "", $order = "desc") {

    if (in_array('Consultar', $derechos)) {

        $res = '<table width="100%" border="0" cellspacing="2">

            <tr class="tabla_titulo">

            <td width="10%">&nbsp;</td>

            <td width="30%">Nombre</td>

            <td width="40%">Comentario</td>

            <td width="20%">Vigente</td>

            </tr>

            ';



        switch ($origen) {

            case(1): {

                    $t = 'clientexdocumento';

                    $idk = 'idcliente';

                    break;

                }

            case(2): {

                    $t = 'puntoxdocumento';

                    $idk = 'idpuntoRepositorio';

                    break;

                }

            case(3): {

                    $t = 'personaxdocumento';

                    $idk = 'idpersonaRepositorio';

                    break;

                }

            case(4): {

                    $t = 'puntoxdocumento';

                    $idk = 'idpuntoRepositorio';

                    break;

                }

        }



        $resultado = @mysql_query('select documentorepositorio.iddocumentoRepositorio,documentorepositorio.nombreFisico,documentorepositorio.comentario,if (vigente="0","No","Si") vig from documentorepositorio,' . $t . '  where documentorepositorio.iddocumentoRepositorio=' . $t . '.iddocumentoRepositorio and ' . $t . '.' . $idk . '="' . mysql_real_escape_string(trim($id)) . '" ' . (trim($nombre) != '' ? ' and documentorepositorio.nombreFisico like "%' . mysql_real_escape_string(trim($nombre)) . '%"' : '') . ' order by	documentorepositorio.nombreFisico ' . $order);



        $fila = 1;



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $res.= '<tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_doc\',\'' . $datos['iddocumentoRepositorio'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Documento\',\'' . $datos['iddocumentoRepositorio'] . '\')" title="Modificar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>&nbsp;' : '') .

                    (in_array('Descargar', $derechos) ? '<a href="download.php?tipo=1&id=' . $datos['iddocumentoRepositorio'] . '&origen=' . $origen . '&idorigen=' . $id . '" title="Descargar" target="_blank"><img src="' . DIR_TEMA_ACTIVO . '_img/download.png" width="18" height="18" alt="Descargar" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos['nombreFisico']) . '</td>

                <td>' . htmlspecialchars($datos['comentario']) . '</td>

                <td>' . htmlspecialchars($datos['vig']) . '</td>

                </tr>';

        }





        $res.='</table><div id="res_enc" align="center">' . (@mysql_num_rows($resultado) <= 0 ? '<span>No se encontraron registros</span>' : '<span>Registros encontrados: ' . @mysql_num_rows($resultado) . ' </span>') . '</div>';

    } else {

        $res = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $res;

}



function imagen($derechos, $id, $origen, $nombre = "", $order = "desc") {

    if (in_array('Consultar', $derechos)) {

        $res = '<table width="100%" border="0" cellspacing="2">

            <tr class="tabla_titulo">

            <td width="10%">&nbsp;</td>

            <td width="20%">Nombre</td>

            <td width="15%">Vigente</td>

            <td >Comentario</td>

            <td width="100px;" >Preview</td>

            </tr>

            ';



        switch ($origen) {

            //case(1):{$t='clientexdocumento';$idk='idcliente';break;}

            case(2): {

                    $t = 'puntoximagen';

                    $idk = 'idpuntoRepositorio';

                    break;

                }

            case(3): {

                    $t = 'personaximagen';

                    $idk = 'idpersonaRepositorio';

                    break;

                }

            case(4): {

                    $t = 'puntoximagen';

                    $idk = 'idpuntoRepositorio';

                    break;

                }

        }



        $resultado = @mysql_query('select imagenrepositorio.imagen,imagenrepositorio.type, imagenrepositorio.idimagenRepositorio,imagenrepositorio.nombreFisico,imagenrepositorio.comentario,if (imagenrepositorio.vigente="0","No","Si") vig from imagenrepositorio,' . $t . '  where imagenrepositorio.idimagenRepositorio=' . $t . '.idimagenRepositorio and ' . $t . '.' . $idk . '="' . mysql_real_escape_string(trim($id)) . '" ' . (trim($nombre) != '' ? ' and imagenrepositorio.nombreFisico like "%' . mysql_real_escape_string(trim($nombre)) . '%"' : '') . ' order by	imagenrepositorio.nombreFisico ' . $order);



        $fila = 1;



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $name = '';

            $im = imagecreatefromstring($datos['imagen']);

            $width = imagesx($im);

            $height = imagesy($im);

            // Set thumbnail-width to 100 pixel

            $imgw = 60;



            $imgh = $height / $width * $imgw;

            $thumb = ImageCreate($imgw, $imgh);

            ImageCopyResized($thumb, $im, 0, 0, 0, 0, $imgw, $imgh, ImageSX($im), ImageSY($im));



            switch ($datos['type']) {

                case('image/pjpeg'): {

                        $name = time() . rand(1000, 99999) . '.jpg';

                        ImagejpeG($thumb, 'tmp/' . $name);

                        break;

                    }

                case('image/x-png'): {

                        $name = time() . rand(1000, 99999) . '.png';

                        Imagepng($thumb, 'tmp/' . $name);

                        break;

                    }

                case('image/gif'): {

                        $name = time() . rand(1000, 99999) . '.gif';

                        imagegif($thumb, 'tmp/' . $name);

                        break;

                    }

            }

            imagedestroy($im);

            imagedestroy($thumb);





            $res.= '<tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_img\',\'' . $datos['idimagenRepositorio'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Imagen\',\'' . $datos['idimagenRepositorio'] . '\')" title="Modificar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>&nbsp;' : '') .

                    (in_array('Descargar', $derechos) ? '<a href="download.php?tipo=2&id=' . $datos['idimagenRepositorio'] . '&origen=' . $origen . '&idorigen=' . $id . '" title="Descargar" target="_blank"><img src="' . DIR_TEMA_ACTIVO . '_img/download.png" width="18" height="18" alt="Descargar" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos['nombreFisico']) . '</td>

                <td>' . htmlspecialchars($datos['vig']) . '</td>

                <td>' . htmlspecialchars($datos['comentario']) . '</td>

                <td><img src="' . 'tmp/' . $name . '"/></td>



                </tr>';

        }





        $res.='</table><div id="res_enc" align="center">' . (@mysql_num_rows($resultado) <= 0 ? '<span>No se encontraron registros</span>' : '<span>Registros encontrados: ' . @mysql_num_rows($resultado) . ' </span>') . '</div>';

    } else {

        $res = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }



    return $res;

}



function resultados_personas($derechos, $origen, $id, $nombre = "", $ap = "", $am = "", $cargo = "", $col = "idpersonaRepositorio", $ordenar = "desc", $pag = "0", $idcliente = '') {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 20;



        $restab = '<a  name="Resultados"></a><table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="170">Opciones</td>

            <td width="170">Nombre</td>

            <td width="170">Apellido Paterno </td>

            <td width="170">Apellido Materno</td>

            <td width="160">Cargo</td>

  		    </tr>';



        switch ($origen) {

            case(1): {

                    $q = 'clientexpersona where  personarepositorio.idpersonaRepositorio=clientexpersona.idpersonaRepositorio and clientexpersona.idcliente';

                    break;

                }// contactos

            case(2): {

                    $q = 'usuarioxpersona,clientexusuario where  personarepositorio.idpersonaRepositorio=usuarioxpersona.idpersonaRepositorio and usuarioxpersona.idusuario=clientexusuario.usuario_idusuario and clientexusuario.clientes_idcliente';

                    break;

                } //cliente

            case(3): {

                    $q = 'usuarioxpersona where  personarepositorio.idpersonaRepositorio=usuarioxpersona.idpersonaRepositorio and  usuarioxpersona.idusuario not in (select usuario_idusuario from clientexusuario) and   usuarioxpersona.idusuario  in (select idusuario from usuarioxgrupo) and usuarioxpersona.idusuario';

                    break;

                }// sistema

            case(4): {

                    $q = 'usuarioxpersona where  personarepositorio.idpersonaRepositorio=usuarioxpersona.idpersonaRepositorio and  usuarioxpersona.idusuario not in (select idusuario from usuarioxgrupo) and  usuarioxpersona.idusuario';

                    break;

                } //compra

            case(5): {

                    $q = 'puntoxpersona where  personarepositorio.idpersonaRepositorio=puntoxpersona.idpersonaRepositorio and puntoxpersona.idpuntoRepositorio';

                    break;

                } //puntos

            case(6): {

                    $q = 'puntoxpersona where  personarepositorio.idpersonaRepositorio=puntoxpersona.idpersonaRepositorio and puntoxpersona.idpuntoRepositorio';

                    break;

                } //puntos

        }



        $filtros = ($nombre == '' ? '' : ' and personarepositorio.nombre like "%' . mysql_real_escape_string(trim($nombre)) . '%" ') . ($ap == '' ? '' : ' and personarepositorio.apellidoPaterno like "%' . mysql_real_escape_string(trim($ap)) . '%" ') . ($am == '' ? '' : ' and personarepositorio.apellidoMaterno like "%' . mysql_real_escape_string(trim($am)) . '%" ') . ($cargo == '' ? '' : ' and personarepositorio.cargo like "%' . mysql_real_escape_string(trim($cargo)) . '%" ');



        $resultado = @mysql_query('SELECT personarepositorio.idpersonaRepositorio,personarepositorio.cargo,personarepositorio.apellidoPaterno,personarepositorio.apellidoMaterno,personarepositorio.nombre FROM personarepositorio,' . $q . '="' . mysql_real_escape_string(($origen == '2' ? $idcliente : $id)) . '" ' . $filtros . ' order by personarepositorio.' . $col . ' ' . $ordenar . ' limit ' . ($pag * $recxpag) . ',' . $recxpag);





        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_persona\',\'' . $datos['idpersonaRepositorio'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Personas\',\'' . $datos['idpersonaRepositorio'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') .

                    (in_array('Domicilios', $derechos) ? '<a href="domicilios.php?id=' . $datos['idpersonaRepositorio'] . '&origen=3&ido=' . $id . '&origen2=' . $origen . '&idc=' . $idcliente . '" title="Ver Domicilios"><img src="' . DIR_TEMA_ACTIVO . '_img/dom.png" width="16" height="16" alt="Domicilio" /></a>' : '') .

                    (in_array('Archivos', $derechos) ? '<a href="archivos.php?id=' . $datos['idpersonaRepositorio'] . '&origen=3&ido=' . $id . '&origen2=' . $origen . '" title="Documentos"><img src="' . DIR_TEMA_ACTIVO . '_img/documentos.png" width="25" height="18" alt="Documentos" /></a>' : '') .

                    (in_array('Imagen', $derechos) ? '<a href="imagenes.php?id=' . $datos['idpersonaRepositorio'] . '&origen=3&ido=' . $id . '&origen2=' . $origen . '" title="Imagen"><img src="' . DIR_TEMA_ACTIVO . '_img/picture.png" width="18" height="18" alt="Imagen" /></a>' : '') .

                    (in_array('Correo', $derechos) ? '<a href="correos.php?id=' . $datos['idpersonaRepositorio'] . '&ido=' . $id . '&origen2=' . $origen . '" title="Correo"><img src="' . DIR_TEMA_ACTIVO . '_img/mail.png" width="18" height="18" alt="Correo" /></a>' : '') .

                    (in_array('Telefonos', $derechos) ? '<a href="telefonos.php?id=' . $datos['idpersonaRepositorio'] . '&origen=1&ido=' . $id . '&origen2=' . $origen . '" title="Teléfono"><img src="' . DIR_TEMA_ACTIVO . '_img/phone.png" width="21" height="18" alt="Teléfono" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos['nombre']) . '</td>

                <td>' . htmlspecialchars($datos['apellidoPaterno']) . '</td>

                <td>' . htmlspecialchars($datos['apellidoMaterno']) . '</td>

                <td>' . htmlspecialchars($datos['cargo']) . '</td></tr>';

        }



        $res = @mysql_query('SELECT personarepositorio.idpersonaRepositorio FROM personarepositorio,' . $q . '="' . mysql_real_escape_string(($origen == '2' ? $idcliente : $id)) . '" ' . $filtros);



        $total = @mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

        for ($i = 0; $i < $total; $i++) {

            if ($pag == $i) {

                $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

            } else {

                $lista .= '<a  href="#Resultados" onclick="paginar(\'' . ($i) . '\',\'' . $nombre . '|' . $ap . '|' . $am . '|' . $cargo . '|' . $col . '|' . $ordenar . '|' . $origen . '|' . $id . '\',\'personas_list2\')">&nbsp;' . $i . '&nbsp;</a>';

            }

        }



        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }



    return $restab;

}



function correos($derechos, $id) {

    if (in_array('Consultar', $derechos)) {



        $restab = '<a  name="Resultados"></a><table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="153">Opciones</td>

            <td width="170">Tipo</td>

            <td width="170">Correo </td>

            <td width="40">vigente</td>

            <td width="300">Comentario</td>

  		    </tr>';







        $resultado = @mysql_query('select correorepositorio.tipo,correorepositorio.idcorreorepositorio,correorepositorio.correo,correorepositorio.comentario,if (correorepositorio.vigente="0","No","Si") vig from correorepositorio,personaxcorreo where correorepositorio.idcorreorepositorio=personaxcorreo.idcorreorepositorio and personaxcorreo.idpersonaRepositorio="' . mysql_real_escape_string(trim($id)) . '"');



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='<tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_mail\',\'' . $datos['idcorreorepositorio'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Correo\',\'' . $datos['idcorreorepositorio'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>&nbsp;' : '') . '

                </td>

                <td>' . htmlspecialchars($datos['tipo']) . '</td>

                <td>' . htmlspecialchars($datos['correo']) . '</td>

                <td>' . htmlspecialchars($datos['vig']) . '</td>

                <td>' . htmlspecialchars($datos['comentario']) . '</td></tr>';

        }



        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . (@mysql_num_rows($resultado) > 0 ? '<span>Registros encontrados: ' . @mysql_num_rows($resultado) . '</span>' : '<span>No se encontraron registros</span>') . '</div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function telefonos($derechos, $id, $origen) {

    if (in_array('Consultar', $derechos)) {

        $res = '<table width="100%" border="0" cellspacing="2">

            <tr class="tabla_titulo">

            <td width="70">&nbsp;</td>

            <td width="120">Tipo</td>

            <td width="39">Pais</td>

            <td width="43">Código área</td>

            <td width="60">Número</td>

            <td width="60">Extensión</td>

            <td width="40">Vigente</td>

            <td width="180">Comentario</td>

            </tr>

            ';



        switch ($origen) {

            case(1): {

                    $t = 'personaxtelefono';

                    $idk = 'idpersonaRepositorio';

                    break;

                }

            case(2): {

                    $t = 'puntoxtelefono';

                    $idk = 'idpunto';

                    break;

                }

            case(3): {

                    $t = 'puntoxtelefono';

                    $idk = 'idpunto';

                    break;

                }

        }



        $resultado = @mysql_query('select telefonorepositorio.idtelefonoRepositorio, telefonorepositorio.tipo,telefonorepositorio.pais,telefonorepositorio.codigoarea,telefonorepositorio.numeroLocal,telefonorepositorio.extension,telefonorepositorio.comentario,if (telefonorepositorio.vigente="0","No","Si") vig from ' . $t . ',telefonorepositorio where ' . $t . '.' . $idk . '=' . $id . ' and ' . $t . '.idtelefonoRepositorio=telefonorepositorio.idtelefonoRepositorio');





        $fila = 1;



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $res.= '<tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_tel\',\'' . $datos['idtelefonoRepositorio'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Teléfono\',\'' . $datos['idtelefonoRepositorio'] . '\')" title="Modificar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos['tipo']) . '</td>

                <td>' . htmlspecialchars($datos['pais']) . '</td>

                <td>' . htmlspecialchars($datos['codigoarea']) . '</td>

                <td>' . htmlspecialchars($datos['numeroLocal']) . '</td>

                <td>' . htmlspecialchars($datos['extension']) . '</td>

                <td>' . htmlspecialchars($datos['vig']) . '</td>

                <td>' . htmlspecialchars($datos['comentario']) . '</td></tr>';

        }



        $res.='</table><div id="res_enc" align="center">' . (@mysql_num_rows($resultado) <= 0 ? '<span>No se encontraron registros</span>' : '<span>Registros encontrados: ' . @mysql_num_rows($resultado) . ' </span>') . '</div>';

    } else {

        $res = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $res;

}



function puntos($derechos, $idcenso, $razon = "", $latitud = "", $longitud = "", $ordenar = "desc", $pag = "1", $_origen = "", $UID = "") {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 50;

        $restab = '<a  name="Resultados"></a>

            <table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="180" nowrap>Opciones</td>

            <td width="60">Id punto</td>

            <td width="210">Razón Social</td>

            <td width="210">Nombre</td>

            <td width="210">UID</td>

            <td width="110">Latitud </td>

            <td width="110" nowrap>Longitud</td>' .

                //<td width="230">Censos</td>

                '<td width="230">Origen</td>

  		    </tr>';

        $fila = 0;

        $filtros = (trim($razon) == '' ? '' : ' and ((puntos.razonSocial like"%' . mysql_real_escape_string(trim($razon)) . '%") or (puntos.nombre like"%' . mysql_real_escape_string(trim($razon)) . '%"))') . (trim($latitud) == '' ? '' : ' and puntos.latitud like"%' . mysql_real_escape_string(trim($latitud)) . '%"') . (trim($longitud) == '' ? '' : ' and puntos.longitud like "%' . mysql_real_escape_string(trim($longitud)) . '%"');

        $filtros = $filtros . (trim($_origen) == '' ? '' : ' and puntos.origen like"%' . mysql_real_escape_string(trim($_origen)) . '%"') . (trim($UID) == '' ? '' : ' and puntos.UID like"' . mysql_real_escape_string(trim($UID)) . '%"');

        if (is_numeric($idcenso)) {

            $Q = 'SELECT puntos_censos(puntos.idpunto) censos_punto, puntos.origen, puntos.UID, puntos.idpunto,puntos.razonSocial,puntos.nombre,puntos.latitud,puntos.longitud FROM puntos,censoxpunto,clientexusuario,clientexcenso where clientexusuario.usuario_idusuario=' . mysql_real_escape_string($_SESSION['user']) . '

                and clientexusuario.clientes_idcliente=clientexcenso.idcliente and  clientexcenso.idcenso=censoxpunto.idcenso and censoxpunto.idpunto=puntos.idpunto and censoxpunto.idcenso="' . $idcenso . '" and puntos.puntopadre is null ';

            $Q2 = 'SELECT count(*) as contador FROM puntos,censoxpunto,clientexusuario,clientexcenso where clientexusuario.usuario_idusuario=' . mysql_real_escape_string($_SESSION['user']) . '

                and clientexusuario.clientes_idcliente=clientexcenso.idcliente and  clientexcenso.idcenso=censoxpunto.idcenso and censoxpunto.idpunto=puntos.idpunto and censoxpunto.idcenso="' . $idcenso . '" and puntos.puntopadre is null ';

        } else {

            //$Q='SELECT puntos_censos(idpunto) censos_punto, puntos.origen, idpunto, UID, razonSocial,puntos.nombre,latitud,longitud from puntos where  puntos.puntopadre is null ';

            $Q = 'SELECT " " censos_punto, puntos.origen, idpunto, UID, razonSocial,puntos.nombre,latitud,longitud from puntos where  puntos.puntopadre is null ';

            $Q2 = 'SELECT count(*) as contador from puntos where  puntos.puntopadre is null ';

        }



        //$resultado =@mysql_query($Q . $filtros . ' order by razonSocial '.$ordenar.' limit '.(($pag-1)*$recxpag).','.$recxpag);

        //$resultado =@mysql_query($Q . $filtros . ' order by idpunto '.$ordenar.' limit '.(($pag-1)*$recxpag).','.$recxpag);

        $resultado = @mysql_query($Q . $filtros . '  limit ' . (($pag - 1) * $recxpag) . ',' . $recxpag);

        $resultadoContador = @mysql_query($Q2 . $filtros);

        $datosContador = @mysql_fetch_assoc($resultadoContador);



        $id = (isset($id) ? $id : '');

        $origen = (isset($origen) ? $origen : '');

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='<tr valign="top">

                <td>' . 

                /*

					'<select class="webSelect" style="width:150px;" onchange="webSelectOnChange(this);" >

					<option value="" selected>Acciones</option>' .

					(in_array('Eliminar', $derechos)   ? '<option value="eliminar;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/delete.png">Eliminar</option>' : '') .

					(in_array('Modificar', $derechos)  ? '<option value="modificar;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/editar.jpg">Modificar</option>' : '') .

                    (in_array('Domicilios', $derechos) ? '<option value="domicilios;' . $datos['idpunto'] . ';' . $id . ';' . $origen . '" data-image="' . DIR_TEMA_ACTIVO . '_img/dom.png">Domicilio</option>' : '') .

                    (in_array('Archivos', $derechos)   ? '<option value="archivos;' . $datos['idpunto'] . ';' . $id . ';' . $origen . '" data-image="' . DIR_TEMA_ACTIVO . '_img/documentos.png">Archivos</option>' : '') .

                    (in_array('Imagen', $derechos)     ? '<option value="imagenes;' . $datos['idpunto'] . ';' . $id . ';' . $origen . '" data-image="' . DIR_TEMA_ACTIVO . '_img/picture.png">Imagen</option>' : '') .

                    (in_array('Telefonos', $derechos)  ? '<option value="telefonos;' . $datos['idpunto'] . ';' . $id . ';' . $origen . '" data-image="' . DIR_TEMA_ACTIVO . '_img/phone.png">Telefonos</option>' : '') .

                    (in_array('Personas', $derechos)   ? '<option value="personas;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/contactos.png">Personas</option>' : '') .

                    (in_array('Censos', $derechos)     ? '<option value="censos;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/censos.png">Censos</option> ' : '') .

*//**/

					(in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_punto\',\'' . $datos['idpunto'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

(in_array('Eliminar', $derechos) ? '<a href="../ficha_detalles_puntoCA.php?Id=' . $datos['idpunto'] . '" title="Ver Ficha"><img src="' . DIR_TEMA_ACTIVO . '_img/ficha.png" width="16" height="16" alt="Ficha" /></a>' : '') .
					
                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Punto\',\'' . $datos['idpunto'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') .

                    (in_array('Domicilios', $derechos) ? '<a href="domicilios.php?id=' . $datos['idpunto'] . '&origen=2&ido=' . $id . '&origen2=' . $origen . '" title="Ver Domicilios"><img src="' . DIR_TEMA_ACTIVO . '_img/dom.png" width="16" height="16" alt="Domicilio" /></a>' : '') .

                    (in_array('Archivos', $derechos) ? '<a href="archivos.php?id=' . $datos['idpunto'] . '&origen=2&ido=' . $id . '&origen2=' . $origen . '" title="Documentos"><img src="' . DIR_TEMA_ACTIVO . '_img/documentos.png" width="25" height="18" alt="Documentos" /></a>' : '') .

                    (in_array('Imagen', $derechos) ? '<a href="imagenes.php?id=' . $datos['idpunto'] . '&origen=2&ido=' . $id . '&origen2=' . $origen . '" title="Imagen"><img src="' . DIR_TEMA_ACTIVO . '_img/picture.png" width="18" height="18" alt="Imagen" /></a>' : '') .

                    (in_array('Telefonos', $derechos) ? ' <a href="telefonos.php?id=' . $datos['idpunto'] . '&origen=2&ido=' . $id . '&origen2=' . $origen . '" title="Teléfono"><img src="' . DIR_TEMA_ACTIVO . '_img/phone.png" width="21" height="18" alt="Telfono" /></a>' : '') .

                    (in_array('Personas', $derechos) ? '<a href="personas.php?id=' . $datos['idpunto'] . '&origen=5" title="Contactos"><img src="' . DIR_TEMA_ACTIVO . '_img/contactos.png" width="16" height="18" alt="Contactos" /></a>' : '') .

                    (in_array('Censos', $derechos) ? '<a href="javascript:mostrar_ventana(\'Censos\',\'' . $datos['idpunto'] . '\')" title="Censos"><img src="' . DIR_TEMA_ACTIVO . '_img/censos.png" width="18" height="18" alt="Censos" /></a> ' : '') . '

                <a href="http://maps.google.com.mx/?q=' . $datos['latitud'] . ',' . $datos['longitud'] . '" title="Ver punto en mapa" target="_blank"><img src="' . DIR_TEMA_ACTIVO . '_img/posicion_mapa.png" width="23" height="16" alt="ver punto en mapa" /></a></td>' .

/*				

					(in_array('Negocios', $derechos)   ? '<option value="negocios;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/negocios.png">Negocios</option> ' : '') .

					(in_array('NegociosCompras', $derechos)   ? '<option value="negociosCompras;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/negocioscompras.png">Negocios Compras</option> ' : '') .

					(in_array('NegociosInfraExt', $derechos)   ? '<option value="negociosInfraExt;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/negociosinfraext.png">Negocios Infra Ext.</option> ' : '') .

					(in_array('NegociosInfraInt', $derechos)   ? '<option value="negociosInfraInt;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/negociosinfraint.png">Negocios Infra Int.</option> ' : '') .

					(in_array('NegociosVentas', $derechos)   ? '<option value="negocios;' . $datos['idpunto'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/negociosventas.png">Negocios Ventas</option> ' : '') .

				'<option value="ver_mapa;' . $datos['latitud'] . ';' . $datos['longitud'] . '" data-image="' . DIR_TEMA_ACTIVO . '_img/posicion_mapa.png">Ver en mapa</option>

				</select></td>' .

*/

                '<td>' . htmlspecialchars($datos['idpunto']) . '</td>

                <td>' . htmlspecialchars($datos['razonSocial']) . '</td>

                <td>' . htmlspecialchars($datos['nombre']) . '</td>

                <td>' . htmlspecialchars($datos['UID']) . '</td>

                <td>' . htmlspecialchars($datos['latitud']) . '</td>

                <td>' . htmlspecialchars($datos['longitud']) . '</td>' .

                    //<td>'.str_replace("||", "<br />",htmlspecialchars(substr($datos['censos_punto'],2))).'</td>

                    '<td>' . htmlspecialchars($datos['origen']) . '</td>

                </tr>';

            $fila+=1;

        }



        $res = @mysql_query($Q . $filtros);



        $total = @mysql_num_rows($res);

        $total = ceil($total / $recxpag);



        $lista = '';



        if ($total <= 11) {

            for ($i = 1; $i <= $total; $i++) {

                if ($pag == ($i)) {

                    $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

                } else {

                    $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . $i . '&nbsp;</a>';

                }

            }

        } elseif ($pag < 6) {

            for ($i = 1; $i <= 11; $i++) {

                if ($pag == ($i)) {

                    $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

                } else {

                    $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . $i . '&nbsp;</a>';

                }

            }

        } elseif (($pag + 5) <= $total) {

            $lista = '<a  href="#Resultados" onclick="paginar(\'' . ($pag - 5) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag - 5) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 4) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag - 4) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 3) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag - 3) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 2) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag - 2) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 1) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag - 1) .

                    '&nbsp;</a> <span>&nbsp;[' . $pag . ']&nbsp;</span><a  href="#Resultados" onclick="paginar(\'' . ($pag + 1) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag + 1) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 2) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag + 2) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 3) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag + 3) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 4) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag + 4) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 5) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag + 5) . '&nbsp;</a> ';

        } else {

            $hasta = $total - $pag;



            for ($i = 1; $i <= $hasta; $i++) {

                $lista.= '<a  href="#Resultados" onclick="paginar(\'' . ($pag + $i) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag + $i) . '&nbsp;</a>';

            }



            $lista = '<span>&nbsp;[' . $pag . ']&nbsp;</span>' . $lista;



            $hasta = 10 - $hasta;

            for ($i = 2; $i < $hasta; $i++) {

                $lista = '<a  href="#Resultados" onclick="paginar(\'' . ($pag - $i) . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;' . ($pag - $i) . '&nbsp;</a>' . $lista;

            }

        }



        if ($pag > 1) {

            $lista = '<a  href="#Resultados" onclick="paginar(\'1\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">Primera&nbsp;&nbsp;</a>' . $lista;

        }

        if ($pag < $total) {

            $lista.='<a  href="#Resultados" onclick="paginar(\'' . $total . '\',\'' . $idcenso . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '|' . $_origen . '|' . $UID . '\',\'puntos2\')">&nbsp;&nbsp;&Uacute;ltima</a>';

        }





        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

        $restab = $datosContador['contador'] . ' registros' . $restab;

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    //return $Q.$filtros;

    return $restab;

}



function usuarios($derechos, $restriccion_acceso, $idcliente, $sistema = 'true', $compras = 'true', $cliente = 'true', $ordenar = 'asc', $pag = 0, $col = 'idusuario') {

    if (in_array('Consultar', $derechos)) {



        $recxpag = 50;

        $restab = '<a  name="Resultados"></a><table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="70">Opciones</td>

            <td width="150">Usuario</td>

            <td width="350">Nombre </td>

            <td width="220">Cliente </td>

            <td width="160">Tipo usuario </td>

  		    </tr>';

        $query = '  usuario.idusuario,concat(personarepositorio.nombre,\' \',personarepositorio.apellidoPaterno,\' \',personarepositorio.apellidoMaterno) name,clientes.razonSocial FROM (usuario left join usuarioxpersona on usuarioxpersona.idusuario=usuario.idusuario left join personarepositorio on usuarioxpersona.idpersonaRepositorio=personarepositorio.idpersonaRepositorio) left join (clientexusuario,clientes ) on clientexusuario.usuario_idusuario=usuario.idusuario and clientes.idcliente=clientexusuario.clientes_idcliente';

        $query.=' where (1=1)  	';

        $q = '';

        if ($restriccion_acceso != '') {

            $idcliente = $restriccion_acceso;

        }

        if ($idcliente == '') {

            if ($sistema == 'true') {

                $q = 'SELECT "3" origen, "Sistema" tipo,"" idcliente, ' . $query . ' and clientes.razonSocial is null and usuario.idusuario in (select idusuario from usuarioxgrupo) ';

            }

            if ($compras == 'true') {

                $q.=($q == '' ? '' : ' union ') . '  SELECT "4" origen,"Compras" tipo,"" idcliente, ' . $query . ' and usuario.idusuario not in (select idusuario from usuarioxgrupo) and usuario.idusuario not in ( select usuario_idusuario from clientexusuario ) ';

            }

        }



        if ($cliente == 'true') {

            $q.=($q == '' ? '' : ' union ') . ' SELECT "2" origen,"Cliente" tipo,clientes.idcliente idcliente, ' . $query . ' and clientes.razonSocial is not null ' . ($idcliente != '' ? 'and clientes.idcliente="' . mysql_real_escape_string(trim($idcliente)) . '"' : '') . ($restriccion_acceso != '' ? 'and clientes.idcliente="' . mysql_real_escape_string($restriccion_acceso) . '"' : '');

        }



        $resultado = @mysql_query($q . ' order by  ' . $col . ' ' . $ordenar . ' limit ' . ($pag * $recxpag) . ',' . $recxpag);



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='

                <tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_user\',\'' . $datos['idusuario'] . '\')" onclick="return window.confirm(\'El Usuario (' . $datos ['idusuario'] . ') ' . $datos['name'] . ' va a ser eliminado. ¿Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana_especial(\'Modificar usuario\',\'' . $datos['idusuario'] . '|' . $datos['origen'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') .

                    (in_array('Personas', $derechos) ? '<a href="personas.php?id=' . $datos['idusuario'] . '&origen=' . $datos['origen'] . '&ido=' . $datos['idcliente'] . '" title="Contactos"><img src="' . DIR_TEMA_ACTIVO . '_img/contactos.png" width="16" height="18" alt="Contactos" /></a></td>' : '') . '

                <td>' . htmlspecialchars($datos['idusuario']) . '</td>

                <td>' . htmlspecialchars($datos['name']) . '</td>

                <td>' . htmlspecialchars($datos['razonSocial']) . '</td>

                <td>' . htmlspecialchars($datos['tipo']) . '</td>

                </tr>';

        }



        $res = @mysql_query($q);

        $total = @mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

        for ($i = 0; $i < $total; $i++) {

            if ($pag == $i) {

                $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

            } else {

                $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $id . '|' . $idcliente . '|' . $sistema . '|' . $compras . '|' . $cliente . '|' . $ordenar . '|' . $col . '\',\'usuarios\')">&nbsp;' . $i . '&nbsp;</a>';

            }

        }



        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function gruposSubPoligonos ( $derechos, $filtro = '', $ordenar = 'Asc', $pag = '0'){

    if (in_array('Consultar', $derechos)) {

        $recxpag = 20;

        $restab = '<a  name="Resultados"></a>

					<table width="100%" border="0" cellspacing="2">

						<tr class="tabla_titulo">

							<td width="70">Opciones</td>

							<td width="300">Nombre</td>

							<td width="500">Descripción </td>

						</tr>';

        $resultado = @mysql_query('SELECT idGrupoSubPoligono, descripcion FROM gruposSubPoligonos ' . 

								( ( trim ( $filtro ) != '' ) ? ' where idGrupoSubPoligono like "%' . mysql_real_escape_string ( trim ( $filtro ) ) . '%"' : '') . 

								' order by idGrupoSubPoligono ' . $ordenar . ' limit ' . ( $pag * $recxpag ) . ',' . $recxpag );

			while ($datos = @mysql_fetch_assoc($resultado)) {

				$restab.='

					<tr>

					<td>' . 

						( in_array ( 'Eliminar'   , $derechos) ? '<a href="javascript:eliminar_registro(\'del_grupoSubPol\',\'' . $datos['idGrupoSubPoligono'] . '\')" onclick="return window.confirm(\'El registro va a ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '' ) .

					    ( in_array ( 'Modificar'  , $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar GrupoSubPol\',\'' . $datos['idGrupoSubPoligono'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '' ) .

					'<td>' . htmlspecialchars($datos['idGrupoSubPoligono']) . '</td>

					 <td>' . htmlspecialchars($datos['descripcion']) . '</td>

					</tr>';

			}

        $res = @mysql_query ( 'SELECT idGrupoSubPoligono, descripcion FROM gruposSubPoligonos ' . 

								( ( trim ( $filtro ) != '' ) ? ' where idGrupoSubPoligono like "%' . mysql_real_escape_string ( trim ( $filtro ) ) . '%"' : '' ) );

        $total = @mysql_num_rows ( $res );

        $total = ceil( $total / $recxpag );

        $lista = '';

			for ($i = 0; $i < $total; $i++) {

				if ($pag == $i) {

					$lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

				}else{

					$lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $filtro . '|' . $ordenar . '\',\'grupos\')">&nbsp;' . $i . '&nbsp;</a>';

				}

			}

        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function grupos($derechos, $filtro = '', $ordenar = 'Asc', $pag = '0') {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 20;

        $restab = '<a  name="Resultados"></a>

					<table width="100%" border="0" cellspacing="2">

						<tr class="tabla_titulo">

							<td width="70">Opciones</td>

							<td width="300">Nombre</td>

							<td width="500">Descripción </td>

						</tr>';

        $resultado = @mysql_query('SELECT idgrupo,descripcion FROM grupo ' . ((trim($filtro) != '') ? ' where idgrupo like "%' . mysql_real_escape_string(trim($filtro)) . '%"' : '') . ' order by idgrupo ' . $ordenar . ' limit ' . ($pag * $recxpag) . ',' . $recxpag);

			while ($datos = @mysql_fetch_assoc($resultado)) {

				$restab.='

					<tr>

					<td>' . 

						( in_array ( 'Eliminar'   , $derechos) ? '<a href="javascript:eliminar_registro(\'del_group\',\'' . $datos['idgrupo'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

					    ( in_array ( 'Modificar'  , $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Grupo\',\'' . $datos['idgrupo'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') .

						( in_array ( 'Privilegios', $derechos) ? '<a href="javascript:mostrar_ventana(\'Privilegios\',\'' . $datos['idgrupo'] . '\')" title="Privilegios"><img src="' . DIR_TEMA_ACTIVO . '_img/privilegios.png" width="18" height="18" alt="Privilegios" /></a>' : '') . '

					<td>' . htmlspecialchars($datos['idgrupo']) . '</td>

					<td>' . htmlspecialchars($datos['descripcion']) . '</td>

					</tr>';

			}

        $res = @mysql_query('SELECT idgrupo,descripcion FROM grupo ' . ((trim($filtro) != '') ? ' where idgrupo like "%' . mysql_real_escape_string(trim($filtro)) . '%"' : ''));

        $total = @mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

			for ($i = 0; $i < $total; $i++) {

				if ($pag == $i) {

					$lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

				}else{

					$lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $filtro . '|' . $ordenar . '\',\'grupos\')">&nbsp;' . $i . '&nbsp;</a>';

				}

			}

        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function resultados_censos($derechos, $idcliente, $nombre = '', $ordenar = '', $pag = 0) {

    if (in_array('Consultar', $derechos)) {

        $restriccion_acceso = tipo_usuario();

        $recxpag = 20;

        $restab = '<a  name="Resultados"></a>

			<table width="100%" border="0" cellspacing="2">

				<tr class="tabla_titulo">

            		<td width="90">Opciones</td>

            		<td width="40">Id censo</td>

            		<td width="230">Nombre del censo</td>

            		<td width="80">Inicio</td>

            		<td width="80">Fin</td>

            		<td width="250">Objetivo </td>

  		    	</tr>';

        if ($idcliente != '') {

            $q = ',clientexcenso where clientexcenso.idcenso=censos.idcenso and clientexcenso.idcliente="' . mysql_real_escape_string(trim($idcliente)) . '"' . ($restriccion_acceso == '' ? '' : '  and  clientexcenso.idcliente="' . mysql_real_escape_string($restriccion_acceso) . '"');

        } else {

            $q = ($restriccion_acceso == '' ? '' : ' ,clientexcenso where clientexcenso.idcenso=censos.idcenso and clientexcenso.idcliente="' . mysql_real_escape_string($restriccion_acceso) . '"');

        }





        $query = ' SELECT censos.idcenso,censos.nombrecenso,censos.objetivo,DATE_FORMAT(inicioLevantamiento, \'%d/%m/%Y\') inicio,DATE_FORMAT(terminoLevantamiento,\'%d/%m/%Y\') fin FROM censos' . $q . ($nombre != '' ? ($q != '' ? ' and censos.nombrecenso like "%' . mysql_real_escape_string($nombre) . '%"' : ' where  censos.nombrecenso like "%' . mysql_real_escape_string($nombre) . '%"') : '');

        $resultado = @mysql_query($query . ' order by  censos.nombrecenso ' . $ordenar . ' limit ' . ($pag * $recxpag) . ',' . $recxpag);

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='

                <tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_censo\',\'' . $datos['idcenso'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Censo\',\'' . $datos['idcenso'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') .

                    (in_array('Preguntas', $derechos) ? '<a href="preguntas_censos.php?idcenso=' . $datos['idcenso'] . '" title="Preguntas del censo"><img src="' . DIR_TEMA_ACTIVO . '_img/pregunta.png" width="16" height="16" alt="Preguntas del censo" /></a>' : '') .

                    (in_array('Puntos', $derechos) ? '<a href="puntosxcenso.php?idcenso=' . $datos['idcenso'] . '" title="Puntos del censo"><img src="' . DIR_TEMA_ACTIVO . '_img/punto.png" width="19" height="16" alt="Puntos del censo" /></a>' : '') .

                    '<td>' . $datos['idcenso'] . '</td>

                <td>' . htmlspecialchars($datos['nombrecenso']) . '</td>

                <td>' . htmlspecialchars($datos['inicio']) . '</td>

                <td>' . htmlspecialchars($datos['fin']) . '</td>

                <td>' . htmlspecialchars($datos['objetivo']) . '</td>

                </tr>';

        }



        $res = @mysql_query($query);

        $total = @mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

        for ($i = 0; $i < $total; $i++) {

            if ($pag == $i) {

                $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

            } else {

                $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $idcliente . '|' . $nombre . '|' . $ordenar . '\',\'censos\')">&nbsp;' . $i . '&nbsp;</a>';

            }

        }



        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

    } else {

        $restab = ' </table><div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function clientes_censos($idcliente) {

    $resultado = @mysql_query('SELECT idcliente,razonsocial FROM clientes  order by razonsocial');





    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {



        $opcion.='<option value="' . $datos['idcliente'] . ' " ' . ($idcliente == $datos['idcliente'] ? ' selected="selected"' : '') . '>' . htmlspecialchars($datos['razonsocial']) . '</option>';

		

    }



    return $opcion;

}



function paginas_p($idgrupo, $id, $all) {



    $resultado = @mysql_query('SELECT idpagina,nombre FROM paginas' . (($all == 0) ? ' where idpagina in (SELECT idpagina FROM grupoxderechos where idgrupo="' . mysql_real_escape_string($idgrupo) . '")' : '') . ' order by nombre');



    $opcion = '';

    $q = '';

    while ($datos = mysql_fetch_assoc($resultado)) {

        $opcion.='<option value="' . $datos['idpagina'] . '" ' . ($id == $datos['idpagina'] ? ' selected="selected"' : '') . '>' . htmlspecialchars($datos['nombre']) . '</option>';

        if ($id == $datos['idpagina']) {

            $q = '1';

        }

    }



    if (($q == '') && ($opcion != '') && ($all == 0)) {

        $opcion = '<option value="">&nbsp;</option>' . $opcion;

    }



    if ($all == 1)

        $q = 'select idpagina,nombre from derechos where idpagina="' . $id . '" and nombre not in (select nombre from grupoxderechos where idpagina="' . mysql_real_escape_string($id) . '" and idgrupo="' . mysql_real_escape_string($idgrupo) . '") order by nombre';

    else

        $q = 'select idpagina,nombre from grupoxderechos where idpagina="' . mysql_real_escape_string($id) . '" and idgrupo="' . mysql_real_escape_string($idgrupo) . '"';



    $resultado = @mysql_query($q);



    $privilegio = '';

    while ($datos = mysql_fetch_assoc($resultado)) {

        $privilegio.='<option value="' . $datos['idpagina'] . ' ">' . htmlspecialchars($datos['nombre']) . '</option>';

    }







    return array($opcion, $privilegio);

}



function etiqueta($id) {

    global $etiqueta;



    $result = '';

    $x = '';

    for ($i = 0; $i < count($etiqueta); $i++) {

        $x.=$id . '-' . $etiqueta[$i]['padre'];

        if ($id == $etiqueta[$i]['padre']) {

            $result.='{ title: "' . $etiqueta[$i]['descripcion'] . '" ,key: "' . $id . '|' . $etiqueta[$i]['id'] . '"},';

        }

    }



    if ($result != '') {

        $result = ',children: [' . substr($result, 0, -1) . ']';

    }





    return $result;

}



function GeneraCadena($indice) {

    global $categoria;



	//die('entro aqui');

    $Result = '';

    $Rutas = '';

    $r = '';

    if ($indice == -1) {

        for ($i = 0; $i < count($categoria); $i++) {

            if ($categoria[$i]['padre'] == '') {

                if (strlen($Rutas) > 0) {

                    $Result.= ', ';

                }



                $Result .= '{title: "' . $categoria[$i]['descripcion'] . '" ' . GeneraCadena($i) . '}';

                $Rutas.=$Result;

                $Result = '';

            }

            $r = $Rutas;

        }

    } else

    if ($categoria[$indice]['id'] != '') {

        $idPadre = $categoria[$indice]['id'];

        for ($i = 0; $i < count($categoria); $i++) {

            if ($categoria[$i]['padre'] == $idPadre) {

                if ($Result == '') {

                    $Result = ', "children": [ ';

                } else {

                    $Result.=', ';

                }

                $Result.='{ title: "' . $categoria[$i]['descripcion'] . '" ' . ($categoria[$i]['idpreg'] != '' ? etiqueta($categoria[$i]['idpreg']) : ' ') . GeneraCadena($i) . '}';

            }

        }

        if ($Result != '') {

            $Result.= ']';

        }

        $r = $Result;

    }

    return $r;

}



function treeview() {

    //( 'idCategoria', 'descripcion', 'categoriaPadre' );

    global $etiqueta, $categoria;

    if (conectar()) {

        $resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias  union all select "" id,idpregunta idpreg,  pregunta des,idcategoria padre  from preguntas');

        while ($datos = mysql_fetch_assoc($resultado)) {

            $categoria[] = array('id' => $datos['id'], 'descripcion' => $datos['des'], 'padre' => $datos['padre'], 'idpreg' => $datos['idpreg']);

        }

        $resultado = @mysql_query('SELECT idetiqueta,idpregunta,valoretiqueta FROM etiqueta');

        while ($datos = mysql_fetch_assoc($resultado)) {

            $etiqueta[] = array('id' => $datos['idetiqueta'], 'descripcion' => $datos['valoretiqueta'], 'padre' => $datos['idpregunta']);

        }

        //$tmp='treeData = ['.GeneraCadena(-1,$categoria). '];';

        $tmp = 'treeData =[];';

        return $tmp;

    }

    //return 'treeData=[]';

}



function etiquetaJSON2($id) {

    global $etiqueta;

    $result = '';

    $x = '';

    for ($i = 0; $i < count($etiqueta); $i++) {

        $x.=$id . '-' . $etiqueta[$i]['padre'];

        if ($id == $etiqueta[$i]['padre']) {

            $result.='{ "title": "' . $etiqueta[$i]['descripcion'] . '", "key": "' . $id . '|' . $etiqueta[$i]['id'] . '"},';

        }

    }

    if ($result != '') {

        $result = ',"children": [' . substr($result, 0, -1) . ']';

    }

    return $result;

}



function GeneraCadenaJSON2($indice) {

    global $categoria;

    $Result = '';

    $Rutas = '';

    $r = '';

    if ($indice == -1)

        $indice = '';

    for ($i = 0; $i < count($categoria); $i++) {

        if ($categoria[$i]['padre'] == $indice) {

            if (strlen($Rutas) > 0) {

                $Result.= ', ';

            }

            if ($categoria[$i]['idpreg'] != '') {

                $Result .= '{"title": "' . /*'[idpreg=' . $categoria[$i]['idpreg'] . ']' .*/ $categoria[$i]['descripcion'] . '", "key":"preg_' . $categoria[$i]['idpreg'] . '", "isLazy": false ' . etiquetaJSON($categoria[$i]['idpreg']) . '}';

            } else {

                $Result .= '{"title": "' . /*'[id=' . $categoria[$i]['id'] . ']' .*/ $categoria[$i]['descripcion'] . '", "key":"' . $categoria[$i]['id'] . '", "isLazy": true}';

            }

            $Rutas.=$Result;

            $Result = '';

        }

        $r = $Rutas;

    }

    return $r;

}



function treeviewJSON2() {

    //( 'idCategoria', 'descripcion', 'categoriaPadre' );

    global $etiqueta, $categoria;

    if (conectar()) {

        $resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias union all select "" id,idpregunta idpreg, pregunta des,idcategoria padre from preguntas');



        while ($datos = mysql_fetch_assoc($resultado)) {

            $categoria[] = array('id' => $datos['id'], 'descripcion' => $datos['des'], 'padre' => $datos['padre'], 'idpreg' => $datos['idpreg']);

        }



        $resultado = @mysql_query('SELECT idetiqueta,idpregunta,valoretiqueta FROM etiqueta');



        while ($datos = mysql_fetch_assoc($resultado)) {

            $etiqueta[] = array('id' => $datos['idetiqueta'], 'descripcion' => $datos['valoretiqueta'], 'padre' => $datos['idpregunta']);

        }



        $tmp = '[' . GeneraCadenaJSON2($_REQUEST['key']) . ']';

        return $tmp;

    }

}



function etiquetaJSON($id) {

    global $etiqueta;



    $result = '';

    $x = '';

    for ($i = 0; $i < count($etiqueta); $i++) {

        $x.=$id . '-' . $etiqueta[$i]['padre'];

        if ($id == $etiqueta[$i]['padre']) {

            $result.='{ "title": "' . $etiqueta[$i]['descripcion'] . '", "key": "' . $id . '|' . $etiqueta[$i]['id'] . '", "isLazy": false},';

        }

    }



    if ($result != '') {

        $result = ', "children": [' . substr($result, 0, -1) . ']';

    }





    return $result;

}



function GeneraCadenaJSON($indice) {

    global $categoria;

    $Result = '';

    $Rutas = '';

    $r = '';

    if (strpos($indice, "preg_") === 0) {

        $r = etiquetaJSON(substr($indice, 5));

        //$r = 'aqui';

    } else {

        if ($indice == -1) {

            for ($i = 0; $i < count($categoria); $i++) {

                if ($categoria[$i]['padre'] == '') {

                    if (strlen($Rutas) > 0) {

                        $Result.= ', ';

                    }



                    $Result .= '{"title": "' . $categoria[$i]['descripcion'] . '", "key":"' . $categoria[$i]['id'] . '", "isLazy": true}';

                    $Rutas.=$Result;

                    $Result = '';

                }

                $r = $Rutas;

            }

        }

        else

            for ($i = 0; $i < count($categoria); $i++) {

                if ($categoria[$i]['padre'] == $indice) {

                    if (strlen($Rutas) > 0) {

                        $Result.= ', ';

                    }



                    if ($categoria[$i]['idpreg'] != '') {

                        //$Result .= '{ "title": "' . $categoria[$i]['descripcion']. '", "key":"'. $categoria[$i]['id'] . '", "isLazy": false"' . etiquetaJSON($categoria[$i]['idpreg']) . '}';

                        $Result .= '{"title": "' . $categoria[$i]['descripcion'] . '", "key":"preg_' . $categoria[$i]['idpreg'] . '", "isLazy": false ' . etiquetaJSON($categoria[$i]['idpreg']) . '}';

                    } else {

                        $SAux = GeneraCadenaJSON($categoria[$i]['id']);

                        if ($SAux != "")

                            $SAux = ', "children":[' . $SAux . ']';

                        $Result .= '{"title": "' . $categoria[$i]['descripcion'] . '", "key":"' . $categoria[$i]['id'] . '", "isLazy": false' . $SAux . '}';

                    }

                    $Rutas.=$Result;

                    $Result = '';

                }

                $r = $Rutas;

            }

    }

    return $r;

}



function treeviewJSON() {

    //( 'idCategoria', 'descripcion', 'categoriaPadre' );

    global $etiqueta, $categoria;

    if (conectar()) {

        $resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias  union all select "" id,idpregunta idpreg,  pregunta des,idcategoria padre  from preguntas');



        while ($datos = mysql_fetch_assoc($resultado)) {

            $categoria[] = array('id' => $datos['id'], 'descripcion' => $datos['des'], 'padre' => $datos['padre'], 'idpreg' => $datos['idpreg']);

        }



        $resultado = @mysql_query('SELECT idetiqueta,idpregunta,valoretiqueta FROM etiqueta');



        while ($datos = mysql_fetch_assoc($resultado)) {

            $etiqueta[] = array('id' => $datos['idetiqueta'], 'descripcion' => $datos['valoretiqueta'], 'padre' => $datos['idpregunta']);

        }



        $tmp = '[' . GeneraCadenaJSON($_REQUEST['key']) . ']';

        return $tmp;

    }

    else

        "[]";

}



function etiquetaJSON3($id) {

    $result = '';

    $x = '';

    $resultado = @mysql_query('SELECT idetiqueta,idpregunta,valoretiqueta FROM etiqueta WHERE idpregunta="' . $id . '"');

	//die ( 'SELECT idetiqueta,idpregunta,valoretiqueta FROM etiqueta WHERE idpregunta="' . $id . '"' );

    //Si hay solo una respuesta se regresa solo el id de la etiqueta para que se acomode!.



    if (mysql_affected_rows() == 1) {

        $datos = mysql_fetch_assoc($resultado);

        $result = $datos['idetiqueta'];

    } else {

        while ($datos = mysql_fetch_assoc($resultado)) {

            $result.='{ "title": "' . $datos['valoretiqueta'] . '", "key": "' . $id . '|' . $datos['idetiqueta'] . '", "isLazy": false},';

        }

        if ($result != '') {

            $result = ', "children": [' . substr($result, 0, -1) . ']';

        }

    }

    return $result;

}



function GeneraCadenaJSON3($indice) {
    //@Rafael
    //Arbol del inicio

    $Rutas = '';

    $r = '';

	//echo ( 'entro aqui' );

    if (($indice == -1) || ($indice == '')) {

        $resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias WHERE (categoriaPadre="") or (categoriaPadre is null)');

        while ($datos = mysql_fetch_assoc($resultado)) {

            if (strlen($Rutas) > 0) {

                $Rutas.= ', ';

            }

            $Rutas .= '{"title": "' . $datos['des'] . '", "key":"' . $datos['id'] . '", "isLazy": true}';

        }

        $r = $Rutas;

    } else {

        $resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias WHERE categoriaPadre="' . $indice . 

									'" union all select "" id,idpregunta idpreg, pregunta des,idcategoria padre from preguntas WHERE idcategoria="' . $indice . 

									'" and ( esEtiquetada = 1 or esEtiquetada is null ) ' );

        while ($datos = mysql_fetch_assoc($resultado)) {

            if (strlen($Rutas) > 0) {

                $Rutas.= ', ';

            }

            if ($datos['idpreg'] != '') {

                $etiquetas = etiquetaJSON3($datos['idpreg']);

                if (($etiquetas != '') && (strpos($etiquetas, 'children') === false )) {

                    $Rutas .= '{"title": "' . $datos['des'] . '", "key":"' . $datos['idpreg'] . '|' . $etiquetas . '", "isLazy": false}';

                } else {

                    $Rutas .= '{"title": "' . $datos['des'] . '", "key":"preg_' . $datos['idpreg'] . '", "isLazy": false ' . $etiquetas . '}';

                }

            } else {

                $SAux = GeneraCadenaJSON3($datos['id']);

                if ($SAux != "")

                    $SAux = ', "children":[' . $SAux . ']';

                $Rutas .= '{"title": "' . $datos['des'] . '", "key":"' . $datos['id'] . '", "isLazy": false' . $SAux . '}';

            }

        }

        $r = $Rutas;

    }

    return $r;

}



function treeviewJSON3() {

    if (conectar()) {

        $tmp = '[' . GeneraCadenaJSON3($_REQUEST['key']) . ']';

        return $tmp;

    }

    else

        "[]";

}



function relacionar() {



    for ($x = 1000; $x <= 2000; $x++) {



        $r.='("19","' . $x . '"),';

    }



    die('insert into censoxpunto (idcenso,idpunto)  values ' . $r);

}



function zonas_ageps($derechos) {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 1;

        $restab = '<a name="Resultados"></a><table width="100%" border="0" cellspacing="2">

							<tr class="tabla_titulo">

            					<td width="70">Opciones</td>

            					<td>Nombre del Ageb</td>

  		    				</tr>';

        $resultado = @mysql_query('SELECT idageb, nombre FROM ageb ORDER BY nombre');

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab .= '<tr>

                						<td>' . ( in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_zona\',\'' .

                            $datos ['idageb'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" ' .

                            'title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '' ) .

                    ( in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Agebs\',\'' .

                            $datos ['idageb'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '' ) .

                    ( in_array('Archivos ageb', $derechos) ? '<a href="archivos_ageb.php?id=' . $datos ['idageb'] .

                            '" title="Administrar Archivos Ageps"><img src="' . DIR_TEMA_ACTIVO . '_img/agep.png" width="18" height="18" alt="Administrar Archivos Ageps" />' .

                            '</a>' : '' ) . '

                						<td>' . htmlspecialchars($datos ['nombre']) . '</td>

                					</tr>';

        }

        $restab .= '</table><br/><br/><div id="res_enc" align="center"><span>' . @mysql_num_rows($resultado) . '  Registros Encontrados</span></div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function ageps($derechos, $id) {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 1;

        $restab = '<a  name="Resultados"></a><table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="70">Opciones</td>

            <td >Archivo</td>

  		    </tr>';



        $resultado = @mysql_query('SELECT idagebxpath,path FROM agebxpath where idageb="' . mysql_real_escape_string($id) . '"order by path ');

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='

                <tr>

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_agep\',\'' . $datos['idagebxpath'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Ageb\',\'' . $datos['idagebxpath'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') . '

                <td>' . htmlspecialchars($datos['path']) . '</td>



                </tr>';

        }





        $restab.=' </table><br/><br/><div id="res_enc" align="center"><span>' . @mysql_num_rows($resultado) . '  Registros Encontrados</span></div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function view_agepbs() {

    $restab = '';

    $resultado = @mysql_query('SELECT idageb,nombre FROM ageb order by nombre ');

    while ($datos = @mysql_fetch_assoc($resultado)) {

        $restab .= '<input type="checkbox"  onclick="ageps(this)"  value="' . $datos['idageb'] . '" name="' . $datos['idageb'] . '"/>' . htmlspecialchars($datos['nombre']) . '<br/>';

    }

    return $restab;

}



function censosxpunto($ipunto, $all = 0) {



    if ($all == 0) {

        $resultado = @mysql_query('SELECT  censos.nombreCenso,censos.idcenso FROM censos where censos.idcenso not in (select distinct idcenso from censoxpunto where idpunto="' . mysql_real_escape_string($ipunto) . '") order by censos.nombreCenso');

    } else {//die('SELECT distinct censos.nombreCenso,censos.idcenso FROM censos,censoxpunto where censoxpunto.idpunto="'.mysql_real_escape_string($ipunto).'" and censoxpunto.idcenso=censos.idcenso order by censos.nombreCenso');

        $resultado = @mysql_query('SELECT distinct censos.nombreCenso,censos.idcenso FROM censos,censoxpunto where censoxpunto.idpunto="' . mysql_real_escape_string($ipunto) . '" and censoxpunto.idcenso=censos.idcenso order by censos.nombreCenso');

    }



    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {

        $opcion.='<option value="' . $datos['idcenso'] . '">' . htmlspecialchars($datos['nombreCenso']) . '</option>';

    }



    return $opcion;

}



function preguntas($derechos, $pag = 0, $preg = '', $order = '') {

    if (in_array('Consultar', $derechos)) {

        $recxpag = 50;

        $restab = '<a  name="Resultados"></a><table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="70">Opciones</td>

            <td width="25">Id</td>             

            <td width="300">Pregunta</td>

            <td width="300">Categoría </td>

            <td width="220">Etiqueta </td>

  		    </tr>';



        $resultado = @mysql_query('select etiquetas_valor(preguntas.idpregunta) etiq, preguntas.pregunta,preguntas.idpregunta,categorias.descripcion,preguntas.idcategoria from preguntas,categorias where categorias.idcategoria=preguntas.idcategoria ' . ($preg != '' ? ' and  preguntas.pregunta like "' . mysql_real_escape_string($preg) . '%"' : '') . '  order by preguntas.pregunta ' . $order . ' limit  ' . ($pag * $recxpag) . ',' . $recxpag);

        $resultadoContador = @mysql_query('Select count(*) as contador from preguntas,categorias where categorias.idcategoria=preguntas.idcategoria ' . ($preg != '' ? ' and  preguntas.pregunta like "' . mysql_real_escape_string($preg) . '%"' : '') . '  order by preguntas.pregunta ' . $order);

        $datosContador = @mysql_fetch_assoc($resultadoContador);



        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='

                <tr valign="top">

                <td>' . (in_array('Eliminar', $derechos) ? '<a href="javascript:eliminar_registro(\'del_pregunta\',\'' . $datos['idpregunta'] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>' : '') .

                    (in_array('Modificar', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Pregunta\',\'' . $datos['idpregunta'] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') . '

                <td>' . $datos['idpregunta'] . '</td>

                <td>' . htmlspecialchars($datos['pregunta']) . '</td>

                <td>-' . htmlspecialchars($datos['idcategoria']) . '- ' . htmlspecialchars($datos['descripcion']) . '</td>

                <td>' . str_replace("||", "<br />", htmlspecialchars(substr($datos['etiq'], 2))) . '</td>

                </tr>';

        }



        $res = @mysql_query('select preguntas.idpregunta from preguntas,categorias where categorias.idcategoria=preguntas.idcategoria' . ($preg != '' ? ' and  preguntas.pregunta  like "' . mysql_real_escape_string($preg) . '%"' : ''));

        $total = @mysql_num_rows($res);

        $total = ceil($total / $recxpag);

        $lista = '';

        for ($i = 0; $i < $total; $i++) {

            if ($pag == $i) {

                $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

            } else {

                $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $preg . '|' . $order . '\',\'pag_preguntas\')">&nbsp;' . $i . '&nbsp;</a>';

            }

        }



        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

        $restab = $datosContador['contador'] . ' registros' . $restab;

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function mostrar_etiquetas($idpreg) {

    
    //echo "----------".$idpreg;
    $res = @mysql_query('select idetiqueta,concat(ididentificador,":",valoretiqueta) valor from etiqueta where idpregunta="' . mysql_real_escape_string($idpreg) . '"  order by ididentificador');
    
    $opcion = '';



    while ($datos = mysql_fetch_assoc($res)) {

        $opcion.='<option value="' . $datos['idetiqueta'] . '">' . $datos['idetiqueta'] . ' : ' . htmlspecialchars($datos['valor']) . '</option>';
        
    }

    $r = '<table border="0" cellspacing="10" name="tetiquetas" id="tetiquetas">

        <tr>

        <td>Etiqueta: </td>

        <td><input type="text" name="idetiqueta" id="idetiqueta" size="6" maxlength="5">&nbsp;:&nbsp;  <input name="add_et" id="add_et" size="30" maxlength="100" type="text" value="" >

        &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="add" id="add" value="Agregar"   OnClick="add_upd_etiqueta(\'\')"; title="Agregar "Etiqueta" />

        <input type="hidden" value="" name="idlabel" id="idlabel"/>

        </td>

        </tr>

        <tr>

        <td>&nbsp;</td>

        <td valign="top"><select style="width:240px; height:80px" name="etiquetas" id="etiquetas"  multiple >' . $opcion . '</select>&nbsp;&nbsp;

        <input type="button" name="drop_eti" value="Eliminar" onclick="drop_upd_label(\'\')" title="Eliminar Etiqueta">

        <input type="button" name="upd" id="upd" value="Modificar" onclick="upd_eti(\'\')" title="Actualizar Etiqueta">



        </td>

        </tr>

		</table>';

    return $r;

}



function add_pregXcenso($idcenso) {



    $res = @mysql_query('select idpregunta,pregunta from preguntas where idpregunta not in (select idpregunta from censoxpregunta where idcenso="' . mysql_real_escape_string($idcenso) . '")  order by pregunta');

    $opcion = '';



    while ($datos = mysql_fetch_assoc($res)) {

        $opcion.='<option value="' . $datos['idpregunta'] . '">' . htmlspecialchars($datos['pregunta']) . '</option>';

    }

    return $opcion;

}



function puntoxcenso($derechos, $idcensos, $razon = '', $latitud = '', $longitud = '', $usuario = '', $pag = "1", $_origen = "", $UID = "") {

    if (in_array('Consultar', $derechos)) {

        $restriccion_acceso = tipo_usuario();

        if ($restriccion_acceso != '') {

            $resultado = @mysql_query('SELECT idcenso FROM clientexcenso where idcenso="' . mysql_real_escape_string(trim($_GET['idcenso'])) . '" and idcliente="' . mysql_real_escape_string($restriccion_acceso) . '"');

            if (mysql_affected_rows() <= 0) {

                header('location:denegado.php');

            }

        }



        $restab = '<form  action="#" method="post" name="Fpuntosxcenso" >

            <table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="190"><input type="checkbox"  id="all_check" value="all" onclick="check_all()" /><input type="checkbox"  name="chck[]" id="chck[]" value=""  style="display:none" />&nbsp;&nbsp;&nbsp;Opciones</td>

            <td width="230">Razón Social</td>

            <td  width="140">Latitud </td>

            <td   width="140">Longitud</td>

            <td  >Usuario Asignado</td>





  		    </tr>';



        $recxpag = 50;

        $sentencia = 'SELECT puntos.idpunto,puntos.razonSocial,puntos.latitud,puntos.longitud,censoxpunto.usuario FROM puntos,censoxpunto where  puntos.idpunto=censoxpunto.idpunto and censoxpunto.idcenso="' . mysql_real_escape_string($idcensos) . '" ' .

                (trim($razon) != '' ? ' and ((puntos.razonSocial like "%' . mysql_real_escape_string($razon) . '%") or (puntos.nombre like "%' . mysql_real_escape_string($razon) . '%"))' : '') .

                (trim($latitud) != '' ? '  and puntos.latitud like "%' . mysql_real_escape_string($latitud) . '%"' : '') . (trim($longitud) != '' ? '  and puntos.longitud like "%' . mysql_real_escape_string($longitud) . '%"' : '') . (trim($usuario) != '' ? '  and censoxpunto.usuario like "%' . mysql_real_escape_string($usuario) . '%"' : '') .

                (trim($_origen) == '' ? '' : ' and puntos.origen like"%' . mysql_real_escape_string(trim($_origen)) . '%"') . (trim($UID) == '' ? '' : ' and puntos.UID like"' . mysql_real_escape_string(trim($UID)) . '%"') .

                ' and  puntos.puntopadre is null ' . ' limit ' . (($pag - 1) * $recxpag) . ',' . $recxpag;

        //die ($sentencia);

        $resultado = @mysql_query($sentencia);

        $sentencia2 = 'Select count(puntos.idpunto) as contador FROM puntos,censoxpunto where  puntos.idpunto=censoxpunto.idpunto and censoxpunto.idcenso="' . mysql_real_escape_string($idcensos) . '" ' . (trim($razon) != '' ? ' and puntos.razonSocial like "%' . mysql_real_escape_string($razon) . '%"' : '') . (trim($latitud) != '' ? '  and puntos.latitud like "%' . mysql_real_escape_string($latitud) . '%"' : '') . (trim($longitud) != '' ? '  and puntos.longitud like "%' . mysql_real_escape_string($longitud) . '%"' : '') . (trim($usuario) != '' ? '  and censoxpunto.usuario like "%' . mysql_real_escape_string($usuario) . '%"' : '') . ' and  puntos.puntopadre is null ';

        $resultadoContador = @mysql_query($sentencia2);

        //die ($sentencia2);

        $datosContador = @mysql_fetch_assoc($resultadoContador);





        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='<tr>

                <td><input type="checkbox"  name="chck[]" id="chck[]" value="' . $datos['idpunto'] . '"  />' .

                    (in_array('Domicilios', $derechos) ? '<a href="domicilios.php?id=' . $datos['idpunto'] . '&origen=4&idcenso=' . $idcensos . '" title="Ver Domicilios"><img src="' . DIR_TEMA_ACTIVO . '_img/dom.png" width="16" height="16" alt="Domicilio" /></a>' : '') .

                    (in_array('Archivos', $derechos) ? '<a href="archivos.php?id=' . $datos['idpunto'] . '&origen=4&idcenso=' . $idcensos . '" title="Documentos"><img src="' . DIR_TEMA_ACTIVO . '_img/documentos.png" width="25" height="18" alt="Documentos" /></a>' : '') .

                    (in_array('Imagen', $derechos) ? '<a href="imagenes.php?id=' . $datos['idpunto'] . '&origen=4&idcenso=' . $idcensos . '" title="Imagen"><img src="' . DIR_TEMA_ACTIVO . '_img/picture.png" width="18" height="18" alt="Imagen" /></a>' : '') .

                    (in_array('Telefonos', $derechos) ? '<a href="telefonos.php?id=' . $datos['idpunto'] . '&origen=3&idcenso=' . $idcensos . '" title="Teléfono"><img src="' . DIR_TEMA_ACTIVO . '_img/phone.png" width="21" height="18" alt="Telfono" /></a>' : '') .

                    (in_array('Personas', $derechos) ? '<a href="personas.php?id=' . $datos['idpunto'] . '&origen=6&idcenso=' . $idcensos . '" title="Contactos"><img src="' . DIR_TEMA_ACTIVO . '_img/contactos.png" width="16" height="18" alt="Contactos" /></a>' : '') .

                    (in_array('Respuestas', $derechos) ? '<a href="responder.php?punto=' . $datos['idpunto'] . '&censo=' . $idcensos . '" title="Respuesta"><img src="' . DIR_TEMA_ACTIVO . '_img/respuesta.png" width="16" height="16" alt="respuesta" /></a>' : '') .

                    '<a href="http://maps.google.com.mx/?q=' . $datos['latitud'] . ',' . $datos['longitud'] . '" title="Ver punto en mapa" target="_blank"><img src="' . DIR_TEMA_ACTIVO . '_img/posicion_mapa.png" width="23" height="16" alt="ver punto en mapa" /></a>

                </td>

                <td>' . htmlspecialchars($datos['razonSocial']) . '</td>

                <td>' . htmlspecialchars($datos['latitud']) . '</td>

                <td>' . htmlspecialchars($datos['longitud']) . '</td>

                <td>' . htmlspecialchars($datos['usuario']) . '</td>

                </tr>';

        }

        $total = ceil($datosContador['contador'] / $recxpag);

        $lista = '';



        if ($total <= 11) {

            for ($i = 1; $i <= $total; $i++) {

                if ($pag == ($i)) {

                    $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

                } else {

                    $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">&nbsp;' . $i . '&nbsp;</a>';

                }

            }

        } elseif ($pag < 6) {

            for ($i = 1; $i <= 11; $i++) {

                if ($pag == ($i)) {

                    $lista .= '<span>&nbsp;[' . $i . ']&nbsp;</span>';

                } else {

                    $lista .= '<a  href="#Resultados" onclick="paginar(\'' . $i . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">&nbsp;' . $i . '&nbsp;</a>';

                }

            }

        } elseif (($pag + 5) <= $total) {

            $lista = '<a  href="#Resultados" onclick="paginar(\'' . ($pag - 5) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">&nbsp;' . ($pag - 5) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 4) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag - 4) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 3) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag - 3) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 2) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag - 2) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag - 1) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag - 1) .

                    '&nbsp;</a> <span>&nbsp;[' . $pag . ']&nbsp;</span><a  href="#Resultados" onclick="paginar(\'' . ($pag + 1) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag + 1) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 2) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag + 2) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 3) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag + 3) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 4) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag + 4) .

                    '&nbsp;</a><a  href="#Resultados" onclick="paginar(\'' . ($pag + 5) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $ordenar . '\',\'puntosXcenso\')">&nbsp;' . ($pag + 5) . '&nbsp;</a> ';

        } else {

            $hasta = $total - $pag;



            for ($i = 1; $i <= $hasta; $i++) {

                $lista.= '<a  href="#Resultados" onclick="paginar(\'' . ($pag + $i) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">&nbsp;' . ($pag + $i) . '&nbsp;</a>';

            }



            $lista = '<span>&nbsp;[' . $pag . ']&nbsp;</span>' . $lista;



            $hasta = 10 - $hasta;

            for ($i = 2; $i < $hasta; $i++) {

                $lista = '<a  href="#Resultados" onclick="paginar(\'' . ($pag - $i) . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">&nbsp;' . ($pag - $i) . '&nbsp;</a>' . $lista;

            }

        }



        if ($pag > 1) {

            $lista = '<a  href="#Resultados" onclick="paginar(\'1\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">Primera&nbsp;&nbsp;</a>' . $lista;

        }

        if ($pag < $total) {

            $lista.='<a  href="#Resultados" onclick="paginar(\'' . $total . '\',\'' . $idcensos . '|' . $razon . '|' . $latitud . '|' . $longitud . '|' . $usuario . '|' . $_origen . '|' . $UID . '\',\'puntosXcenso\')">&nbsp;&nbsp;&Uacute;ltima</a>';

        }



        $restab.='</table><input type="hidden" id="borrando" name="borrando" value="1"><input type="button" id="borrar" name="borrar" value="Eliminar Seleccionados" onclick="document.forms[\'Fpuntosxcenso\'].submit();"></form><br/><br/><div id="res_enc" align="center">' . ($lista == '' ? '<span>No se encontraron registros</span>' : $lista) . '</div>';

        $restab = $datosContador['contador'] . ' registros' . $restab;

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function censos_punto() {

    for ($x = 13652; $x <= 14651; $x++) {

        $tmp.='insert into censoxpunto (idcenso,idpunto) values(26,' . $x . ');<br>';

    }

    return $tmp;

}



function zonas_admin() {

    $res = '';

    if (sesiones(true)) {

        $resultado = @mysql_query('SELECT grupoxderechos.nombre  FROM usuarioxgrupo,grupoxderechos where grupoxderechos.idgrupo=usuarioxgrupo.idgrupo and usuarioxgrupo.idusuario="' . mysql_real_escape_string($_SESSION['user']) . '" and grupoxderechos.idpagina="33" and grupoxderechos.nombre="Poligonos"');

        if (@mysql_num_rows($resultado) == 0) {

            $res = array('', '<div style="width:280px; height:30px; margin-top:5px; margin-bottom:15px;" ></div><div  id="crear_zonas">	<br /><br /><div style="text-align:center;"><b>No tiene acceso a este modulo.</b> </div></div>');

        } else {





            $res = @mysql_query('select zonas.idzonas,zonas.nombre znombre,poligonozonas.nombre,poligonozonas.id from zonas,poligonozonas where poligonozonas.idzonas=zonas.idzonas group by zonas.nombre,poligonozonas.id');



            $id = '';

            $tmp = '';

            while ($datos = @mysql_fetch_assoc($res)) {

                if ($id != $datos['idzonas']) {

                    $id = $datos['idzonas'];

                    $tmp.=($tmp != '' ? ']},' : '') . '{title: "' . htmlspecialchars($datos['znombre']) . '",children: [ {title: "' . htmlspecialchars($datos['nombre']) . '" ,key: "' . $datos['idzonas'] . '|' . $datos['id'] . '"}';

                } else {

                    $tmp.=', {title: "' . htmlspecialchars($datos['nombre']) . '" ,key: "' . $datos['idzonas'] . '|' . $datos['id'] . '"}';

                }

            }





            if ($tmp != '') {

                $tmp = '

                    treeData2 = [' . $tmp . ']}];

                    $("#crear_zonas").dynatree({

                    checkbox: true,

                    selectMode: 3,

                    children: treeData2,



                    onSelect: function(select, node) {

                        var selKeys = $.map (node.tree.getSelectedNodes(), function(node){

                            r=node.data.key;

                            if (r.indexOf(\'_\') > -1)

                                {r=null;}

                            return r;

                            });

                        $(\'#seleccion_zonas\').text(selKeys+\'\');

                    },



                    onDblClick: function(node, event) {

                        node.toggleSelect();

                    },

                    onKeydown: function(node, event) {

                        if( event.which == 32 ) {

                        node.toggleSelect();

                        return false;

                        }

                    },



                    cookieId: "dynatree-zonas",

					idPrefix: "dynatree-zonas-"

					});';

            }

            $res = array($tmp,

                '
                <div style="width:288px; padding-left:3px" class="miscompras" id="adminzonas" >

                        <table border="0" height="100%">

                            <tr id="trEncabezadoAdminZona"  height="1px"><td valign="top">

                                <div style="margin-left:20px;margin-right:20px">

                                    <font color="green">La lista prestenta todas las zonas activas. La definicin de una zona puede contener varios polgonos (subzonas). Cundo el usuario seleccione una zona, todos los polgonos comprendidos sern encendidos!.</font>

                                </div>

                                <div align="right">

                                    <img src="' . DIR_TEMA_ACTIVO . '_img/tip.png" width="20" height="20"/>

                                </div>

                                <div style="margin-left:20px;margin-right:20px">

                                    <font color="green">Hacer doble click sobre el mapa te ayudar a acercarlo y enfocarte mejor en una zona!.<br><img src="' . DIR_TEMA_ACTIVO . '_img/'.($dominio=='censos'? '04_maps.png"' : '04_mapsv2.png"')  .'height="20"/> Navega a la localidad deseada. Puedes hacerlo <a href="#" onclick="buscar()">aqu&iacute;</a></font>

                                </div>

                               <table width="100%" border="0">

                                   <tr>

                                       <td><input type="image" id="marca_zona2" name="marca_zona2" src="' . DIR_TEMA_ACTIVO . '_img/marcar.png" onclick="ActivaPoligono(\'2\')" /><input type="image" id="borrar_zona2" name="borrar_zona2" src="' . DIR_TEMA_ACTIVO . '_img/desmarcar.png" onclick="ActivaPoligono(\'2\')" style="display:none;" /></td>

                                       <td><input type="image" id="btguardar_zona" name="btguardar_zona" src="' . DIR_TEMA_ACTIVO . '_img/guardar.png" onclick="guardar_zona()" /></td>

                                       <td><input type="image" id="btguardar_zona" name="btguardar_zona" src="' . DIR_TEMA_ACTIVO . '_img/eliminar.png" onclick="elimnar_adm_zona()" /></td>

                                   </tr>

                               </table>

                            </td></tr>

                            <tr valign="top" id="trTreeAdminZona">

                                <td>

                                    <table border="0" height="100%">

                                        <tr valig="top">

                                            <td width="10px"></td>

                                            <td valign="top" height=99% width="275px" style="border:solid 1px #CCC; background-color: #EEEEEE;">

                                                <div  id="crear_zonas"></div>

                                            </td>

                                            <td width="0px"></td>

                                        </tr>

                                    </table>

                                </td>

                            </tr>

                            <tr id="trPieAdminZona" height="1px"><td>

                                <br/>

                                <div align="right">

                                    <img src="' . DIR_TEMA_ACTIVO . '_img/notas.gif" width="20" height="20"/>

                                </div>

                                <div style="margin-left:20px;margin-right:20px">

                                    <font color="green">Al nombrar una zona nueva, incluye el nombre de la regin y/o ciudad para poderla identificar fcilmente!.<br/><br/></font>

                                </div>

                            </td></tr>

                        </table>

                    </div>'



                    /*

                      '<div style="width:280px; padding-left:10px; height:30px; margin-top:5px; margin-bottom:15px;" align="center" >

                      <table width="100%" border="0">

                      <tr>

                      <td><input type="image" id="marca_zona2" name="marca_zona2" src="' . DIR_TEMA_ACTIVO . '_img/marcar.png" onclick="ActivaPoligono(\'2\')" /><input type="image" id="borrar_zona2" name="borrar_zona2" src="' . DIR_TEMA_ACTIVO . '_img/desmarcar.png" onclick="ActivaPoligono(\'2\')" style="display:none;" /></td>

                      <td><input type="image" id="btguardar_zona" name="btguardar_zona" src="' . DIR_TEMA_ACTIVO . '_img/guardar.png" onclick="guardar_zona()" /></td>

                      <td><input type="image" id="btguardar_zona" name="btguardar_zona" src="' . DIR_TEMA_ACTIVO . '_img/eliminar.png" onclick="elimnar_adm_zona()" /></td>

                      </tr>

                      </table>

                      </div>

                      <div  id="crear_zonas">	<br /><br />

                      </div>' */

            );

        }

        return $res;

    }

}



function zonas_view() {

    $restab = '';

    $resultado = @mysql_query('SELECT idzonas,nombre FROM zonas order by nombre ');

    while ($datos = @mysql_fetch_assoc($resultado)) {

        $restab.='<input type="checkbox"  onclick="zonas(this)"  value="' . $datos['idzonas'] . '" name="z_' . $datos['idzonas'] . '"/>' . htmlspecialchars($datos['nombre']) . '<br/>';

    }

    return $restab;

}



function _derechos($user, $page) {

    $resultado = @mysql_query('SELECT grupoxderechos.nombre ' .

                    'FROM usuarioxgrupo, grupoxderechos ' .

                    'WHERE grupoxderechos.idgrupo = usuarioxgrupo.idgrupo and ' .

                    'usuarioxgrupo.idusuario = "' . mysql_real_escape_string($user) . '" and ' .

                    'grupoxderechos.idpagina = "' . mysql_real_escape_string($page) . '"');

    if (@mysql_num_rows($resultado) == 0) {

		unset ($tmp);

		$tmp[] = '';

        return $tmp;

    } else {

        unset($tmp);

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $tmp[] = $datos ['nombre'];

        }

        return $tmp;

    }

}



function derechos($user, $page) {

    $SAux = _derechos($user, $page);

    if ($SAux == '')

        header('location: denegado.php');

    else

        return $SAux;

}



function tipo_usuario($solotipo = false) {

    $res = @mysql_query('SELECT idusuario FROM usuario where idusuario="' . mysql_real_escape_string($_SESSION['user']) . '" and idusuario not in (select idusuario from usuarioxgrupo where idusuario="' . mysql_real_escape_string($_SESSION['user']) . '")');

    if (mysql_num_rows($res) > 0) {

        $tuser = 'compra';

    } else {

        $res = @mysql_query('SELECT idusuario FROM usuario where idusuario="' . mysql_real_escape_string($_SESSION['user']) . '" and idusuario  in (select idusuario from usuarioxgrupo where idusuario="' . mysql_real_escape_string($_SESSION['user']) . '") and  idusuario  in (select usuario_idusuario from clientexusuario where usuario_idusuario="' . mysql_real_escape_string($_SESSION['user']) . '")');

        if (mysql_num_rows($res) > 0) {

            $tuser = 'cliente';

        } else {

            $res = @mysql_query('SELECT idusuario FROM usuario where idusuario="' . mysql_real_escape_string($_SESSION['user']) . '" and idusuario  in (select idusuario from usuarioxgrupo where idusuario="' . mysql_real_escape_string($_SESSION['user']) . '") and  idusuario not in (select usuario_idusuario from clientexusuario where usuario_idusuario="' . mysql_real_escape_string($_SESSION['user']) . '")');

            if (mysql_num_rows($res) > 0) {

                $tuser = 'sistema';

            } else {

                $tuser = '';

            }

        }

    }

    if ($solotipo) {

        die($tuser);

    }

    switch ($tuser) {

        case('compra'): {

                header('location: denegado.php');

                break;

            }

        case('cliente'): {

                $res = @mysql_query('SELECT clientes_idcliente FROM clientexusuario where usuario_idusuario="' . mysql_real_escape_string($_SESSION['user']) . '"');

                if (@mysql_num_rows($res) == 0) {

                    header('location: nofound.php');

                } else {

                    $datos = mysql_fetch_assoc($res);

                    return $datos['clientes_idcliente'];

                }

                break;

            }

        case('sistema'): {

                return '';

                break;

            }

        default: {

                header('location: denegado.php');

                break;

            }

    }

}



function pertenece($q) {

    $res = @mysql_query($q);

    if (@mysql_num_rows($res) == 0) {

        header('location: denegado.php');

    }

}



function getAgeb( $lat, $lng ){

	$ageb = '';

		if( conectar() ){

			$milMetros = 0.008983152841199880;

//			$resultado = "latMax=$latMax, latMin=$latMin, lngMax=$lngMax, lngMin=$lngMin<br>";

			$latMax = $lat + $milMetros;

			$latMin = $lat - $milMetros;

			$lngMax = $lng + $milMetros;

			$lngMin = $lng - $milMetros;

//			$resultado .= "latMax=$latMax, latMin=$latMin, lngMax=$lngMax, lngMin=$lngMin<br>";

//			die ( $resultado );

			$consulta = 'select distinct(ap.idAgeb) ' .

						'from agebspoligonos ap ' .

						'where ( lat between ' . mysql_real_escape_string( $latMin ) . ' and ' . mysql_real_escape_string( $latMax ) . ' ) ' .

						'and ( lon between ' . mysql_real_escape_string( $lngMin ) . ' and ' . mysql_real_escape_string( $lngMax ) . ') ' .

						'and ap.idAgeb in ( select ap2.idAgeb from agebspoligonos ap2 ' .

							               'where ap.idAgeb = ap2.idAgeb and ap2.lat < ' . $lat . ' and ap2.lon < ' . $lng . ' ) ' .

						'and ap.idAgeb in ( select ap3.idAgeb from agebspoligonos ap3 ' .

										   'where ap.idAgeb = ap3.idAgeb and ap3.lat > ' . $lat . ' and ap3.lon > ' . $lng . ' )';

			$resultado = mysql_query( $consulta ) or die ( 'Error en la consulta: ' . mysql_error() . '<br>' . $consulta );

				while( $datos = mysql_fetch_assoc( $resultado ) ){

					$subConsulta = 'select lat, lon from agebspoligonos where idAgeb = "' . $datos['idAgeb'] . '" order by idAgebsPoligonos asc';

					$res = mysql_query( $subConsulta ) or die ( 'Error en la consulta: ' . mysql_error() . '<br>' . $subConsulta );

					unset( $poligono );

						while( $d = mysql_fetch_assoc( $res ) ){

							$poligono[] = array( "Longitud" => $d['lon'], "Latitud" => $d['lat'] );

						}

						if( DentroPoligono( $poligono, '', array("Longitud" => $lng, "Latitud" => $lat ) ) ){

							$ageb = $datos['idAgeb'];

							break;

						}

				}

		}

	return $ageb;

}



function agebsAPintar($latmax, $latmin, $longmax, $longmin, $polig) {

    global $pasadas;

    $resultado = '';

    if (conectar()) {

        $consulta_sql = 'select idAgeb as id, lat, lon ' .

                'from agebspoligonos ' .

                'where ( lat between ' . mysql_real_escape_string($latmin) . ' and ' . mysql_real_escape_string($latmax) . ' ) ' .

                'and ( lon between ' . mysql_real_escape_string($longmin) . ' and ' . mysql_real_escape_string($longmax) . ' )';

        $resultado = mysql_query($consulta_sql);

        if (!$resultado) {

            return "no se puede";

            die($consulta_sql);

        }

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $res[] = array("Longitud" => $datos['lon'], "Latitud" => $datos['lat'], "idAgeb" => $datos['id']);

        }

        if (count($res) > 0) {

            $poligono = explode('|', $polig);

            foreach ($poligono as &$value) {

                $tmp = explode(',', $value);

                $poli[] = array("Longitud" => $tmp[1], "Latitud" => $tmp[0]);

            }

            //$resultado = $resultado . ' desde aqui ';

            $resultado = '';

            foreach ($res as $vpunto){

            	if( strpos( $resultado, $vpunto['idAgeb'] ) === false ){

	                if ( DentroPoligono( $poli, '', array("Longitud" => $vpunto['Longitud'], "Latitud" => $vpunto['Latitud']))){

                        $resultado .= $vpunto ['idAgeb'] . '|';

	                }

            	}

            }

			// El resultado tiene que terminar con | para corregir el error de que no imprime un solo ageb

/*            if ($resultado != '') {

                $resultado = substr($resultado, 0, - 1);

            }

*/

        }else{

			// Si no se encuentra ningun Ageb a imprimir es posible que el poligono este completamente dentro de un ageb,

			// se hara la busqueda del Ageb con un punto de los limites

			$resultado = getAgeb( $latmax, $longmax );

            if( $resultado != '' ){

                $resultado .= '|';

            }			

		}

        return $resultado;

    }

    else

        return "no se pudo conectar";

}



function puntos_mapa($seleccion, $latmax, $latmin, $longmax, $longmin, $polig, $cantidad = 'false', $spoli = '') {

    if (conectar()) {

        $condicion = explode(',', $seleccion);

        $eti = '';

        foreach ($condicion as $value) {

            list ( $idpregunta, $idetiqueta ) = explode('|', $value);

            $eti .= $idetiqueta . ',';
            
            //echo "<script>console.log('Console: " . $eti . "' );</script>";

        }
        
        

        $eti = substr($eti, 0, -1);
        
        

		// se hace una consulta a la base de datos

		panelControl( (isset($_SESSION['user']) ? $_SESSION['user'] : ''), 6, 'consulta base de datos');

        $res2 = @mysql_query('select * from config');

        $config = @mysql_fetch_assoc($res2);
        

		$restringirCenso = ''; 

			if ( $config['restringirCensos'] == true ){

				$consulta = 'select idcenso from censoxusuario where idusuario = "' . $_SESSION['user'] . '" 

							 union

							 select idcenso from censos where `general` = 1';

				$result = mysql_query ( $consulta );

				$restringirCenso = '';

					while ( $censos = mysql_fetch_assoc ( $result ) ){

						$restringirCenso .= ( $restringirCenso != '' ? ',' : '' ) . $censos['idcenso'];

					}

				$restringirCenso = ' and cp.idcenso in (' . ( $restringirCenso == '' ? '-1' : $restringirCenso ) . ')';

			}

		$consulta = 'select distinct p.idpunto, p.latitud, p.longitud, p.razonsocial, p.icono, categoria_padre(preg.idcategoria) categoriaPadre ' .

                        		   'from puntos p join respuesta r on r.idpunto = p.idpunto and r.idetiqueta in (' . $eti . ') ' .

                        						'join preguntas preg on preg.idpregunta = r.idpregunta ' .

												'join censoxpunto cp on p.idpunto = cp.idpunto ' .

                        		   'where p.puntopadre is null and p.vigente = true and ( p.latitud between ' . mysql_real_escape_string($latmin) .

                        		         ' and ' . mysql_real_escape_string($latmax) . ' ) and ' .

                        				 '( p.longitud between ' . mysql_real_escape_string($longmin) . ' and ' . mysql_real_escape_string($longmax) .

                        				 ')' . $restringirCenso;

		panelControl ( 'debug', 6, $consulta );

        $resultado = @mysql_query( $consulta );

        while ($datos = @mysql_fetch_assoc($resultado)) {

            $iconoActual = $datos['icono'];

            if (($iconoActual != '') && ($iconoActual != '0')) {

                if (($iconoActual >= 1) && ($iconoActual >= 12) && ($config['icono' . $iconoActual] != ''))

                    $iconoActual = 'sitios/' . $config['icono' . $iconoActual];

                else

                    $iconoActual = DIR_TEMA_ACTIVO . '_img/' . $iconoActual . '.png';

            }

            $res[] = array("Longitud" => $datos['longitud'], "Latitud" => $datos['latitud'],

                "Razonsocial" => $datos['razonsocial'], "idpunto" => $datos['idpunto'],

                "icono" => $iconoActual, "catPad" => $datos['categoriaPadre']);

        }

        if (count($res) > 0) {

            $poligono = explode('|', $polig);

            foreach ($poligono as &$value) {

                $tmp = explode(',', $value);

                $poli[] = array("Longitud" => $tmp[1], "Latitud" => $tmp[0]);

            }

            if ($spoli != '') {

                $spoligono = explode('|', $spoli);

                foreach ($spoligono as &$value) {

                    $tmp = explode(',', $value);

                    $spolig[] = array("Longitud" => $tmp[1], "Latitud" => $tmp[0]);

                }

            } else {

                $spolig = '';

            }

            $resultado = '';

            $pdentro = '';

            foreach ($res as $vpunto) {

                if (DentroPoligono($poli, $spolig, array("Longitud" => $vpunto['Longitud'], "Latitud" => $vpunto['Latitud']))) {

                    $pdentro .= $vpunto['idpunto'] . ',';

                    $resultado .= $vpunto['Longitud'] . ',' . $vpunto['Latitud'] . ',' . $vpunto['Razonsocial'] . ',' . $vpunto['idpunto'] . ',' . $vpunto['icono'] . ',' . $vpunto['catPad'] . '|';

                }

            }

            if ($resultado != '') {

                $resultado = substr($resultado, 0, -1);

            }

            if ($cantidad == 'true') {

                if (empty($resultado)) {

                    $resultado = 0;

                } else {

                    $rest = @mysql_query('select sum( p.costo) costo ' .

                                    'from respuesta r,preguntas p ' .

                                    'where r.idpunto in (' . substr($pdentro, 0, -1) . ') and r.idetiqueta in(' . $eti . ') and ' .

                                    'p.idpregunta = r.idpregunta');

                    $datos = @mysql_fetch_assoc($rest);

                    $resultado = $datos['costo'];

                }

            }

        } else {

            if ($cantidad == 'true') {

                $resultado = '0';

            } else {

                $resultado = '';

            }

        }

        //	@mysql_query('drop table  '.$name);

        return $resultado;

    }

}



function puntos_mapa_total($seleccion, $latmax, $latmin, $longmax, $longmin, $polig, $cantidad = 'false', $spoli = '') {

    $resultado = puntos_mapa($seleccion, $latmax, $latmin, $longmax, $longmin, $polig, $cantidad, $spoli);

    if ($resultado != '') {

        $puntos = explode('|', $resultado);



        for ($x = 0; $x < count($puntos); $x++) {

            $punto = explode(',', $puntos[$x]);

            $contador[$punto[5]] = $contador[$punto[5]] + 1;

        }



        $SAux = '';

        foreach ($contador as $k => $v) {

            $SAux = $SAux . ($SAux != '' ? '|' : '') . $v . '  ' . $k;

        }



        //$resultado = count($puntos)."";

        $resultado = $SAux;

    }

    return $resultado;

}



function DentroPoligono($poligono, $sup_poligono, $punto) {

    /*   $poligono es un array definido de la siguiente manera

      $poligono = array ( "0" => array ( "Longitud" => 20.3654738, "Latitud" => -103.2878974 ),

                          "1" => array ( "Longitud" => 20.3847564, "Latitud" => -103.8273467 )

      ...

      );

      $punto es un array definido de la siguiente manera

      $punto = array ( "Longitud" => 20.384756, "Latitud" => -103.293847 );

     */



    $NoPaths = count($poligono);

    $j = $NoPaths - 1;

    $dentroPoligono = false;

    for ($i = 0; $i < $NoPaths; $i++) {

        $vertice1 = $poligono[$i];

        $vertice2 = $poligono[$j];

        if ($vertice1["Longitud"] < $punto["Longitud"] && $vertice2["Longitud"] >= $punto["Longitud"] || $vertice2["Longitud"] < $punto["Longitud"] && $vertice1["Longitud"] >= $punto["Longitud"]) {

            if ($vertice1["Latitud"] + ( $punto["Longitud"] - $vertice1["Longitud"] ) / ( $vertice2["Longitud"] - $vertice1["Longitud"] ) * ( $vertice2["Latitud"] - $vertice1["Latitud"] ) < $punto["Latitud"]) {

                $dentroPoligono = !$dentroPoligono;

            }

        }

        $j = $i;

    }

    if (( $sup_poligono != '' ) && ( $dentroPoligono )) {

        $NoPaths = count($sup_poligono);

        $j = $NoPaths - 1;

        $dentroPoligono = false;

        for ($i = 0; $i < $NoPaths; $i++) {

            $vertice1 = $sup_poligono[$i];

            $vertice2 = $sup_poligono[$j];

            if ($vertice1["Longitud"] < $punto["Longitud"] && $vertice2["Longitud"] >= $punto["Longitud"] || $vertice2["Longitud"] < $punto["Longitud"] && $vertice1["Longitud"] >= $punto["Longitud"]) {

                if ($vertice1["Latitud"] + ( $punto["Longitud"] - $vertice1["Longitud"] ) / ( $vertice2["Longitud"] - $vertice1["Longitud"] ) * ( $vertice2["Latitud"] - $vertice1["Latitud"] ) < $punto["Latitud"]) {

                    $dentroPoligono = !$dentroPoligono;

                }

            }

            $j = $i;

        }

    }

    return $dentroPoligono;

}



function AreaPoligono($poligono) {

    $NoPaths = count($poligono);

    $suma = 0;

    if ($NoPaths >= 3) {

        //producto en cruz desde 0 hasta n-1

        for ($i = 0; $i < $NoPaths - 1; $i++) {

            $verticei = $poligono[$i];

            $verticeimas1 = $poligono[$i + 1];

            $suma += $verticei["Longitud"] * $verticeimas1["Latitud"] -

                    $verticei["Latitud"] * $verticeimas1["Longitud"];

        }

        //ahora el ltimo con el primero

        $verticei = $poligono[$NoPaths - 1];

        $verticeimas1 = $poligono[0];

        $suma += $verticei["Longitud"] * $verticeimas1["Latitud"] -

                $verticei["Latitud"] * $verticeimas1["Longitud"];

        if ($suma < 0)

            $suma = $suma * -1;

    }

    $suma = $suma / 2;

    return $suma;

}



function areaAgebs($listaAgebs) {

    if ($listaAgebs != '') {

        $lista = explode('|', $listaAgebs);

        $area = 0;

        foreach ($lista as &$ageb) {

            $sqlArea = mysql_query('SELECT idAgebsPoligonos, idAgeb, lat, lon ' .

                    'FROM agebspoligonos ' .

                    'WHERE idAgeb = "' . $ageb . '" ' .

                    'ORDER BY idAgebsPoligonos asc');

            $resArea = null;

            while ($datosPoligonoArea = mysql_fetch_assoc($sqlArea)) {

                $resArea[] = array("Longitud" => $datosPoligonoArea['lon'], "Latitud" => $datosPoligonoArea['lat']);

            }

            $area += AreaPoligono($resArea);

        }

        return $area;

    }

}



function AreaPoligonoKm($poligono) {

    $area = AreaPoligono($poligono);

    $res = pow(109.5187 * (pow($area, 0.5)), 2);

    return $res;

}



function areaAgebsKm($listaAgebs) {

    $area = areaAgebs($listaAgebs);

    $res = pow(109.5187 * (pow($area, 0.5)), 2);

    return $res;

}



function puedeCompartirCompras(){

	$consulta = 'select compartirCompras from config';

	$resultado = mysql_query ( $consulta );

	$datos = mysql_fetch_assoc ( $resultado );

	return ( $datos['compartirCompras'] == 1 ? true : false );

}



function mis_compras($id) {

	$dominio = $_SERVER["HTTP_HOST"];

	$dominio = substr( $dominio, 0, strpos( $dominio, '.' ) );

    global $valores;

    if (esAdministrador()) {

        if (isset($_POST['otroUsuario']))

            $elUsuario = $_POST['otroUsuario'];

        if ($elUsuario == '')

            $elUsuario = $_SESSION['user'];

    }

    else {

        $elUsuario = $_SESSION['user'];

    }    

    $resultado = @mysql_query('SELECT DATE_FORMAT( fecha, "%d/%m/%Y %h:%i:%s %p" ) fec, idcompra, nombre ' .

                    'FROM compra where idusuario = "' . $elUsuario . '" order by fecha desc ');

    

    //Consultar a otro usuario

    $resAdmin = '';

	$usuariosEnSelect = '';

    if (esAdministrador()) {

        $resultadoUsuarios = @mysql_query('SELECT * From usuario');

        $resAdmin .= '<form action="index.php" method="post" id="formaComprasAdmin">';

        $resAdmin .= '<input id="MCompras" name="MCompras" type=hidden>';

        $resAdmin .= 'Selecciona otro usuario: ';

        //$resAdmin .= '<select name="otroUsuario" id="otroUsuario" onChange="document.getElementById(\'imagenResultados\').src = \'https://chart.googleapis.com/chart?chst=d_bubble_icon_texts_big&chld=location|bbT|FFB573|000000|Espere...|Consultando censosMKD.com... espere!\';
        
        $resAdmin .= '<select name="otroUsuario" id="otroUsuario" onChange="document.getElementById(\'imagenResultados\').src = \'https://fa1.censosmkd.com/temas/default/_img/Nada.png\';
        
                                                                            $(\'#resultadosGratis\').fadeIn(200);setTimeout(\'enviaFormaAdmin()\',200);>';

        while ($datos = @mysql_fetch_assoc($resultadoUsuarios)) {

            $resAdmin .= '<option value="' . $datos['idusuario'] . '" ' . ($elUsuario == $datos['idusuario'] ? 'selected' : '') . '>' . $datos['idusuario'] . '</option>';

			$usuariosEnSelect .= ( $elUsuario != $datos['idusuario'] ? '<option value="' . $datos['idusuario'] . '" >' . $datos['idusuario'] . '</option>' : '' );

        }

        $resAdmin .= '</select>';

        $resAdmin .= '</form>';

    }

    

    if (mysql_num_rows($resultado) == 0) {

        $res = array('', '<div style="width:288px;" id="Lmiscompras" class="miscompras" align="center"><br/><br/><br/>' .

            '<b>No ha realizado ninguna compra</b>'.$resAdmin.'</div>');

    } else {

        $compras = '<option value="-1" ' . ( $datos['idcompra'] == '-1' ? 'selected' : '' ) . '>N I N G U N A</option>';

        if (($datos['idcompra'] == '-1') || ($id == '')) {

            $id = '-1';

        }



        while ($datos = @mysql_fetch_assoc($resultado)) {

            if ($id == '') {

                $id = $datos['idcompra'];

            }



            $sqlArea = @mysql_query('SELECT * FROM poligonocompra where compra="' . $datos['idcompra'] . '"');

            $resArea = null;

            while ($datosPoligonoArea = @mysql_fetch_assoc($sqlArea)) {

                $resArea[] = array("Longitud" => $datosPoligonoArea['longitud'], "Latitud" => $datosPoligonoArea['latitud']);

            }



            $compras .= '<option value="' . $datos ['idcompra'] . '" ' . ( $id == $datos['idcompra'] ? 'selected' : '' ) . '>' .

                    $datos ['nombre'] . '-' . $datos ['fec'] . ' el área del polígono=' . AreaPoligonoKm($resArea) . '</option>';

        }

		$paginaExcel=tipoRadiog();

		$archivoexcel=validaexl('document.seleccionExcel');	
		
		/*if ($_SESSION['user'] == 'Admin' or $_SESSION['user'] == 'rafael' or $_SESSION['user'] == 'reporte1' or $_SESSION['user'] == 'reporte2')
        {*/
            $reportes='
                <tr>
					<td>
						<div align="left">
						    <br>
							<a style="text-decoration: none; padding: 5px; font-weight: 300; font-size: 15px; color: black; background-color: #ffffff; border-radius: 6px; border: 2px solid rgba(230,0,0, 0.7);" href="https://fa1.censosmkd.com/reportesFa.php">Reportes</a>
							</div>
							<br>
					</td>
			    </tr>
            ';
            
        /*}else{
            $reportes='';
        }*/
if($dominio=='censosmkd'){
 
}
    
       if (($dominio=='franquicias')||($dominio=='fa1') ||($dominio=='faprueba')){
    
        
    

        $res = array(($id != '-1' ? compras_opciones($id) : ''),
        

            '<!-- Mis consultas -->
            <div style="background-color:#FFF; width: 100%; height: height: 375px; overflow-y: scroll;" class="boxContenedor_consultas2">
             <div style="width:288px;aling-left:20px" id="Lmiscompras" class="miscompras">
            <div class="boxContenedor_consultas">
                                <div style="margin-left:20px;margin-right:20px">
                                <table width="100%" border="0">
                                <div id="usuarioR" style="display:none";>'.$_SESSION ['user'] .'</div>
                                <div id="idcompraR" style="display:none";>'.$id.'</div>
                                <h3>Bricks</h3>
								<tr>
									<td>
										<div align="left">
                                			<input  type="checkbox" name="btn4" id="btn40" onclick="printaAguascalientes2();" /> Aguascalientes<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn5" id="btn50" onclick="printaBajaCal2();" /> Baja California<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn6" id="btn60" onclick="printaCampeche2();" /> Campeche<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn202" id="btn202" onclick="printagebs2();" /> CDMX <br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn7" id="btn70" onclick="printaChiapas2();" /> Chiapas<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn8" id="btn80" onclick="printaChihuahua2();" /> Chihuahua<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn9" id="btn90" onclick="printaCoahuila2();" /> Coahuila<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn10" id="btn100" onclick="printaColima2();" /> Colima<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn11" id="btn110" onclick="printaDrurango2();" /> Durango<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn3" id="btn3300" onclick="printaEdoMex2();" /> Estado de México<br></div></td>
                                </tr>
                                <tr>
                                	<td>
                                		<div align="left">
                                		<input  type="checkbox" name="btn12" id="btn120" onclick="printaGuanajuato2();" /> Guanajuato<br></div></td>
                                </tr>
                                    <tr>
                                        <td>
                                            <div align="left">
                                            <input  type="checkbox" name="btn13" id="btn130" onclick="printaGuerrero2();" /> Guerrero<br>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        	<div align="left">
                                            <input  type="checkbox" name="btn15" id="btn150" onclick="printaHidalgo2();" /> Hidalgo<br>
                                            </div>
                                        </td>
                                    <tr>
                                        <td>
                                        	<div align="left">
                                            <input  type="checkbox" name="btn16" id="btn160" onclick="printaJalisco2();" /> Jalisco<br>
                                        	</diV>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <div align="left">
                                            <input  type="checkbox" name="btn17" id="btn170" onclick="printaMichoacan2();" /> Michoacan<br>
                                            </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                            <div align="left">
                                            <input  type="checkbox" name="btn18" id="btn180" onclick="printaMorelos2();" /> Morelos<br>
                                            </div>
                                        </td>
                                    <tr>
                                        <td>
                                        	<div align="left">
                                            <input  type="checkbox" name="btn19" id="btn190" onclick="printaNayarit2();" /> Nayarit<br>
                                        	</div>
                                        </td>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn140" id="btn140" class="btn14" onclick="printaNuevoLeon2();" /> Nuevo León<br>
                                        </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn20" id="btn200" onclick="printaOaxaca2();" /> Oaxaca<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn21" id="btn210" onclick="printaPuebla2();" /> Puebla<br>
                                        </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn22" id="btn220" onclick="printaQueretaro2();" /> Queretaro<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn23" id="btn230" onclick="printaQuintanaRoo2();" /> Quintana Roo<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn24" id="btn240" onclick="printaSanLuis2();" /> San Luis Potosi<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn25" id="btn250" onclick="printaSinaloa2();" /> Sinaloa<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn26" id="btn260" onclick="printaSonora2();" /> Sonora<br>
                                        </div>
                                        </td>
                                        <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn27" id="btn270" onclick="printaTabasco2();" /> Tabasco<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn28" id="btn280" onclick="printaTamaulipas2();" /> Tamaulipas<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn29" id="btn290" onclick="printaTlaxcala2();" /> Tlaxcala<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn30" id="btn300" onclick="printaVeracruz2();" /> Veracruz<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn31" id="btn310" onclick="printaYucatan2();" /> Yucatan<br>
                                        </div>
                                        </td>
                                        </tr>
                                    <tr>
                                        <td>
                                        <div align="left">
                                            <input  type="checkbox" name="btn32" id="btn320" onclick="printaZacatecas2();" /> Zacatecas<br>
                                        </div>
                                        </td>
                                        </tr>
                                    
                                </table>
                                </div>
                                
                                </div>

                          <table border="0" height="100%">
                            <tr height="1px" id="trEncabezado">
                              <td>

                                <div style="margin-left:20px;margin-right:20px">
                                  <table width="100%" border="0" >
									<tr>
										<td>
											<div align="left">
												<input type="checkbox" name="agruparResultados" 
												id="agruparResultados" checked 
												onclick="borra_puntos_compra(); 
												activar_compra(idCompra);"/>
												Agrupar
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div align="left">
										
														<input type="checkbox" 
														name="mostrarAgebsCompras" 
														id="mostrarAgebsCompras" 
														onclick="document.getElementById( \'mostrarAgebsC\' ).checked = document.getElementById ( \'mostrarAgebsCompras\' ).checked;
														muestraAgebsPolilinea(pcompras); 
														resize();"/>
														Población
												</div>
													</td>'.$reporte.'<td>
														<img style="display:none" 
														id="mostrarAgebsImgCompra" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" />
													</td>
													<td>
													<div align="left" style="display:none" name="divMostrar
															Compra" id="divMostrarEtiquetasCompra">
												
															<input type="checkbox" style="display:none" 
															name="mostrarEtiquetasCompra" id="mostrarEtiquetasCompra" 
															onclick="muestraAgebsPolilinea(pcompras);" />
														
															<a href="#"  onclick="ayuda(\'9\');">Códigos</a>
													
													</div>
													
													
													</td>
												
												
												</tr>
												<tr>
													<td>
														<div align="left">
										
														<input type="checkbox" 
														name="mostrarAgebsComprasNSE" 
														id="mostrarAgebsComprasNSE" 
														onclick="document.getElementById( \'mostrarAgebsCNSE\' ).checked = document.getElementById ( \'mostrarAgebsComprasNSE\' ).checked;
														muestraAgebsPolilineaNSE(pcompras); 
														resize();"/>
														NSE
												</div>										
													</td>
													<td>
														<img style="display:none" 
														id="mostrarAgebsImgCompraNSE" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" />
													</td>
													<td>
													<div align="left" style="display:none" name="divMostrar
															CompraNSE" id="divMostrarEtiquetasCompraNSE">
												
															<input type="checkbox" style="display:none" 
															name="mostrarEtiquetasCompraNSE" id="mostrarEtiquetasCompraNSE" 
															onclick="muestraAgebsPolilineaNSE(pcompras);" />
														
															<a href="#"  onclick="ayuda(\'11\');">Códigos NSE</a>
													
													</div>
													
													
													</td>
												</tr>
												
												'.$reportes.'
								
	                                  </td>
	                                 
	                              </tr>
                                  </table>
                                  
                                 
                                  
                                  

                                <b><font color="blue">Selecciona una ' . (esCorporativo() ? 'consulta' : 'compra') . ' para verla en el mapa!</font><b>

                                </div>

                                <div style="margin-left:10px;margin-right:10px">'.

                                    (esAdministrador() ? $resAdmin : '' ).

                                    '<form action="index.php" method="post" id="formaCompras" onsubmit="return compras_actualizar()">'.

                                    (esAdministrador() ? '<input type="hidden" id="otroUsuario" name="otroUsuario" value="'. $elUsuario . '">' : '' ).

                                    '<select id="MCompras" name="MCompras" style="width:265px" onChange="document.getElementById(\'imagenResultados\').src = \'https://fa1.censosmkd.com/temas/default/_img/Nada.png\'; 

                                                                                                       $(\'#resultadosGratis\').fadeIn(200);setTimeout(\'enviaForma()\',200); changeFunc(value);">

                                       ' . $compras . '

                                    </select>' .

									((puedeCompartirCompras()) && ($id != -1) ? 

									'Compartir compra con:<br/>

									 <select id="compartirCompraUsuario" style="width:265px" name="compartirCompraUsuario">' .

									 $usuariosEnSelect .

									 '</select><input type="button" value="Compartir" OnClick="comparteCompra(' . $id . ');setTimeOut(\'enviaForma()\',1000);"><br/>' : '' ) .

									(esGenerador() ? '

                                    SubPoligonos:<br/>

                                    <select id="SelSubPoligonos" style="width:265px" name="SelSubPoligonos" onChange="muestraSubPoligonosCompra(' . $id . ');">' .

									getSubPoligonosDelUsuarioActual($id) .

									'</select>

									<br/>

									Asignar el SubPoligono al Usuario:

									<select id="selUsuariosSubPoligonos" name="selUsuariosSubPoligonos" style="width:265px">' .

									getUsuariosSubPoligonos() .

                                    '</select><input type="button" value="Asignar" OnClick="asignaSubPoligono();setTimeOut(\'enviaForma()\', 1000);"/>

									' : '' ) .

                                    '</form>

                                </div>

                                <div style="margin-left:20px;margin-right:20px">
                                    <font color="blue">Después, aplica los filtros necesarios para que el mapa despliegue las ubicaciones buscadas.</font>

                                    <br/>
                                    <a href="#" onClick="all_tree();">Seleccionar Todo</a>

                                </div>

                              </td>

                            </tr>

                            <tr valign="top" id="trTree" name="trTree">

                              <td>

                                <table border="0" height="100%">

                                  <tr valig="top" height="100%">

                                    <td width="5px"></td>

                                    <td valign="top" width="275px" style="border:solid 1px #CCC; background-color: #EEEEEE;">

                                      <div id="treecompras"></div>

                                    </td>

                                  </tr>

                                </table>

								

                              </td>

                            </tr>

                            <tr height="1px" id="trPie">

                              <td>

							 

							 <table width="90%" border="0" align="center" style="display:none" id="agregar_en_mis_compras">

                                  <tr>

                                  	<td width="100%">' .

            (esAdministrador() ? '<a href="#" onclick="elimina_compra(\'' . $id . '\');" title="' . (esCorporativo() ? 'Eliminar esta consulta' : 'Eliminar esta compra') . '" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/delete.png"  width="25" height="25" alt="Eliminar toda la compra" />

                                  		</a>' : '') .

            					   '</td>' .

								   

				(esGenerador() ? '<td style="" id="tdAgregarSubPoligono">	

                                  		<a href="#" onclick="agregaSubPoligonoUsuario(' . $id . ');setTimeout(\'enviaForma()\',1000)" title="Agrega el SubPoligono dibujado al usuario actual" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/subpoligonoAgregar.png" width="25" height="17" alt="Agrega el SubPoligono dibujado al usuario actual" />

                                  		</a>

                                  	</td>' : '' ) . 

									'<td style="display:none" id="subpoligono_en_mis_compras">	

                                  		<a href="#" onclick="crear_sub_poli(); $(\'#borrar_en_mis_compras\').css(\'display\',\'block\'); $(\'#subpoligono_en_mis_compras\').css(\'display\',\'none\'); resize();" title="Dibuja un polígono dentro del área de compra para delimitar el área de consulta!... comienza haciendo click aquí. Dibuja el subpolgono en el mapa haciendo click sobre el que será el primer vértice. Los puntos de tu seleccin serán automticamente reflejados en el subpolígono que estés creando!" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/subpoligono.png" width="25" height="17" alt="Crear Sub poligono, Comienza a dibujar el subpolgono en el mapa haciendo click sobre el que será el primer vértice. Los puntos de tu seleccin serán automticamente reflejados en el subpolígono que estés creando!" />

                                  		</a>

                                  	</td>

                                  	<td style="display:none" id="borrar_en_mis_compras">

                                     		<a href="#" onclick="borra_poligo_sub(); activar_compra(\'' . $id . '\'); $(\'#borrar_en_mis_compras\').css(\'display\',\'none\'); $(\'#subpoligono_en_mis_compras\').css(\'display\',\'block\'); resize()" title="Borrar sub poligono" >

                                              <img src="' . DIR_TEMA_ACTIVO . '_img/borrar_subpoligono.png"  width="25" height="21" alt="Borrar Sub poligono" />

                                        </a>

                                    </td>

                                    <td>

                                  		<a href="#" onclick="add_preg(\'' . $id . '\');" title="Agregar preguntas a la ' . (esCorporativo() ? 'consulta' : 'compra') . '" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/add_pregunta.png"  width="25" height="25" alt="Agregar preguntas adicionales a esta consulta" />

                                  		</a>

                                  	</td>

                                  	

                                  	

                                  	

                                <form action="infoAExcelDemografico.php" method="POST" id="formaExcel" name="formaExcel">   	

                                   <td>

                                        <input id="seleccionExcel" name="seleccionExcel" type="hidden" />

                                        <input id="latmax" name="latmax" type="hidden" />

                                        <input id="latmin" name="latmin" type="hidden" />

                                        <input id="longmax" name="longmax" type="hidden" />

                                        <input id="longmin" name="longmin" type="hidden" />

                                        <input id="poligono" name="poligono" type="hidden" />

										<td>

				

	                        <td><a href="#" onclick="infoAExcelDemografico( \'' . $id . '\', document.formaExcel );">

								<img src="' . DIR_TEMA_ACTIVO . '_img/icono_demografico.png" height="35" width="38" /> 

								</a>                                 

								</td>		

								

			

								    </form>

								    

					 <form action="infoAExcelNegocios.php" method="POST" id="formaExcel2" name="formaExcel2">   	

                             <td>

                                        <input id="seleccionExcel" name="seleccionExcel" type="hidden" />

                                        <input id="latmax" name="latmax" type="hidden" />

                                        <input id="latmin" name="latmin" type="hidden" />

                                        <input id="longmax" name="longmax" type="hidden" />

                                        <input id="longmin" name="longmin" type="hidden" />

                                        <input id="poligono" name="poligono" type="hidden" />

										<td>

	                        

								 <td>

								 <a href="#" onclick="infoAExcelNegocios( \'' . $id . '\', document.formaExcel2 );">

								<img src="' . DIR_TEMA_ACTIVO . '_img/icono_negocios.png" height="35" width="38" /> 

								</a>                                 

								</td>

								 

											

							

			

								    </form>

								    

						<form action="infoAExcel2_1.php" method="POST" id="formaExcel3" name="formaExcel3">   	

                             <td>

                                        <input id="seleccionExcel" name="seleccionExcel" type="hidden" />

                                        <input id="latmax" name="latmax" type="hidden" />

                                        <input id="latmin" name="latmin" type="hidden" />

                                        <input id="longmax" name="longmax" type="hidden" />

                                        <input id="longmin" name="longmin" type="hidden" />

                                        <input id="poligono" name="poligono" type="hidden" />

										<td>

	                        

								 <td>

								 <a href="#" onclick="infoAExcel2_1( \'' . $id . '\', document.formaExcel3 );">

								<img src="' . DIR_TEMA_ACTIVO . '_img/icono_demografico_integrado.png" height="35" width="38" /> 

								</a>                                 

								</td>

								 

											

								</tr>

			

								    </form>		    

								    

								    

								      

								  </table>

                             

                                

                              </td>

                            </tr>

                          </table>
                          </div>

                          </div>

                          <SCRIPT TYPE="text/javascript">
                          
                          //Reporte-Rafael
                          
                          function enviaReporte(){
                            var usuario = document.getElementById("usuarioR").innerHTML;
                            var idcompra = document.getElementById("idcompraR").innerHTML;
                              console.log("USUARIO: " + usuario + " ")
                              console.log("ID COMPRA: " + idcompra + " ")
                              //alert(usuario);
                              
                              
                            
            
                             
                             /*$.ajax({
                            		type: "POST",
                    	            url: "reporteFa.php",
                    	            data: "idcompra=" + idcompra + "&usuario=" + usuario,
                    	            success: function(data) {
                    	                console.log(data);
                    	            }
                            	})
                            	.fail(function(){
                            		console.log("error");
                            })*/
                           }
                          

                            $(document).ready(function(){

                            	if(\'-1\' != \'' . $id . '\'){

                                	setTimeout("$(\'#subpoligono_en_mis_compras\').css(\'display\',\'block\'); $(\'#agregar_en_mis_compras\').css(\'display\',\'block\'); $( \'#seleccion_miscompras\' ).text ( \'\' ); mostrar_policompra(\'' . $id . '\'); resize();",1000)

								} 

								setTimeout("var w = $( window ).width(); if ($( \'#tcompras\' ).is ( \':visible\' )) { $( \'#mapa\' ).css ( \'width\', w - 350 + \'px\'); $(\'#flecha_ayuda\').css(\'display\', \'block\'); }else{ $( \'#mapa\' ).css ( \'width\', w - 50 + \'px\' ); }",200); 

							});

                          </SCRIPT>

                          '

            , substr($valores, 0, -1));   // fin variable res


        }

    else 

    {
if($dominio=="coexito")
{
	 $res = array(($id != '-1' ? compras_opciones($id) : ''),

            '
            <div style="width:288px;aling-left:20px" id="Lmiscompras" class="miscompras">
            			
                          <table border="0" height="100%">

                            <tr height="1px" id="trEncabezado">

                              <td>

                                <div style="margin-left:20px;margin-right:20px">

                                  <table width="100%" border=0>

                                  <tr>

	                                  <td>

	                                     <div align="left"><input type="checkbox" name="agruparResultados" id="agruparResultados" checked onclick="borra_puntos_compra(); activar_compra(idCompra);"/>Agrupar</div>

	                                  </td>

	                                  <td>

	                                     <div align="left"><table border="0"><tr><td><input type="checkbox" name="
s" id="mostrarAgebsCompras" onclick="document.getElementById( \'mostrarAgebsC\' ).checked = document.getElementById ( \'mostrarAgebsCompras\' ).checked; muestraAgebsPolilinea(pcompras); resize();"/>Población</td><td><img style="display:none" id="mostrarAgebsImgCompra" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" /></td></tr></table></div>

	                                  </td>

	                                  <td>

	                                     <div align="left" style="display:none" name="divMostrarEtiquetasCompra" id="divMostrarEtiquetasCompra">

	                                     	<table border="0"><tr><td><input type="checkbox" style="display:none" name="mostrarEtiquetasCompra" id="mostrarEtiquetasCompra" onclick="muestraAgebsPolilinea(pcompras);" /></td><td><a href="#"  onclick="ayuda(\'9\');">Códigos</a></td></tr></table>

                                             </div>

	                                  </td>

	                              </tr>

                                  </table>

                                    <input type="checkbox" id="chkbox1" style="display:none"/>
                           
<input type="checkbox" id="chkbox2"  style="display:none"/>
                        
<input type="checkbox" id="chkbox3"  style="display:none"/>
                          
   <a href="#"  onclick="ayuda(\'19\');">Códigos Iconos</a> <br><br>
 
                                  <font color="green">Selecciona una ' . (esCorporativo() ? 'consulta' : 'compra') . ' para verla en el mapa!</font>

                                </div>

                                <div style="margin-left:10px;margin-right:10px">'.

                                    (esAdministrador() ? $resAdmin : '' ).

                                    '<form action="index.php" method="post" id="formaCompras" onsubmit="return compras_actualizar()">'.

                                    (esAdministrador() ? '<input type="hidden" id="otroUsuario" name="otroUsuario" value="'. $elUsuario . '">' : '' ).

                                    '<select id="MCompras" name="MCompras" style="width:265px" onChange="document.getElementById(\'imagenResultados\').src = \'https://fa1.censosmkd.com/temas/default/_img/Nada.png\'; 

                                                                                                       $(\'#resultadosGratis\').fadeIn(200);setTimeout(\'enviaForma()\',200)">

                                       ' . $compras . '

                                    </select>' .

									((puedeCompartirCompras()) && ($id != -1) ? 

									'Compartir compra con:<br/>

									 <select id="compartirCompraUsuario" style="width:265px" name="compartirCompraUsuario">' .

									 $usuariosEnSelect .

									 '</select><input type="button" value="Compartir" OnClick="comparteCompra(' . $id . ');setTimeOut(\'enviaForma()\',1000);"><br/>' : '' ) .

									(esGenerador() ? '

                                    SubPoligonos:<br/>

                                    <select id="SelSubPoligonos" style="width:265px" name="SelSubPoligonos" onChange="muestraSubPoligonosCompra(' . $id . ');">' .

									getSubPoligonosDelUsuarioActual($id) .

									'</select>

									<br/>

									Asignar el SubPoligono al Usuario:

									<select id="selUsuariosSubPoligonos" name="selUsuariosSubPoligonos" style="width:265px">' .

									getUsuariosSubPoligonos() .

                                    '</select><input type="button" value="Asignar" OnClick="asignaSubPoligono();setTimeOut(\'enviaForma()\', 1000);"/>

									' : '' ) .

                                    '</form>

                                </div>

                                <div style="margin-left:20px;margin-right:20px">

                                    <font color="green">Después, aplica los filtros necesarios para que el mapa despliegue las ubicaciones buscadas.</font>

                                    <br/>

                                </div>

                              </td>

                            </tr>

                            <tr valign="top" id="trTree" name="trTree">

                              <td>

                                <table border="0" height="100%">

                                  <tr valig="top" height="100%">

                                    <td width="5px"></td>

                                    <td valign="top" width="275px" style="border:solid 1px #CCC; background-color: #EEEEEE;">

                                      <div id="treecompras"></div>

                                    </td>

                                  </tr>

                                </table>

								

                              </td>

                            </tr>

                            <tr height="1px" id="trPie">

                              <td>

							  <form action="'.$paginaExcel.'.php" method="post" id="formaExcel" name="formaExcel">

							 <table width="90%" border="0" align="center" style="display:none" id="agregar_en_mis_compras">

                                  <tr>

                                  	<td width="100%">' .

            (esAdministrador() ? '<a href="#" onclick="elimina_compra(\'' . $id . '\');" title="' . (esCorporativo() ? 'Eliminar esta consulta' : 'Eliminar esta compra') . '" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/delete.png"  width="25" height="25" alt="Eliminar toda la compra" />

                                  		</a>' : '') .

            					   '</td>' .

								   

				(esGenerador() ? '<td style="" id="tdAgregarSubPoligono">	

                                  		<a href="#" onclick="agregaSubPoligonoUsuario(' . $id . ');setTimeout(\'enviaForma()\',1000)" title="Agrega el SubPoligono dibujado al usuario actual" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/subpoligonoAgregar.png" width="25" height="17" alt="Agrega el SubPoligono dibujado al usuario actual" />

                                  		</a>

                                  	</td>' : '' ) . 

									'<td style="display:none" id="subpoligono_en_mis_compras">	

                                  		<a href="#" onclick="crear_sub_poli(); $(\'#borrar_en_mis_compras\').css(\'display\',\'block\'); $(\'#subpoligono_en_mis_compras\').css(\'display\',\'none\'); resize();" title="Dibuja un polígono dentro del área de compra para delimitar el área de consulta!... comienza haciendo click aquí. Dibuja el subpolgono en el mapa haciendo click sobre el que será el primer vértice. Los puntos de tu seleccin serán automticamente reflejados en el subpolígono que estés creando!" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/subpoligono.png" width="25" height="17" alt="Crear Sub poligono, Comienza a dibujar el subpolgono en el mapa haciendo click sobre el que será el primer vértice. Los puntos de tu seleccin serán automticamente reflejados en el subpolígono que estés creando!" />

                                  		</a>

                                  	</td>

                                  	<td style="display:none" id="borrar_en_mis_compras">

                                     		<a href="#" onclick="borra_poligo_sub(); activar_compra(\'' . $id . '\'); $(\'#borrar_en_mis_compras\').css(\'display\',\'none\'); $(\'#subpoligono_en_mis_compras\').css(\'display\',\'block\'); resize()" title="Borrar sub poligono" >

                                              <img src="' . DIR_TEMA_ACTIVO . '_img/borrar_subpoligono.png"  width="25" height="21" alt="Borrar Sub poligono" />

                                        </a>

                                    </td>

                                    <td>

                                  		<a href="#" onclick="add_preg(\'' . $id . '\');" title="Agregar preguntas a la ' . (esCorporativo() ? 'consulta' : 'compra') . '" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/add_pregunta.png"  width="25" height="25" alt="Agregar preguntas adicionales a esta consulta" />

                                  		</a>

                                  	</td>

                                    <td>

                                        <input id="seleccionExcel" name="seleccionExcel" type="hidden" />

                                        <input id="latmax" name="latmax" type="hidden" />

                                        <input id="latmin" name="latmin" type="hidden" />

                                        <input id="longmax" name="longmax" type="hidden" />

                                        <input id="longmin" name="longmin" type="hidden" />

                                        <input id="poligono" name="poligono" type="hidden" />

										

										<a href="#" onclick="infoAExcel( \'' . $id . '\', document.formaExcel );" title="Manda Excel_'.$paginaExcel.'" >

												  <img src="' . DIR_TEMA_ACTIVO . '_img/icono_excel.png" height="25" width="25" alt="Manda tu información a Excel"/>

												

									 </a>  </td>  
									

						

						</tr>

						

						        <tr>

								 <td>  </td>

								 <td>  </td>

								 

								 <td></td>

	

								   

								 </td>

								  </tr>

								 

								  </table>

                                </form>

                              </td>

                            </tr>

                          </table>

                          
</div>
                          <SCRIPT TYPE="text/javascript">

                            $(document).ready(function(){

                            	if(\'-1\' != \'' . $id . '\'){

                                	setTimeout("$(\'#subpoligono_en_mis_compras\').css(\'display\',\'block\'); $(\'#agregar_en_mis_compras\').css(\'display\',\'block\'); $( \'#seleccion_miscompras\' ).text ( \'\' ); mostrar_policompra(\'' . $id . '\'); resize();",1000)

								} 

								setTimeout("var w = $( window ).width(); if ($( \'#tcompras\' ).is ( \':visible\' )) { $( \'#mapa\' ).css ( \'width\', w - 350 + \'px\'); $(\'#flecha_ayuda\').css(\'display\', \'block\'); }else{ $( \'#mapa\' ).css ( \'width\', w - 50 + \'px\' ); }",200); 

							});

                          </SCRIPT>

                          '

            , substr($valores, 0, -1));   // fin variable res  

}
else
{
        $res = array(($id != '-1' ? compras_opciones($id) : ''),

            '<div style="width:288px;aling-left:20px" id="Lmiscompras" class="miscompras">

                          <table border="0" height="100%">

                            <tr height="1px" id="trEncabezado">

                              <td>

                                <div style="margin-left:20px;margin-right:20px">

                                  <table width="100%" border=0>

                                  <tr>

	                                  <td>

	                                     <div align="left"><input type="checkbox" name="agruparResultados" id="agruparResultados" checked onclick="borra_puntos_compra(); activar_compra(idCompra);"/>Agrupar</div>

	                                  </td>

	                                  <td>

	                                     <div align="left"><table border="0"><tr><td><input type="checkbox" name="
s" id="mostrarAgebsCompras" onclick="document.getElementById( \'mostrarAgebsC\' ).checked = document.getElementById ( \'mostrarAgebsCompras\' ).checked; muestraAgebsPolilinea(pcompras); resize();"/>Población</td><td><img style="display:none" id="mostrarAgebsImgCompra" src="' . DIR_TEMA_ACTIVO . '_img/waiting.gif" /></td></tr></table></div>

	                                  </td>

	                                  <td>

	                                     <div align="left" style="display:none" name="divMostrarEtiquetasCompra" id="divMostrarEtiquetasCompra">

	                                     	<table border="0"><tr><td><input type="checkbox" style="display:none" name="mostrarEtiquetasCompra" id="mostrarEtiquetasCompra" onclick="muestraAgebsPolilinea(pcompras);" /></td><td><a href="#"  onclick="ayuda(\'9\');">Códigos</a></td></tr></table>

                                             </div>

	                                  </td>

	                              </tr>

                                  </table>

                                    <input type="checkbox" id="chkboxZona17" onclick="ir_a(\'mexicali\'); chkActivarZonas(\'Zona17\'); " style="display:none" />
                         
						<input type="checkbox" id="chkboxZona18" onclick="ir_a(\'Ciudad de Mexico\'); chkActivarZonas(\'Zona18\'); " style="display:none"/>
                         

 
                                  <font color="green">Selecciona una ' . (esCorporativo() ? 'consulta' : 'compra') . ' para verla en el mapa!</font>

                                </div>

                                <div style="margin-left:10px;margin-right:10px">'.

                                    (esAdministrador() ? $resAdmin : '' ).

                                    '<form action="index.php" method="post" id="formaCompras" onsubmit="return compras_actualizar()">'.

                                    (esAdministrador() ? '<input type="hidden" id="otroUsuario" name="otroUsuario" value="'. $elUsuario . '">' : '' ).

                                    '<select id="MCompras" name="MCompras" style="width:265px" onChange="document.getElementById(\'imagenResultados\').src = \'https://fa1.censosmkd.com/temas/default/_img/Nada.png\'; 

                                                                                                       $(\'#resultadosGratis\').fadeIn(200);setTimeout(\'enviaForma()\',200)">

                                       ' . $compras . '

                                    </select>' .

									((puedeCompartirCompras()) && ($id != -1) ? 

									'Compartir compra con:<br/>

									 <select id="compartirCompraUsuario" style="width:265px" name="compartirCompraUsuario">' .

									 $usuariosEnSelect .

									 '</select><input type="button" value="Compartir" OnClick="comparteCompra(' . $id . ');setTimeOut(\'enviaForma()\',1000);"><br/>' : '' ) .

									(esGenerador() ? '

                                    SubPoligonos:<br/>

                                    <select id="SelSubPoligonos" style="width:265px" name="SelSubPoligonos" onChange="muestraSubPoligonosCompra(' . $id . ');">' .

									getSubPoligonosDelUsuarioActual($id) .

									'</select>

									<br/>

									Asignar el SubPoligono al Usuario:

									<select id="selUsuariosSubPoligonos" name="selUsuariosSubPoligonos" style="width:265px">' .

									getUsuariosSubPoligonos() .

                                    '</select><input type="button" value="Asignar" OnClick="asignaSubPoligono();setTimeOut(\'enviaForma()\', 1000);"/>

									' : '' ) .

                                    '</form>

                                </div>

                                <div style="margin-left:20px;margin-right:20px">

                                    <font color="green">Después, aplica los filtros necesarios para que el mapa despliegue las ubicaciones buscadas.</font>

                                    <br/>

                                </div>

                              </td>

                            </tr>

                            <tr valign="top" id="trTree" name="trTree">

                              <td>

                                <table border="0" height="100%">

                                  <tr valig="top" height="100%">

                                    <td width="5px"></td>

                                    <td valign="top" width="275px" style="border:solid 1px #CCC; background-color: #EEEEEE;">

                                      <div id="treecompras"></div>

                                    </td>

                                  </tr>

                                </table>

								

                              </td>

                            </tr>

                            <tr height="1px" id="trPie">

                              <td>

							  <form action="'.$paginaExcel.'.php" method="post" id="formaExcel" name="formaExcel">

							 <table width="90%" border="0" align="center" style="display:none" id="agregar_en_mis_compras">

                                  <tr>

                                  	<td width="100%">' .

            (esAdministrador() ? '<a href="#" onclick="elimina_compra(\'' . $id . '\');" title="' . (esCorporativo() ? 'Eliminar esta consulta' : 'Eliminar esta compra') . '" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/delete.png"  width="25" height="25" alt="Eliminar toda la compra" />

                                  		</a>' : '') .

            					   '</td>' .

								   

				(esGenerador() ? '<td style="" id="tdAgregarSubPoligono">	

                                  		<a href="#" onclick="agregaSubPoligonoUsuario(' . $id . ');setTimeout(\'enviaForma()\',1000)" title="Agrega el SubPoligono dibujado al usuario actual" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/subpoligonoAgregar.png" width="25" height="17" alt="Agrega el SubPoligono dibujado al usuario actual" />

                                  		</a>

                                  	</td>' : '' ) . 

									'<td style="display:none" id="subpoligono_en_mis_compras">	

                                  		<a href="#" onclick="crear_sub_poli(); $(\'#borrar_en_mis_compras\').css(\'display\',\'block\'); $(\'#subpoligono_en_mis_compras\').css(\'display\',\'none\'); resize();" title="Dibuja un polígono dentro del área de compra para delimitar el área de consulta!... comienza haciendo click aquí. Dibuja el subpolgono en el mapa haciendo click sobre el que será el primer vértice. Los puntos de tu seleccin serán automticamente reflejados en el subpolígono que estés creando!" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/subpoligono.png" width="25" height="17" alt="Crear Sub poligono, Comienza a dibujar el subpolgono en el mapa haciendo click sobre el que será el primer vértice. Los puntos de tu seleccin serán automticamente reflejados en el subpolígono que estés creando!" />

                                  		</a>

                                  	</td>

                                  	<td style="display:none" id="borrar_en_mis_compras">

                                     		<a href="#" onclick="borra_poligo_sub(); activar_compra(\'' . $id . '\'); $(\'#borrar_en_mis_compras\').css(\'display\',\'none\'); $(\'#subpoligono_en_mis_compras\').css(\'display\',\'block\'); resize()" title="Borrar sub poligono" >

                                              <img src="' . DIR_TEMA_ACTIVO . '_img/borrar_subpoligono.png"  width="25" height="21" alt="Borrar Sub poligono" />

                                        </a>

                                    </td>

                                    <td>

                                  		<a href="#" onclick="add_preg(\'' . $id . '\');" title="Agregar preguntas a la ' . (esCorporativo() ? 'consulta' : 'compra') . '" >

                                  			<img src="' . DIR_TEMA_ACTIVO . '_img/add_pregunta.png"  width="25" height="25" alt="Agregar preguntas adicionales a esta consulta" />

                                  		</a>

                                  	</td>

                                    <td>

                                        <input id="seleccionExcel" name="seleccionExcel" type="hidden" />

                                        <input id="latmax" name="latmax" type="hidden" />

                                        <input id="latmin" name="latmin" type="hidden" />

                                        <input id="longmax" name="longmax" type="hidden" />

                                        <input id="longmin" name="longmin" type="hidden" />

                                        <input id="poligono" name="poligono" type="hidden" />

										

										<a href="#" onclick="infoAExcel( \'' . $id . '\', document.formaExcel );" title="Manda Excel_'.$paginaExcel.'" >

												  <img src="' . DIR_TEMA_ACTIVO . '_img/icono_excel.png" height="25" width="25" alt="Manda tu información a Excel"/>

												

									 </a>  </td>  

						

						</tr>

						

						        <tr>

								 <td>  </td>

								 <td>  </td>

								 

								 <td></td>

	

								   

								 </td>

								  </tr>

								 

								  </table>

                                </form>

                              </td>

                            </tr>

                          </table>

                          </div>

                          <SCRIPT TYPE="text/javascript">

                            $(document).ready(function(){

                            	if(\'-1\' != \'' . $id . '\'){

                                	setTimeout("$(\'#subpoligono_en_mis_compras\').css(\'display\',\'block\'); $(\'#agregar_en_mis_compras\').css(\'display\',\'block\'); $( \'#seleccion_miscompras\' ).text ( \'\' ); mostrar_policompra(\'' . $id . '\'); resize();",1000)

								} 

								setTimeout("var w = $( window ).width(); if ($( \'#tcompras\' ).is ( \':visible\' )) { $( \'#mapa\' ).css ( \'width\', w - 350 + \'px\'); $(\'#flecha_ayuda\').css(\'display\', \'block\'); }else{ $( \'#mapa\' ).css ( \'width\', w - 50 + \'px\' ); }",200); 

							});

                          </SCRIPT>

                          '

            , substr($valores, 0, -1));   // fin variable res   
          }
        }

    }

    return $res;

}





function validaexl($exl){

$res='';

$exl= $_GET['seleccionExcel'];

if ($exl=='infoAExcelNegocios')

 {

 $res='infoAExcelNegocios.php';

  }

  if ($exl=='infoAExcelDemografico')

  {

  $res='infoAExcelDemografico.php';

  }

	if ($exl=='infoAExcel2_1')

	{

	$res='infoAExcel2_1.php';

	}



return $res;



}


function TipoRadiog(){

$cad='REV';

$cad2='';

$res2 = @mysql_query('SELECT * from config');

$config = @mysql_fetch_assoc($res2);

if ($config["TipoRadiog"] == '1') {

	$cad='infoAExcel';	  /*Normal (3 archivos)*/

	}

	if ($config["TipoRadiog"] == '2') {

		$cad='infoAExce';	/*Dos aRCHIVOS	*/

	}

	if ($config["TipoRadiog"] == '3') {

		$cad='infoAExcel2_1';  /* Falta desarrollar*/

	}



	return $cad;

}



function etiqueta2($id, $add = '') {

    global $valores;



    $result = '';

    $resultado = @mysql_query('SELECT idetiqueta,idpregunta,valoretiqueta FROM etiqueta WHERE idpregunta="' . $id . '"');

    if (mysql_affected_rows() == 1) {

        $datos = mysql_fetch_assoc($resultado);

        $result = $id . '|' . $datos['idetiqueta'];

    } else {

        while ($datos = mysql_fetch_assoc($resultado)) {

            $result.='{ "title": "' . $datos['valoretiqueta'] . '", "key": "' . $id . '|' . $datos['idetiqueta'] . '", "select": "false"},';

        }

        if ($result != '') {

            $result = ', "children": [' . substr($result, 0, -1) . ']';

        }

    }



    return $result;

}




function getRaizCategorias($listaDeCategorias){

	$consulta = 'select * from categorias';

	$res = mysql_query($consulta);

		if(mysql_affected_rows() > 0){

			

		}
	
	echo 'echo 1 '.$res;

}



function GeneraCadena2($indice, $add = '') {

//Arbol seccion mis consultas
//@Rafael

    global $listaCategorias;
    

    global $listaPreguntas;
    

    $Rutas = '';




    if ($indice == -1)  {

                            

		$consulta = 'SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre 

                                   FROM categorias 

                                   WHERE ((categoriaPadre="") or (categoriaPadre is null)) and (idcategoria in (' . $listaCategorias . '))';

        $resultado = @mysql_query($consulta);

		//panelControl( 'Super', 6, $consulta );

        if (mysql_affected_rows() > 0) {

            while ($datos = mysql_fetch_assoc($resultado)) {

                if (strlen($Rutas) > 0) {

                    $Rutas.= ', ';

                }

                $Rutas .= '{"title": "' . $datos['des'] . '"' . GeneraCadena2($datos['id'], $add) . '}';

            }

        }

    } else {

//censosmkd2011
//Versión anterior


       /*$resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre ' .

                        'FROM categorias ' .

                        'WHERE categoriaPadre="' . $indice . '"  and categoriaPadre in (' . $listaCategorias . ')' .

                        'union all ' .

                        'select "" id, idpregunta idpreg, pregunta des, idcategoria padre ' .

                        'from preguntas ' .

                        'where idcategoria="' . $indice . '" and idpregunta in (' . $listaPreguntas . ')');*/
                        
                        
                        //echo $algo=parse_str($listaCategorias);
                        
                        
                        
                        
        //FUNCIONAL @rafael  
        //censosmkd2022
			
		
		$resultado = @mysql_query('SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre ' .

                        'FROM categorias ' .

                        'WHERE categoriaPadre="' . $indice . '"  ' .    //<----- Aquí va el filtro

                        'union all ' .

                        'select "" id, idpregunta idpreg, pregunta des, idcategoria padre ' .

                        'from preguntas ' .

                        'where idcategoria="' . $indice . '" and idpregunta in (' . $listaPreguntas . ')');


        while ($datos = mysql_fetch_assoc($resultado)) {

            if ($Rutas == '') {

                $Rutas = ', "children": [ ';

            } else {

                $Rutas .=', ';

            }

            if ($datos['idpreg'] != '') {

                $etiquetas = etiqueta2($datos['idpreg'], $add);

                if (($etiquetas != '') && (strpos($etiquetas, 'children') === false )) {

                    $Rutas .= '{"title": "' . $datos['des'] . '", "key":"' . $etiquetas . '"}';

                } else {

                    $Rutas .= '{"title": "' . $datos['des'] . '", "key":"preg_' . $datos['idpreg'] . '"' . $etiquetas . '}';

                }

            } else {

                $SAux = GeneraCadena2($datos['id'], $add);

                //$Rutas .= '{"title": "' . $datos['des'] . '", "key":"' . $datos['id'] . '"' . $SAux . '}';

                $Rutas .= '{"title": "' . $datos['des'] . '"' . ($SAux != '' ? $SAux : '') . '}';

            }

        }

        if ($Rutas != '') {

            $Rutas.= ']';

        }

    }


    return $Rutas;
    //echo $Rutas;

}



function treeviewJSON3Compras($add = '') {

//Arbol mis compras

    $idcompras = $_REQUEST['idCompra'];

    global $categoria2, $valores;

    global $listaCategorias;

    global $listaPreguntas;

    $listaCategorias = "";

    $listaPreguntas = "";

    if (conectar()) {

        $tmpeti = '';

        if ($add == '') {
            
            //hace una consulta a la compra y arroja solo los idpregunta de tu compra (1)

            $tmpr = @mysql_query('SELECT DISTINCT e.idpregunta, e.idetiqueta, e.valoretiqueta ' .

                            'FROM compraxetiqueta ce, etiqueta e ' .

                            'WHERE ce.idetiqueta = e.idetiqueta and ' .

                            'ce.idcompra = "' . mysql_real_escape_string($idcompras) . '"');

        } else {
            
            //En caso contrario trae todos los idpregunta (1.1)

            $tmpr = @mysql_query('SELECT DISTINCT idpregunta, idetiqueta, valoretiqueta ' .

                            'FROM etiqueta ' .

                            'WHERE idetiqueta not in ( select idetiqueta ' .

                            'from compraxetiqueta ' .

                            'where idcompra = "' . mysql_real_escape_string($idcompras) . '")');

        }

        //se guarda en variable tmpeti los idpregunta
        while ($datos = mysql_fetch_assoc($tmpr)) {

            $tmpeti .= $datos ['idpregunta'] . ',';
            
            $RBA = $datos['idpregunta'];
            
            //echo $RBA;
            

            $listaPreguntas = $listaPreguntas . ($listaPreguntas != '' ? ',' : '') . $datos['idpregunta'];
            //$listaPreguntas = $listaPreguntas . $RBA;
            

            
            

        }

        $cat = '';

        //$tmpeti = substr($tmpeti, 0, -1);
        
        $tmpeti = substr($tmpeti, 0,-1);
        

        if ($tmpeti != '') {
//(2)
            $resultado = @mysql_query('select "" id, idpregunta idpreg, pregunta des, idcategoria padre ' .

                            'from preguntas ' .

                            'where idpregunta in (' . $tmpeti . ')');



            while ($datos = mysql_fetch_assoc($resultado)) {

                $categoria2[] = array('id' => $datos ['id'], 'descripcion' => $datos ['des'], 'padre' => $datos ['padre'], 'idpreg' => $datos ['idpreg']);

                $cat.=$datos['padre'] . ',';

                $listaCategorias = $listaCategorias . ($listaCategorias != '' ? ',' : '') . $datos['id'];
                //$listaCategorias = $datos['id'];

            }

            $cat = substr($cat, 0, -1);

        }



        if ($cat != '') {

            $q = 'SELECT distinct idcategoria id,"" idpreg, descripcion des, categoriaPadre padre ' .

                    'FROM categorias ' .

                    'WHERE idcategoria in (' . $cat . ') ';



            while ($q != '') {

                //echo $cat . '<br>';

                $resultado = @mysql_query($q);

                $cat = '';

                while ($datos = mysql_fetch_assoc($resultado)) {

                    $existe = false;

                    foreach ($categoria2 as $value) {

                        if ($value['descripcion'] == $datos['des']) {

                            $existe = true;

                        }

                    }

                    if (!$existe) {
//mis consultas

                        $categoria2[] = array('id' => $datos ['id'], 'descripcion' => $datos ['des'], 'padre' => $datos ['padre'], 'idpreg' => $datos ['idpreg']);

                        $listaCategorias = $listaCategorias . ($listaCategorias != '' ? ',' : '') . $datos['id'];
                        //$listaCategorias = $listaCategorias .array($datos['id']);

                    }

                    if ($datos ['padre'] != '') {

                        $cat .= $datos ['padre'] . ',';

                    }

                }

                $cat = substr($cat, 0, -1);

                if ($cat == '') {

                    $q = '';

                } else {

                    $q = 'SELECT idcategoria id,"" idpreg, descripcion des, categoriaPadre padre ' .

                            'FROM categorias ' .

                            'WHERE idcategoria in (' . $cat . ') ';

                }

            }

        }

    }

    //return '[{"title":"probando rendimiento"}]';

    $miCadena = GeneraCadena2(-1, $add);

    return '[' . $miCadena . ']';

}



function compras_opciones($idcompras, $add = '') {

    $rest = '

            $( "#treecompras' . $add . '").dynatree({

            checkbox: true,

            selectMode: 3,

			initAjax: {url: "mapa.php?tipo=treeviewJSONCompras",

					   data: {idCompra: "' . $idcompras . '", // Optional arguments to append to the url

					   		  mode: "all",

    						  add:"' . $add . '"},

					   success: function() {setTimeout("sortCompra();", 200);}

					  },' .

            /*

              onLazyRead: function(node){

              node.appendAjax({url: "mapa.php?tipo=treeviewJSON",

              data: {"key": node.data.key, // Optional url arguments

              "mode": "all"},

              success: function(node) {}

              });

              },

             */

            '		  

            onSelect: function ( select, node ){

                    var selKeys = $.map ( node.tree.getSelectedNodes(), function ( node ){

                    r = node.data.key;

                    if ( r.indexOf ( \'_\' ) > -1 ){

                        r=null;

                    }

                    return r;

                });

                $( \'#seleccion_miscompras\' ).text ( selKeys + \'\' );

                //alert(selKeys);

                borra_puntos_compra(); activar_compra(\'' . mysql_real_escape_string($idcompras) . '\');

            },

            onDblClick: function ( node, event ){

            node.toggleSelect();

            },

            onKeydown: function(node, event) {

            if ( event.which == 32 ) {

            node.toggleSelect();

            return false;

            }

            },

            cookieId: "dynatree-miscompras' . $add . '",

            idPrefix: "dynatree-miscompras-' . $add . '"

            });';

    return $rest;

}



function resultados_cats_zona($derechos, $descripcion = "", $padre = "", $ordenar = "desc", $col = "idcategorias_zonas", $pag = "0", $filtros = "") {

    if (in_array('Listar cat.', $derechos)) {

        $restab = '<a  name="Resultados"></a><table width="100%" border="0" cellspacing="2">

			<tr class="tabla_titulo">

            <td width="56">Opciones</td>

            <td width="391">Descripción</td>

            <td width="392">Categoría Padre</td>

  		    </tr>';



        $resultado = @mysql_query('select categorias_zonas.idcategorias_zonas,categorias_zonas.descripcion,cat.descripcion cp  from categorias_zonas left join categorias_zonas cat on(categorias_zonas.categoriaPadre=cat.idcategorias_zonas) where categorias_zonas.idcategorias_zonas ' . $descripcion . $padre . ' order by categorias_zonas.' . $col . '  ' . $ordenar);





        while ($datos = @mysql_fetch_assoc($resultado)) {

            $restab.='<tr>

                <td>' . (in_array('Mod. Cat', $derechos) ? '<a href="javascript:eliminar_registro(\'del_cat_zona\',\'' . $datos[idcategorias_zonas] . '\')" onclick="return window.confirm(\'El registro ser eliminado. Desea continuar?\')" title="Eliminar"><img src="' . DIR_TEMA_ACTIVO . '_img/delete.png" width="16" height="16" alt="Eliminar" /></a>&nbsp;' : '') .

                    (in_array('Eliminar cat.', $derechos) ? '<a href="javascript:mostrar_ventana(\'Modificar Categoría Zona\',\'' . $datos[idcategorias_zonas] . '\')" title="Editar"><img src="' . DIR_TEMA_ACTIVO . '_img/editar.jpg" width="16" height="16" alt="Editar" /></a>' : '') . '</td>

                <td>' . htmlspecialchars($datos[descripcion]) . '</td>

                <td>' . htmlspecialchars($datos[cp]) . '</td></tr>';

        }



        $restab.=' </table><br/><br/><div id="res_enc" align="center">' . (mysql_num_rows($resultado) == '' ? '<span>No se encontraron registros</span>' : '') . '</div>';

    } else {

        $restab = '<div id="res_enc" align="center"><span>No tiene privilegios para listar resultados</span></div>';

    }

    return $restab;

}



function zonas_v($id) {

    global $etiqueta3;



    $result = '';

    $x = '';

    for ($i = 0; $i < count($etiqueta3); $i++) {

        if ($id == $etiqueta3[$i]['padre']) {

            $result.='{ title: "' . $etiqueta3[$i]['descripcion'] . '" ,key: "' . $etiqueta3[$i]['id'] . '" },';

        }

    }

    if ($result != '') {

        $result = substr($result, 0, -1);

    }

    return $result;

}



function Generazonas_v($indice) {

    global $categoria3;

    $Result = '';

    $idPadre = '';

    if ($indice != -1)

        $idPadre = $categoria3[$indice]['id'];

    for ($i = 0; $i < count($categoria3); $i++) {

        if ($categoria3[$i]['padre'] == $idPadre) {

            if ($Result != '') {

                $Result.=', ';

            }

            $Result.='{ title: "' . $categoria3[$i]['descripcion'] . '" ';



            $ResultAux = ($categoria3[$i]['idpreg'] != '' ? zonas_v($categoria3[$i]['idpreg']) : ' ');

            $ResultAux2 = Generazonas_v($i);

            //$ResultAux2 = Generazonas_v ( $i,$add );

            if ($ResultAux != '') {

                if ($ResultAux2 != '')

                    $ResultAux = $ResultAux . ',' . $ResultAux2;

            }

            else

                $ResultAux = $ResultAux2;



            if ($ResultAux != '')

                $Result .= ', children: [ ' . $ResultAux . ']';

            $Result = $Result . '}';

        }

    }

    return $Result;

}



function zonas_mostrar_arbol() {

    global $etiqueta3, $categoria3;

    if (conectar()) {

        $tmpeti = '';



        $tmpr = @mysql_query('SELECT idzonas,nombre,idcategorias_zonas FROM zonas');

        while ($datos = mysql_fetch_assoc($tmpr)) {

            $etiqueta3[] = array('id' => $datos['idzonas'], 'descripcion' => $datos['nombre'], 'padre' => $datos['idcategorias_zonas']);

        }



        $resultado = @mysql_query('select idcategorias_zonas id,idcategorias_zonas idpreg,  descripcion des,categoriapadre padre  from categorias_zonas');

        while ($datos = mysql_fetch_assoc($resultado)) {

            $categoria3[] = array('id' => $datos['id'], 'descripcion' => $datos['des'], 'padre' => $datos['padre'], 'idpreg' => $datos['idpreg']);

        }

        return 'treezonas_view = [' . Generazonas_v(-1) . '];';

        ;

    }

}



function chat_user() {

    $resultado = @mysql_query('select DISTINCT  idusuario from chat where idusuario<>"Admin" order by fecha desc');

    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {



        $opcion.='<option value="' . htmlspecialchars($datos['idusuario']) . '">' . htmlspecialchars($datos['idusuario']) . '</option>';

    }

    return '<b>Contestar al usuario: &nbsp;&nbsp;</b><select style="width: 295px;" name="usuario" id="usuario">' . $opcion . '</ select>';

}



function censos_listar() {

    $resultado = @mysql_query('SELECT idcenso,nombrecenso FROM censos order by nombrecenso');

    $opcion = '';

    while ($datos = mysql_fetch_assoc($resultado)) {



        $opcion.='<option value="' . htmlspecialchars($datos['idcenso']) . '">' . htmlspecialchars($datos['nombrecenso']) . '</option>';

    }

    return $opcion;

}



function puntoxasiganacion($idcensos, $usuario) {

    $restab = '<form  action="#" method="post" name="Fpuntosxcenso" ><table width="100%" border="0" cellspacing="2">

						<tr class="tabla_titulo">

							<td width="50">

								<input type="checkbox" id="all_check" value="all" onclick="check_all()" />

								<input type="checkbox" name="chck[]" id="chck[]" value="" style="display:none" />

							</td>

							<td width="230">Razon Social</td>

							<td width="120">Latitud </td>

							<td width="120">Longitud</td>

							<td>Censo</td>' . ( $usuario == 'Supervisor' ? '<td>Usuario</td>' : '' ) .

            '</tr>';

    if ($idcensos == '') {

        $restab .= '</table></form><div id="res_enc" align="center"><span>Selecciona un censo para ver los puntos que te corresponden</span></div>';

    } else {

        $consulta = 'SELECT puntos.idpunto, puntos.razonSocial, puntos.latitud, puntos.longitud, censos.nombrecenso, ' .

                'censoxpunto.usuario ' .

                'FROM censoxpunto, puntos, censos ' .

                'WHERE censoxpunto.idcenso = censos.idcenso and censoxpunto.idpunto = puntos.idpunto and ' .

                'censoxpunto.idcenso = "' . mysql_real_escape_string($idcensos) . '" ';

        if ($usuario != 'Supervisor') {

            $consulta .= 'and censoxpunto.usuario ' .

                    ( $usuario == 'Coordinador' ? ' is null' : '= "' . mysql_real_escape_string($_SESSION ['user']) . '"' );

        }

        $resultado = @mysql_query($consulta);

        if (mysql_affected_rows() == 0) {

            $restab .= '</table></form><div id="res_enc" align="center"><span>No se encontraron puntos disponibles para ti.</span></div>';

        } else {

            while ($datos = @mysql_fetch_assoc($resultado)) {

                $restab .= '<tr>

												<td>

													<input type="checkbox" name="chck[]" id="chck[]" value="' . $datos ['idpunto'] . '" />

													<a href="http://maps.google.com.mx/?q=' . $datos ['latitud'] . ',' .

                        $datos ['longitud'] . '" title="Ver punto en mapa" target="_blank">' .

                        '<img src="' . DIR_TEMA_ACTIVO . '_img/posicion_mapa.png" width="23" height="16" alt="ver punto en mapa" /></a>

												</td>

												<td>' . htmlspecialchars($datos ['razonSocial']) . '</td>

												<td>' . htmlspecialchars($datos ['latitud']) . '</td>

												<td>' . htmlspecialchars($datos ['longitud']) . '</td>

												<td>' . htmlspecialchars($datos ['nombrecenso']) . '</td>';

                if ($usuario == 'Supervisor') {

                    $restab .= '<td>' . htmlspecialchars($datos ['usuario']) . '</td>';

                }

                $restab .= '</tr>';

            }

            $restab.='</table></form>';

        }

    }

    return $restab;

}



function etiqueta4($id) {

    global $etiqueta4;



    $result = '';

    $x = '';

    for ($i = 0; $i < count($etiqueta4); $i++) {

        $x.=$id . '-' . $etiqueta4[$i]['padre'];

        if ($id == $etiqueta4[$i]['padre']) {

            $result.='{ title: "' . $etiqueta4[$i]['descripcion'] . '" ,key: "' . $id . '|' . $etiqueta4[$i]['id'] . '" },';

        }

    }



    if ($result != '') {

        $result = ',children: [' . substr($result, 0, -1) . ']';

    }





    return $result;

}



function GeneraCadena4($indice) {

    global $categoria4;





    $Result = '';

    $Rutas = '';

    $r = '';

    if ($indice == -1) {

        for ($i = 0; $i < count($categoria4); $i++) {

            if ($categoria4[$i]['padre'] == '') {

                if (strlen($Rutas) > 0) {

                    $Result.= ', ';

                }



                $Result .= '{title: "' . $categoria4[$i]['descripcion'] . '"' . GeneraCadena4($i) . '}';

                $Rutas.=$Result;

                $Result = '';

            }

            $r = $Rutas;

        }

    } else

    if ($categoria4[$indice]['id'] != '') {



        $idPadre = $categoria4[$indice]['id'];

        for ($i = 0; $i < count($categoria4); $i++) {

            if ($categoria4[$i]['padre'] == $idPadre) {

                if ($Result == '') {

                    $Result = ', children: [ ';

                } else {

                    $Result.=', ';

                }

                $Result.='{ title: "' . $categoria4[$i]['descripcion'] . '" ' . ($categoria4[$i]['idpreg'] != '' ? etiqueta4($categoria4[$i]['idpreg']) : ' ') . GeneraCadena4($i) . '}';

            }

        }

        if ($Result != '') {

            $Result.= ']';

        }

        $r = $Result;

    }



    return $r;

}



function ver_preguntas($idcompras) {

    global $etiqueta4, $categoria4;

    if (conectar()) {

        $resultado = @mysql_query('select "" id,idpregunta idpreg,  pregunta des,idcategoria padre FROM preguntas where costo="0"');

        $cat = '';

        $pregfree = '';

		

        while ($datos = mysql_fetch_assoc($resultado)) {

            $categoria4[] = array('id' => $datos['id'], 'descripcion' => $datos['des'], 'padre' => $datos['padre'], 'idpreg' => $datos['idpreg']);

            $pregfree.=$datos['idpreg'] . ',';

            if ($datos['padre'] != '') {

                  $cat.=$datos['padre'] . ',';

            }

        }

		

        $cat = substr($cat, 0, -1);

        $pregfree = substr($pregfree, 0, -1);



        $tmpr = @mysql_query('SELECT  idpregunta,idetiqueta,valoretiqueta FROM etiqueta where idpregunta in(' . $pregfree . ')');

        while ($datos = @mysql_fetch_assoc($tmpr)) {

            $etiqueta4[] = array('id' => $datos['idetiqueta'], 'descripcion' => $datos['valoretiqueta'], 'padre' => $datos['idpregunta']);

        }

		

        $q = 'SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias where idcategoria in (' . $cat . ') ';



        while ($q != '') {

            $resultado = @mysql_query($q);

            $cat = '';

            while ($datos = @mysql_fetch_assoc($resultado)) {

                $categoria4[] = array('id' => $datos['id'], 'descripcion' => $datos['des'], 'padre' => $datos['padre'], 'idpreg' => $datos['idpreg']);

                if ($datos['padre'] != '') {

                    $cat.=$datos['padre'] . ',';

                }

            }

            $cat = substr($cat, 0, -1);

            if ($cat == '') {

                $q = '';

            } else {

                $q = 'SELECT idcategoria id,"" idpreg, descripcion des,categoriaPadre padre FROM categorias where idcategoria in (' . $cat . ') ';

            }

        }



        //vtreegratis = ['.GeneraCadena4(-1).'];

        $rest = '

            vtreegratis = [];

            $("#treegratis").dynatree({

            checkbox: true,

            selectMode: 3,

            children: vtreegratis,

            onSelect: function(select, node) {

            var selKeys = $.map(node.tree.getSelectedNodes(), function(node){

            r=node.data.key;

            if (r.indexOf(\'_\')>-1)

            {r=null;}



            return r;

            });



            $(\'#selecciontreegratis\').text(selKeys+\'\');

            mostrar_puntos_gratis();

            },

            onDblClick: function(node, event) {

            node.toggleSelect();

            },

            onKeydown: function(node, event) {

            if( event.which == 32 ) {

            node.toggleSelect();

            return false;

            }

            },



            cookieId: "dynatree-treegratis",

            idPrefix: "dynatree-treegratis-"



            });

            ';



        return $rest;

    }

}



function cotizar_compras($poligono, $selecction) {

    /*

      $coord = explode ( '|', substr ( $poligono, 0, -1 ) );

      $latmax = '';

      $latmin = '';

      $longmax = '';

      $longmin = '';

      foreach ( $coord as $value ){

      list ( $lat, $long ) = explode ( ',', $value );

      if ( $latmax == '' ){

      $latmax = $lat;

      $latmin = $lat;

      $longmax = $long;

      $longmin = $long;

      }else{

      $latmax = max ( $lat, $latmax );

      $latmin = min ( $lat, $latmin );

      $longmax = max ( $long, $longmax );

      $longmin = min ( $long, $longmin );

      }

      }

      return puntos_mapa ( $selecction, $latmax, $latmin, $longmax, $longmin, $poligono, 'true' );

     */

    $coord = explode('|', substr($poligono, 0, -1));

    $resArea = null;

    for ($i = 0; $i < count($coord) - 1; $i++) {

        $punto = explode(',', $coord[$i]);

        $resArea[] = array("Longitud" => $punto[0], "Latitud" => $punto[1]);

    }



    $km = AreaPoligonoKm($resArea);

    if ($km < 1)

        $km = 1;

    $resultado = @mysql_query('SELECT * FROM config');

    $datos = @mysql_fetch_assoc($resultado);



    return $km * $datos['costoXkm'];

    //return 50;

}







class geoLocateIp{

	private $serviceLocateURL = 'http://api.hostip.info/?ip=';

 

	public function getLocationFromIp(){

		$ip = $this->getIpAdress();

		if (empty($ip))

			throw new Exception('Error retrieving IP address');

		// Use the method your server supports ( most of them only support curl )

		$xmlData = geoLocateIp::file_get_contents_curl($this->serviceLocateURL.$ip);

		//$xmlData = file_get_contents($this->serviceLocateURL.$ip);

		if (empty($xmlData))

			throw new Exception('Error retrieving xml');

		$locationInfo = $this->parseLocationData($xmlData);

		return $locationInfo;

	}

 

	public function getIpAdress(){

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

			   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		}

		elseif (isset($_SERVER['HTTP_VIA'])) {

		   $ip = $_SERVER['HTTP_VIA'];

		}

		elseif (isset($_SERVER['REMOTE_ADDR'])) {

		   $ip = $_SERVER['REMOTE_ADDR'];

		}

		else {

		   $ip = NULL;

		}

		return $ip;

    }

 

	private function parseLocationData($xmlData){

		// Use of Simple XML extension of PHP 5

		$xml = simplexml_load_string($xmlData);

		if (!is_object($xml))

		    throw new Exception('Error reading XML');

		$infoHost = $xml->xpath('//gml:featureMember');

		$city = $xml->xpath('//gml:featureMember//gml:name');

		$coordinates = $infoHost[0]->xpath('//gml:coordinates');

		$coordinates = explode(',', (string) $coordinates[0]);

		$info = array (

			"City"		=> (string) $city[0],

			"CountryName"	=> (string) $infoHost[0]->Hostip->countryName,

			"CountryCode"	=> (string) $infoHost[0]->Hostip->countryAbbrev,

			"Longitude"	=> $coordinates[0],

			"Latitude"	=> $coordinates[1]

		);

		return $info;

	}

 

	public static function file_get_contents_curl($url){

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);

		curl_close($ch);

		return $data;

	}

}





		// Funciones para caragr archivos pdf
		function upfile3 () 

		{

			$idpunto = $_GET['Id'];

		if ( $_FILES [ 'filepdf3' ][ 'error' ] == 0 ){

			$file_opc = pathinfo ( $_FILES [ 'filepdf3' ][ 'name' ] );

			$namefile = $idpunto .".pdf";

			$ruta = "archivos_metrored/".$namefile;

			$orig = isset($_FILES['filepdf3']) && isset($_FILES['filepdf3']['tmp_name']) ? $_FILES['filepdf3']['tmp_name'] : '';

           copy($_FILES['filepdf3']['tmp_name'], $ruta);

	//		move_uploaded_file($orig, $ruta);

		}							

	}
	function upfile4 () 

		{

			$idpunto = $_GET['Id'];

		if ( $_FILES [ 'filepdf4' ][ 'error' ] == 0 ){

			$file_opc = pathinfo ( $_FILES [ 'filepdf4' ][ 'name' ] );

			$namefile = $idpunto ."._contrato.pdf";

			$ruta = "archivospdf/".$namefile;

			$orig = isset($_FILES['filepdf4']) && isset($_FILES['filepdf4']['tmp_name']) ? $_FILES['filepdf4']['tmp_name'] : '';

           copy($_FILES['filepdf4']['tmp_name'], $ruta);

	//		move_uploaded_file($orig, $ruta);

		}							

	}

function upfile1 () 

		{

			$idpunto = $_GET['Id'];

		if ( $_FILES [ 'filepdf1' ][ 'error' ] == 0 ){

			$file_opc = pathinfo ( $_FILES [ 'filepdf1' ][ 'name' ] );

			$namefile = $idpunto ."_layout.pdf";

			$ruta = "archivospdf/".$namefile;

			$orig = isset($_FILES['filepdf1']) && isset($_FILES['filepdf1']['tmp_name']) ? $_FILES['filepdf1']['tmp_name'] : '';

           copy($_FILES['filepdf1']['tmp_name'], $ruta);

	//		move_uploaded_file($orig, $ruta);

		}							

	}

	

function upfile2 () 

		{

			$idpunto = $_GET['Id'];

		if ( $_FILES [ 'filepdf2' ][ 'error' ] == 0 ){

			$file_opc = pathinfo ( $_FILES [ 'filepdf2' ][ 'name' ] );

			$namefile = $idpunto ."_contrato.pdf";

			$ruta = "archivospdf/".$namefile;

            $orig = isset($_FILES['filepdf2']) && isset($_FILES['filepdf2']['tmp_name']) ? $_FILES['filepdf1']['tmp_name'] : '';

            copy($_FILES['filepdf2']['tmp_name'], $ruta);

	//		move_uploaded_file($orig, $ruta);



		}							

	}

	







?>