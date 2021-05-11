<?phpinclude("./picStylizer.php");

// Initialize Class
$pS = new picStylizer();

// define folder configuration
$config = array(
	// set the origin folder
	"origin" => array(
		"images" => "origin/images", // folder from where the script will take the images,
		"include_subfolders" => true
	),
	// set destination folder
	"destination" => array(
		"styles" => "destination/css/sprites.css", // define css style of sprites
		"sprites" => "destination/sprites/sprites.png", // define the sprite image result
		//"example" => "destination/example/sprites.html", // define the html example
		"rel_path_to_sprite_image" => "./", // define the path
		"rel_path_to_sprite_css" => "./"    // define the path
	)
);
$pS->setFoldersConfig($config);

// resize images
$pS->resizeCoefficient(0.7);

// define minization (default: true)
$pS->setMinization();

// define css style by default
$pS->setCssInit($css ='body {backgound-color:#000; color:#fff; font-size:14px;}', $class_prefix='mySprite');

// gen sprites, styles and html example
set_time_limit(1000);
$pS->getSprite($save_html=true, $redirect_to_html=true);
?>