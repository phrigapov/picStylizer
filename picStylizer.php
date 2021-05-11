<?php

/**
 * Create a Css style and sprite from images
 * 
 * @version 1.2
 * @link https://github.com/lutian/picStylizer
 * @author Lutian (Luciano Salvino)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Luciano Salvino
 *
 * Thanks Aldo Conte for improvements on script!
 * Reduces the size of CSS script and sprite in a big way
 */
 
 class picStylizer {
 
	/**
     * @var string The image result source
     */
    private $im;
	
	/**
     * @var int W image
     */
	private $im_w = 0;
	
	/**
     * @var int H image
     */
	private $im_h = 0;
	
	/**
     * @var int X image
     */
	private $im_x = 0;
	
	/**
     * @var int Y image
     */
	private $im_y = 0;
	
	/**
     * @var string The image tmp result source
     */
    private $temp;
	
	/**
     * @var int W temp
     */
	private $temp_w = 0;
	
	/**
     * @var int H temp
     */
	private $temp_h = 0;
	
	/**
     * @var int temp separation in px
     */
	private $temp_sep = 2;
	
	/**
     * @var string temp_css
     */
	private $temp_css = '';
	
	/**
     * @var string temp_css
     */
	private $temp_min_sep = "\n";

	/**
     * @var string temp_html
     */
	private $temp_html = '';
	
	/**
     * @var string version
     */
	private $version = '1.1';
	
	/**
     * @var array folders_config folder
     */
	private $folders_config = array(
								"origin" => array(
									"images" => "origin/images",
									"include_subfolders" => true
								), 
								"destination" => array(
									"styles" => "destination/css/sprites.css",
									"sprites" => "destination/sprites/sprites.png",
									"rel_path_to_sprite_image" => "./",
									"example" => "destination/example/sprites.html",
								)
							);
	/*
	* @var string define default css style
	*/
	private $css_init = '';
	
	/**
     * @var array sprites
     */
	private $sprites = array();
	
	/*
	* crate a sprite from images
	* @return: string $imageResult image result path
	*/
	
	public function getSprite($save_html = true, $redirect = true)
    {
    
		// first read the origin folder looking for png pictures
		$arrImages = $this->readFolder($this->folders_config["origin"]["images"]);
		print_r($arrImages);
		// save images array
		$this->setSprite($arrImages);
		
		// create the sprite
		$this->createSprite($save_html, $redirect);

    }
	
	/*
	* read folder looking for images
	* @return: array $result 
	*/
	private function readFolder($dir='',$acceptedformats=array('png')) {
		$result = array(); 
		$cdir = scandir($dir); 
		// read origin dir
		foreach ($cdir as $key => $value) 
		{ 
			// exclude non files
			if (!in_array($value,array(".",".."))) 
			{ 
				// if have sub folders loop on the same function
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
				{ 
					if ($this->folders_config["origin"]["include_subfolders"])
						$result[$value] = $this->readFolder($dir . DIRECTORY_SEPARATOR . $value); 
				} 
				else 
				{ 
					// exclude files with extentions not accepted
					$ext = strtolower(substr($value, strrpos($value, '.') + 1));
					if(in_array($ext, $acceptedformats)) {
						$result[] = $value; 
					}
				} 
			} 
		} 
		   
		return $result; 
	}
	
	
	/*
	* Include subfolders or not
	*/
	
	
	/*
	* get the images info from array
	*/
	private function getImageInfoFromDir($dir,$subdir='') {
	
		foreach($dir as $key => $value){
			if(!is_int($key)) {
				$this->getImageInfoFromDir($value,$subdir . $key . DIRECTORY_SEPARATOR);
			} else {
				$this->calculateSpriteWidthHeight($this->folders_config["origin"]["images"] . DIRECTORY_SEPARATOR . $subdir . $value);
			}
		}
	}
	
	/*
	* get the images info from array
	*/
	private function getImagesFromDir($dir,$subdir='') {
		foreach($dir as $key => $value){
			if(!is_int($key)) {
				$this->getImagesFromDir($value,$subdir . $key . DIRECTORY_SEPARATOR);
			} else {
				$this->proccessMedia($this->folders_config["origin"]["images"] . DIRECTORY_SEPARATOR . $subdir . $value);
			}
		}
	}
	
	/*
	* resize image to coefficient
	*/
	public function resizeCoefficient($coefficient=1.0) {
		$this->coeff = $coefficient;
	}
	
	/*
	* create the sprite
	*/
	private function createSprite($save_html = true, $redirect = true) {
	
		$arrImages = $this->getSprites();

		
		if(count($arrImages)>0) {
		
			// calculate sprite width and height
			$this->getImageInfoFromDir($arrImages); 
		
			// create the empty sprite 
			$this->im = imagecreatetruecolor($this->im_w*$this->coeff, $this->im_h*$this->coeff);
			imagealphablending($this->im, false);
			$transparency = imagecolorallocatealpha($this->im, 0, 0, 0, 127);
			imagefill($this->im, 0, 0, $transparency);
			imagesavealpha($this->im, true);
			
			$x = 0;
			$y = 0;
		
			$this->getImagesFromDir($arrImages);
			
			// save the sprite
			$this->saveSprite();
			
			// save the css
			$this->saveCss();
			
			// save the example html
			if ($save_html) $this->saveHtml();
		
		}
		
		
		
		if ($redirect)
        {
            header('location:' . $this->folders_config['destination']['example']);
            exit();
        }
	
	}
	
	/*
	* calculate sprite w & h
	* @param: string $image
	* @return: object $this->temp 
	*/
	private function calculateSpriteWidthHeight($image) {
		if(is_file($image)) {
      $arrImage = @getimagesize($image);
			// updated by Aldo Conte			
			$tmps =	$arrImage[0]+$this->temp_sep;
			if ($tmps > $this->im_w) $this->im_w = $arrImage[0]+$this->temp_sep;
			// end
			$this->im_h += $arrImage[1]+$this->temp_sep;
		}
	}
	
	/*
	* proccess media
	* @param: string $image
	* @return: object $this->temp 
	*/
	private function proccessMedia($image) {
		if(is_file($image)) {
            $arrImage = @getimagesize($image);
			$this->temp_w = $arrImage[0];
			$this->temp_h = $arrImage[1];
			
			$tmp = ImageCreateTrueColor($this->im_w*$this->coeff, $this->im_h*$this->coeff);
			imagealphablending($tmp, false);
			$col=imagecolorallocatealpha($tmp,255,255,255,0);
			imagefilledrectangle($tmp,0,0,$this->im_w*$this->coeff, $this->im_h*$this->coeff,$col);
			imagealphablending($tmp,true);
			
			$gd_ext = substr($image, -3);
			
			if(strtolower($gd_ext) == "gif") {
              if (!$this->temp = imagecreatefromgif($image)) {
                    exit;
              }
            } else if(strtolower($gd_ext) == "jpg") {
              if (!$this->temp = imagecreatefromjpeg($image)) {
                    exit;
              }
            } else if(strtolower($gd_ext) == "png") {
              if (!$this->temp = imagecreatefrompng($image)) {
                    exit;
              }
            } else {
                die;
            }
			
			imagecopyresampled($tmp, $this->im, 0, 0, 0, 0, $this->im_w*$this->coeff, $this->im_h*$this->coeff, $this->im_w, $this->im_h);
			imagealphablending($tmp,true);
			
			// add each image to sprite
			
			// updated by Aldo Conte
			imagecopyresampled($this->im, $this->temp, 0, $this->im_y, 0, 0, $this->temp_w*$this->coeff, $this->temp_h*$this->coeff, $this->temp_w, $this->temp_h);  
			// end
			
			imagealphablending($this->im,true);
			
			$ext = substr($image, strrpos($image, '.'));
			
			$filename = basename($image,$ext);
			
			// add piece of script to css
			$this->genCssPieceCode($filename);
			
			// add piece of script to html example
			$this->genHtmlPieceCode($filename);
			
			$this->im_x += $this->temp_w*$this->coeff+$this->temp_sep;
			$this->im_y += $this->temp_h*$this->coeff+$this->temp_sep;
			
		} else {
            die;
        }
	}
	
	private function genCssPieceCode($name) {
		// if filename contain "_hover" add the part of code
		if(strpos($name,"_hover")!==false) $name = substr($name,0,-6).':hover';

        // updated by Aldo Conte
        $temp_css_detail = "background-position: 0 -" . $this->im_y . "px; background-repeat:no-repeat;" . $this->temp_min_sep;
        // end
        $temp_css_detail .= "width:" . $this->temp_w*$this->coeff . "px; height:" . $this->temp_h*$this->coeff . "px;" . $this->temp_min_sep;
        $temp_css = "." . $this->class_prefix . $name . " {" . $temp_css_detail . "}" . $this->temp_min_sep;
        $this->temp_css .= $temp_css;
	}
	
	private function genHtmlPieceCode($name) {
        // if filename contain "_hover" add the part of code
        if (strpos($name, "_hover") === false)
        {
            $temp_html = '<h3>class: .' . $this->class_prefix . $name . '</h3>';
            $temp_html .= '<div class="'.$this->class_prefix.'each ' . $this->class_prefix . $name . '">';
            $temp_html .= '</div>';
            $this->temp_html .= $temp_html;
        }
	}
	
	private function saveSprite() {
		imagepng($this->im,$this->folders_config['destination']['sprites'],3); 
		return $this->im;
		imagedestroy($this->im);
		//echo 'salvou';
	}
	
	private function saveCss() {
        $css_path = $this->folders_config['destination']['styles'];
        $css_img = '.'.$this->class_prefix.'each{background-image:url("' . $this->folders_config['destination']['rel_path_to_sprite_image'] . basename($this->folders_config['destination']['sprites']) . '");';
        file_put_contents($css_path, $this->css_init . $this->temp_css . $css_img);
	}
	
	private function saveHtml() {
		$html_path = $this->folders_config['destination']['example'];
		$html = '<link rel="stylesheet" href="'.$this->folders_config['destination']['rel_path_to_sprite_css']. basename($this->folders_config['destination']['styles']).'">'.$this->temp_html;
		file_put_contents($html_path,$html);
	}
 

	/**
     * Set the image temp result
     * @var object $temp
     * @return object
	 */
	private function setTemp($temp)
    {
		return $this->temp = $temp;
	}
 
	/**
     * Get the image temp result
     * 
     * @return object
	 */
	private function getImageTemp()
    {
		return $this->temp;
	}
 
	/**
     * Set the image result
     * @var object $image
     * @return object
	 */
	private function setImage($image)
    {
		return $this->im = $image;
	}
 
	/**
     * Get the image result
     * 
     * @return object
	 */
	private function getImage()
    {
		return $this->im;
	}

	/**
     * Set sprites array
     * 
     * @return array
	 */
	private function setSprite($sprites)
    {
		return $this->sprites = $sprites;
	}
	
	/**
     * Get sprites array
     * 
     * @return array
	 */
	private function getSprites()
    {
		return $this->sprites;
	}
		
	/**
     * Set css obfuscation
     * 
     * @return string
	 */
	public function setMinization($obs = true) {
		if($obs) $this->temp_min_sep = '';
		else $this->temp_min_sep = "\n";
	}
	
	/**
     * Set css init
     * 
     * @return string
	 */
	public function setCssInit($style, $class_prefix) {
		$this->class_prefix = empty($class_prefix) ? "" : $class_prefix.'-';
        $this->css_init = $style . $this->temp_min_sep;
	}
	
	/**
     * Set folder config array
     * 
     * @return array
	 */
	public function setFoldersConfig($config)
    {
		return $this->folders_config = $config;
	}
 
 }
