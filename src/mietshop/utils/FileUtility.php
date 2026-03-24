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

    public function getFilename(string $filepath) : string
    {
        return basename($filepath);
    }

    public function getDirectoryPath(string $filepath) : string
    {
        $filename = $this->getFilename($filepath);

        if(str_ends_with($filepath, $filename))
        {
            $offset = strlen($filepath) - strlen($filename);

            $replaced = substr_replace($filepath, "", $offset, strlen($filename));

            return is_string($replaced) ? $replaced : $filepath;
        }
        else
        {
            return $filepath; // It is a directory path yet.
        }
    }
}