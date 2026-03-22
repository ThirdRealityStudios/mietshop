<?php

namespace src\mietshop\model;

use src\mietshop\contracts\IImageModel;
use src\mietshop\utils\IFileUtility;

class ImageModel implements IImageModel
{
    private IFileUtility $fileUtility;

    public function __construct(IFileUtility $fileUtility)
    {
        $this->fileUtility = $fileUtility;
    }

    private function convertImage($originalImage, $outputImage, $quality)
    {
        $mimeType = mime_content_type($originalImage);

        if(!is_string($mimeType))
        {
            return false;
        }

        /*
        // images
        'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
        */

        if(strcmp($mimeType, "image/jpeg") === 0)
        {
            $imageTmp = imagecreatefromjpeg($originalImage);
        }
        else if(strcmp($mimeType, "image/png") === 0)
        {
            $imageTmp = imagecreatefrompng($originalImage);
        }
        else if(strcmp($mimeType, "image/gif") === 0)
        {
            $imageTmp = imagecreatefromgif($originalImage);
        }
        else if(strcmp($mimeType, "image/bmp") === 0)
        {
            $imageTmp = imagecreatefrombmp($originalImage);
        }
        else
        {
            return false;
        }

        if(is_bool($imageTmp))
        {
            return false;
        }

        if(!imagesavealpha($imageTmp, true))
        {
            return false;
        }

        // quality is a value from 0 (worst) to 100 (best)
        // imagejpeg($imageTmp, $outputImage, $quality);

        // quality is a value from 0 (best quality without compression) to 9 (worst but smallest)
        if(!imagepng($imageTmp, $outputImage, $quality))
        {
            return false;
        }

        if(!imagedestroy($imageTmp))
        {
            return false;
        }

        return true;
    }

    private function isUploadInformationAvailable(array $uploadedFiles, string $postName) : bool
    {
        if(!isset($uploadedFiles[$postName]))
        {
            return false;
        }

        if(!isset($uploadedFiles[$postName]['name']) || !isset($uploadedFiles[$postName]['tmp_name']) || !isset($uploadedFiles[$postName]['type']) ||
           !isset($uploadedFiles[$postName]['size']) || !isset($uploadedFiles[$postName]['error']))
        {
            return false;
        }

        return true;
    }

    public function upload(string $localPath, string $finalFileName, array $uploadedFiles, string $postName) : bool
    {
        // UPLOAD_ERR_OK         Value: 0
        // There is no error, the file uploaded with success.

        // UPLOAD_ERR_INI_SIZE   Value: 1
        // The uploaded file exceeds the upload_max_filesize directive in php.ini.

        // UPLOAD_ERR_FORM_SIZE  Value: 2
        // The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.

        // UPLOAD_ERR_PARTIAL    Value: 3
        // The uploaded file was only partially uploaded.

        // UPLOAD_ERR_NO_FILE    Value: 4
        // No file was uploaded.

        // UPLOAD_ERR_NO_TMP_DIR Value: 6
        // Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.

        // UPLOAD_ERR_CANT_WRITE Value: 7
        // Failed to write file to disk. Introduced in PHP 5.1.0.

        // UPLOAD_ERR_EXTENSION  Value: 8
        // A PHP extension stopped the file upload. Introduced in PHP 5.2.0.

        if(!$this->isUploadInformationAvailable($uploadedFiles, $postName))
        {
            return false;
        }

        $uploadedPicturesAmount = sizeof($uploadedFiles[$postName]['name']);

        // Sometimes there is a "file" stored in array,
        // although nothing was uploaded (array size is 1 then).
        // Anyway, it only contains information about the upload in some cases (metadata).
        // Hence, there is a need for check whether this is a real file or not.
        // The execution of the further upload code will continue
        // with the adjusted amount of photos in reality (= 0, as intended).
        if($uploadedPicturesAmount === 1 && $uploadedFiles[$postName]['error'][0] === UPLOAD_ERR_NO_FILE)
        {
            $uploadedPicturesAmount = 0; // in reality no file was uploaded (only metadata about the upload).
        }

        $pictureFolderAbsolutePath = $_SERVER['DOCUMENT_ROOT']."$localPath";

        $unprocessedFolderPath = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'unprocessed';

        // Recursively create the needed folder structure if not done yet,
        // meaning for the articles picture directory and its subdirectory "unprocessed".
        if(!is_dir($unprocessedFolderPath))
        {
            $unprocessedFolderCreationFailed = !mkdir($unprocessedFolderPath, 0777, true);

            if($unprocessedFolderCreationFailed)
            {
                // Creating "unprocessed" directory failed..
                return false;
            }
        }

        if($uploadedPicturesAmount === 0)
        {
            // No pictures were transmitted but pre-processing above was necessary
            // for picture folder creation.
            return true;
        }

        for($i = 0; $i < $uploadedPicturesAmount; $i++)
        {
            $fileName = $uploadedFiles[$postName]['name'][$i];
            $fileTmpName = $uploadedFiles[$postName]['tmp_name'][$i];
            $fileType = $uploadedFiles[$postName]['type'][$i];
            $fileSize = $uploadedFiles[$postName]['size'][$i];
            $uploadError = $uploadedFiles[$postName]['error'][$i];

            if($uploadError == UPLOAD_ERR_NO_FILE || $uploadError == UPLOAD_ERR_PARTIAL)
            {
                continue;
            }

            if(!($uploadError === UPLOAD_ERR_OK || $uploadError === UPLOAD_ERR_NO_FILE || $uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE || $uploadError === UPLOAD_ERR_NO_TMP_DIR || $uploadError === UPLOAD_ERR_CANT_WRITE || $uploadError === UPLOAD_ERR_EXTENSION))
            {
                // Creating directory failed..
                return false;
            }

            // $fileExtension = $this->fileUtility->getImageFileExtension($fileName);

            // Upload filepath where all unprocessed images are.
            $uploadFilepath = $unprocessedFolderPath.DIRECTORY_SEPARATOR.$finalFileName;

            if(!move_uploaded_file($fileTmpName, $uploadFilepath))
            {
                // Moving temporary file to server folder failed..
                return false;
            }

            // if-statement is true: the funtion convertImage(..) fails somehow to treat ALL uploaded files (only one)
            if(!$this->convertImage($uploadFilepath, "$pictureFolderAbsolutePath$finalFileName", 9))
            {
                // Converting the uploaded picture file to JPEG failed.
                return false;
            }

            // Delete original uploaded file.
            $deleteOriginalUploadFileSuccess = unlink($uploadFilepath);

            if(!$deleteOriginalUploadFileSuccess)
            {
                return false;
            }
        }

        $unprocessedFolderFiles = scandir($unprocessedFolderPath);

        // Check if there are still unprocessed picture files in "unprocessed" folder for whatever reason.
        // If scandir(..) determined, the given path does not represent a directory, it will return false.
        // Otherwise it returns the array of files inside.
        // So a check is needed to match that scenario too.
        if(is_array($unprocessedFolderFiles) && count($unprocessedFolderFiles) > 0)
        {
            // Delete all photo files inside folder "unprocessed".
            // $filename is the file name with file extension, e.g. "myphoto.jpg".
            // But directories can also be contained, so further checks are being made in for-loop.
            foreach($unprocessedFolderFiles as $filename)
            {
                $unprocessedPhotoFilepath = $unprocessedFolderPath.DIRECTORY_SEPARATOR.$filename;

                // Try to delete photo (and if unsuccessful, log the error into the log file).
                if(!is_dir($unprocessedPhotoFilepath) && !unlink($unprocessedPhotoFilepath))
                {
                    return false;
                }
            }
        }

        // Delete temporary folder again,
        // because all uploaded pictures have been normally procssed and deleted in that folder yet.
        // Bugfix: there was no check if the folder is indeed empty, so I added code before this if-statement for treatment.
        if(!rmdir($unprocessedFolderPath))
        {
            return false;
        }

        return true; // Upload succeeded of all pictures.
    }
}