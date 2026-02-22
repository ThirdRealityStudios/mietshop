<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/lib/php/limits/index.php';
	
	function convertImage($originalImage, $outputImage, $quality)
	{
		// jpg, png, gif or bmp?
		$exploded = explode('.',$originalImage);
		$ext = $exploded[count($exploded) - 1]; 

		if (preg_match('/jpg|jpeg/i',$ext))
			$imageTmp=imagecreatefromjpeg($originalImage);
		else if (preg_match('/png/i',$ext))
			$imageTmp=imagecreatefrompng($originalImage);
		else if (preg_match('/gif/i',$ext))
			$imageTmp=imagecreatefromgif($originalImage);
		else if (preg_match('/bmp/i',$ext))
			$imageTmp=imagecreatefrombmp($originalImage);
		else
			return false;

		// quality is a value from 0 (worst) to 100 (best)
		imagejpeg($imageTmp, $outputImage, $quality);
		imagedestroy($imageTmp);

		return true;
	}
	
	function getFileExtension($filepath)
	{
		// The path to the file with extension.
		$fileBasename = basename($filepath);
		
		$explodedFilepath = explode('.', $fileBasename); // Creates an array of n strings.
		$fileExtensionUnchanged = end($explodedFilepath); // Get the last string (extension).
		
		// Changed file extension with lower case modification (for further processing).
		$fileExtensionLowerCase = mb_strtolower($fileExtensionUnchanged);
		
		return $fileExtensionLowerCase;
	}
?>