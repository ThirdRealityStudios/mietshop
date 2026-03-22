<?php

namespace src\mietshop\contracts;

interface IImageModel
{
    public function upload(string $localPath, string $finalFileName, array $uploadedFiles, string $postName) : bool;
}