<?php

namespace src\mietshop\utils;

interface IColorUtility
{
    public function isHexRGBCode(string $hexCode): bool;

    public function isHexRGBACode(string $hexCode): bool;

    public function invert(string $hexCode): string;

    public function getRed(string $hexCode): int;

    public function getGreen(string $hexCode): int;

    public function getBlue(string $hexCode): int;

    public function isDark(string $hexCode) : bool;
}