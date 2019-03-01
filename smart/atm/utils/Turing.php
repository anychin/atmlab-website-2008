<?php
class Utils_Turing{
	
	private static $instance = null;
	
	private $alphabet = "0123456789abcdefghijklmnopqrstuvwxyz";
	
	private $allowedSymbols = "23456789abcdeghkmnpqsuvxyz";
	
	private $fontFile = 'library/turing/fonts/cambria.png';
	
	private $length; 
	
	private $width;
	
	private $height;
	
	private $fluctuationAmplitude = 0.15;
	
	private $noSpaces = false;
	
	private $foregroundColor; 
	
	private $backgroundColor; 
	
	private $jpegQuality = 100;
	
	private $generatedString = "";
	
	private $mime = "jpg";
	
	public static function generate($width = 90, $height = 40, $fontFile = 'library/turing/fonts/antiqua.png', $jpegQuality = 100, $mime = "jpg"){
		 if(self::$instance == null)
		 	self::$instance = new Turing($width, $height, array(204, 0, 0), array(255, 255, 255), $fontFile, $jpegQuality, $mime);
		 return self::$instance->doAll();
	}
	
	private function __construct($width, $height, $background = array(230, 230, 229), $foreground = array(95, 95, 95), $fontFile, $jpegQuality, $mime){
		$this->length = 4;
		$this->width = $width;
		$this->height = $height;
		$this->foregroundColor = $foreground;
		$this->backgroundColor = $background;
		$this->fontFile = $fontFile;
		$this->jpegQuality = $jpegQuality;
		$this->mime = $mime;
	}
	
	private function generateString(){
		$this->generatedString = "";
		for($i=0; $i< $this->length; $i++)
			$this->generatedString .= $this->allowedSymbols{mt_rand(0,strlen($this->allowedSymbols)-1)};
	}
	
	private function doAll(){
		$alphabetLength=strlen($this->alphabet);
		$this->generateString();
		$font=imagecreatefrompng($this->fontFile);
		imagealphablending($font, true);
		$fontWidth=imagesx($font);
		$fontHeight=imagesy($font)-1;
		$fontMetrics=array();
		$symbol=0;
		$readingSymbol=false;
		for($i=0; $i < $fontWidth && $symbol < $alphabetLength; $i++)
		{
			$transparent = (imagecolorat($font, $i, 0) >> 24) == 127;
			if(!$readingSymbol && !$transparent)
			{
				$fontMetrics[$this->alphabet{$symbol}]= array('start'=>$i);
				$readingSymbol=true;
				continue;
			}
			if($readingSymbol && $transparent)
			{
				$fontMetrics[$this->alphabet{$symbol}]['end']=$i;
				$readingSymbol=false;
				$symbol++;
				continue;
			}
		}
		$img=imagecreatetruecolor($this->width, $this->height);
		imagealphablending($img, true);
		$white=imagecolorallocate($img, 255, 255, 255);
		imagefilledrectangle($img, 0, 0, $this->width-1, $this->height-1, $white);
		$x = 1;
		for($i=0; $i < $this->length; $i++)
		{
			$m=$fontMetrics[$this->generatedString{$i}];
			
			$y=mt_rand(-$this->fluctuationAmplitude, $this->fluctuationAmplitude)+($this->height-$fontHeight) / 2 + 2;
			if($this->noSpaces)
			{
				$shift=0;
				if($i > 0)
				{
					$shift=10000;
					for($sy = 7; $sy < $fontHeight - 20; $sy += 1)
					{
						for($sx = $m['start'] - 1; $sx < $m['end']; $sx += 1)
						{
			        		$rgb=imagecolorat($font, $sx, $sy);
			        		$opacity=$rgb>>24;
							if($opacity<127)
							{
								$left = $sx - $m['start'] + $x;
								$py = $sy + $y;
								if($py > $this->height)
									break;
								for($px = min($left, $this->width - 1); $px > $left-12 && $px >= 0; $px -= 1)
								{
					        		$color=imagecolorat($img, $px, $py) & 0xff;
									if($color+$opacity < 190)
									{
										if($shift > $left - $px)
											$shift = $left - $px;
										break;
									}
								}
								break;
							}
						}
					}
					if($shift == 10000)
						$shift=mt_rand(4,6);
				}
			}
			else
				$shift=1;
			imagecopy($img, $font, $x - $shift, $y, $m['start'], 1, $m['end']-$m['start'], $fontHeight);
			$x+=$m['end']-$m['start']-$shift;
		}
		$center=$x/2;
		
		$img2=imagecreatetruecolor($this->width, $this->height);
		$background=imagecolorallocate($img2, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
		imagefilledrectangle($img2, 0, 0, $this->width-1, $this->height-1, $background);		
		
		$randoms = $this->getRandoms();
		for($x = 0;$x < $this->width; $x++)
		{
			for($y = 0; $y < $this->height; $y++)
			{
				
				$sx = $x + (sin($x * $randoms['periods'][0] + $randoms['phases'][0]) + sin($y * $randoms['periods'][2] + $randoms['phases'][1])) * $randoms['amplitudes'][0] - $this->width / 2 + $center + 1;
				$sy = $y + (sin($x * $randoms['periods'][1] + $randoms['phases'][2]) + sin($y * $randoms['periods'][3] + $randoms['phases'][3])) * $randoms['amplitudes'][1];
				if($sx < 0 || $sy < 0 || $sx >= $this->width-1 || $sy >= $this->height - 1)
					continue;
				
				$color=imagecolorat($img, $sx, $sy) & 0xFF;
				$color_x=imagecolorat($img, $sx+1, $sy) & 0xFF;
				$color_y=imagecolorat($img, $sx, $sy+1) & 0xFF;
				$color_xy=imagecolorat($img, $sx+1, $sy+1) & 0xFF;
				if($color==255 && $color_x==255 && $color_y==255 && $color_xy==255)
					continue;
				
				if($color==0 && $color_x==0 && $color_y==0 && $color_xy==0){
					$newred = $this->foregroundColor[0];
					$newgreen=$this->foregroundColor[1];
					$newblue=$this->foregroundColor[2];
				}else{
					$frsx=$sx-floor($sx);
					$frsy=$sy-floor($sy);
					$frsx1=1-$frsx;
					$frsy1=1-$frsy;
					$newcolor=(
						$color*$frsx1*$frsy1+
						$color_x*$frsx*$frsy1+
						$color_y*$frsx1*$frsy+
						$color_xy*$frsx*$frsy);
					if($newcolor>255)
						$newcolor=255;
					$newcolor /= 255;
					$newcolor0 = 1 - $newcolor;

					$newred=$newcolor0*$this->foregroundColor[0]+$newcolor*$this->backgroundColor[0];
					$newgreen=$newcolor0*$this->foregroundColor[1]+$newcolor*$this->backgroundColor[1];
					$newblue=$newcolor0*$this->foregroundColor[2]+$newcolor*$this->backgroundColor[2];
				}

				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
			}
		}
		self::setHeaders();
			
		if($this->mime == "jpg"){
			imagejpeg($img2, null, $this->jpegQuality);
		}
		if($this->mime == "jpg"){
			imagejpeg($img2, null, $this->jpegQuality);
		}
		else if($this->mime == "gif"){
			imagegif($img2);
		}else if($this->mime == "png"){
			imagepng($img2);
		}
		return $this->generatedString;
	}
	
	public static function setHeaders(){
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		header('Cache-Control: no-store, no-cache, must-revalidate'); 
		header('Cache-Control: post-check=0, pre-check=0', FALSE); 
		header('Pragma: no-cache');
		if(self::$instance == null)
			return;
		if(self::$instance->mime == "jpg")
			header("Content-Type: image/jpeg");
		if(self::$instance->mime == "png")
			header("Content-Type: image/x-png");
		if(self::$instance->mime == "gif")
			header("Content-Type: image/gif");
	}
	
	private function getRandoms(){
		$result = array();
		$result[periods] = array(mt_rand(750000,1200000)/10000000, mt_rand(750000,1200000)/10000000, mt_rand(750000,1200000)/10000000, mt_rand(750000,1200000)/10000000);
		$result[phases] = array(mt_rand(0,31415926)/10000000, mt_rand(0,31415926)/10000000, mt_rand(0,31415926)/10000000, mt_rand(0,31415926)/10000000);
		$result[amplitudes] = array(mt_rand(330,420)/110, mt_rand(330,450)/110);
		return $result;
	}
}
?>