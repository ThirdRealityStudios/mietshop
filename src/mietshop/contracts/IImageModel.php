<?php

namespace src\mietshop\contracts;

interface IImageModel
{
    /**
     * @param string $localPath Should start with DIRECTORY_SEPARATOR (on Windows / or \ on Linux). The rest is application-dependent.
     * @param string $finalFileName
     * @param array $uploadedImage
     * @param string $postName
     * @return bool
     */
    public function uploadImageAsPNG(string $localPath, string $finalFileName, array $uploadedImage, string $postName) : bool;
}