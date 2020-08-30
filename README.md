# picStylizer

picStylizer is a PHP class that create sprite and css style file from images folder

Notice: if you want to change the image onMouseOver (hover) you have add "_hover" at the end of filename before the extention
Example: 
// initial image
icon.png
// hover image
icon_hover.png //adding _hover
// the result css code will be:
.icon {...}
.icon:hover {...}

# Usage

```php
// Initialize Class
$pS = new picStylizer();

// define folder configuration
$config = array(
	// set the origin folder
	"origin" => array(
		"images" => "origin/images" // folder from where the script will take the images
	),
	// set destiny folder
	"destiny" => array(
		"styles" => "destiny/css/sprites.css", // define css style of sprites
		"sprites" => "destiny/sprites/sprites.png", // define the sprite image result
		"example" => "destiny/example/sprites.html", // define the html example
		"ini_path" => "../../" // define the path
	)
);
$pS->setFoldersConfig($config);

// resize images
$pS->resizeCoefficient(0.7);

// define minization (default: true)
$pS->setMinization();

// define css style by default
$css = 'body {backgound-color:#000;font-family:courier;color:#fff,font-size:14px;}';
$pS->setCssInit($css, $class_prefix='mySprite');

// gen sprites, styles and html example
$pS->getSprite($save_html=true, $redirect_to_html=true);
```


# Results
Output source code will be like this:

```
<link rel="stylesheet" href="../../sprites.css">
<div class="sprite-each mySprite-image1"></div>
<div class="sprite-each mySprite-image2"></div>
...
```
sprites.css :
```
body {background-color:#000;font-family:courier;color:#fff;font-size:14px;}.sprite-each{background-image:url("../../sprites.png"); 
.mySprite-image1 {background-position: 0 -XXX; background-repeat:no-repeat;width:XXX; height:XXX}
.mySprite-image2 {background-position: 0 -XXX; background-repeat:no-repeat;width:XXX; height:XXX}
...
```
