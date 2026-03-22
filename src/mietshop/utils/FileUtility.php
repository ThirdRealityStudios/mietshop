<?php

namespace src\mietshop\utils;

class FileUtility implements IFileUtility
{
    public function getImageFileExtension($filepath) : string
    {
        // The path to the file with extension.
        $fileBasename = basename($filepath);

        $explodedFilepath = explode('.', $fileBasename); // Creates an array of n strings.
        $fileExtensionUnchanged = end($explodedFilepath); // Get the last string (extension).

        // Changed file extension with lower case modification (for further processing).
        $fileExtensionLowerCase = mb_strtolower($fileExtensionUnchanged);

        return $fileExtensionLowerCase;
    }
}