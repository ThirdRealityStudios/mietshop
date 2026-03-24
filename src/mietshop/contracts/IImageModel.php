<?php

namespace src\mietshop\contracts;

interface IImageModel
{
    /**
     * @param string $localPath Should start with DIRECTORY_SEPARATOR (on Windows / or \ on Linux). The rest is application-dependent.
     * @param string $finalFileName
     * @param array $uploadedImage Usually $_FILES
     * @param string $postName
     * @param int $maxBytesAccepted The maximum amount of bytes accepted to upload for an image.
     * @param int $heightPx The height in pixels to which the uploaded file should be scaled to (good to save disk space e.g.)
     * @return bool
     */
    public function uploadImageAsPNG(string $localPath, string $finalFileName, array $uploadedImage, string $postName, int $maxBytesAccepted, int $heightPx) : bool;
}