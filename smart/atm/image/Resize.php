<?php
class Image_Resize
{

	public $width;

	public $height;

	public $format;

	public $logo;

	public $logoX;

	public $logoY;

	public $bg;

	public $crop;

	public $grey = false;

	public $enlarge = false;

	public $stream = false;

	public $blur = false;

	public function __construct ($width, $height, $format = Image_Format::ORIGINAL, $crop = false, $bg = false, $grey = false, $enlarge = false, $blur = false, $stream = false, $logo = null, $logoX = 0.5, $logoY = 0.5)
	{
		$this->width = $width;
		$this->height = $height;
		$this->format = $format;
		$this->logo = $logo;
		$this->logoX = $logoX;
		$this->logoY = $logoY;
		$this->bg = $bg;
		$this->crop = $crop;
		$this->blur = $blur;
		$this->grey = $grey;
		$this->enlarge = $enlarge;
		$this->stream = $stream;
	}
}
?>