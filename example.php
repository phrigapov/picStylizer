<?php

exit("comment this line"); // comment this line when you want to test this file

include("./picStylizer.php");

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
