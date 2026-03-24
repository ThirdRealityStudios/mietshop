<?php

namespace src\mietshop\utils;

interface IFileUtility
{
    /**
     * Returns the images file extension,
     * using regular expressions.
     * A usual filepath is being expected.
     *
     * @param $filepath
     * @return string
     */
    public function getImageFileExtension($filepath) : string;

    public function getFilename(string $filepath) : string;

    public function getDirectoryPath(string $filepath) : string;
}