<?php

namespace src\mietshop\utils;

use InvalidArgumentException;

class ColorUtility implements IColorUtility
{
    public function isHexRGBCode(string $hexCode): bool
    {
        return preg_match('#^\#[0-9A-Fa-f]{6}$#', $hexCode) === 1;
    }

    public function isHexRGBACode(string $hexCode): bool
    {
        return preg_match('#^\#[0-9A-Fa-f]{8}$#', $hexCode) === 1;
    }

    public function getRed(string $hexCode): int
    {
        if(!$this->isHexRGBACode($hexCode) && !$this->isHexRGBCode($hexCode))
        {
            return 0;
        }

        $rawHexCode = ltrim($hexCode, '#');

        return hexdec(substr($rawHexCode, 0, 2));
    }

    public function getGreen(string $hexCode): int
    {
        if(!$this->isHexRGBACode($hexCode) && !$this->isHexRGBCode($hexCode))
        {
            return 0;
        }

        $rawHexCode = ltrim($hexCode, '#');

        return hexdec(substr($rawHexCode, 2, 2));
    }

    public function getBlue(string $hexCode): int
    {
        if(!$this->isHexRGBACode($hexCode) && !$this->isHexRGBCode($hexCode))
        {
            return 0;
        }

        $rawHexCode = ltrim($hexCode, '#');

        return hexdec(substr($rawHexCode, 4, 2));
    }

    public function invert(string $hexCode): string
    {
        if(!$this->isHexRGBACode($hexCode) && !$this->isHexRGBCode($hexCode))
        {
            return $hexCode;
        }

        $rawHexCode = ltrim($hexCode, '#');

        $r = $this->getRed($rawHexCode);
        $g = $this->getGreen($rawHexCode);
        $b = $this->getBlue($rawHexCode);

        $r = 255 - $r;
        $g = 255 - $g;
        $b = 255 - $b;

        if($this->isHexRGBACode($hexCode))
        {
            $a = hexdec(substr($rawHexCode, 6, 2));
            return sprintf("#%02X%02X%02X%02X", $r, $g, $b, $a);
        }
        else
        {
            return sprintf("#%02X%02X%02X", $r, $g, $b);
        }
    }

    public function isDark(string $hexCode) : bool
    {
        if(!$this->isHexRGBACode($hexCode) && !$this->isHexRGBCode($hexCode))
        {
            return $hexCode;
        }

        $rawHexCode = ltrim($hexCode, '#');

        $r = $this->getRed($hexCode);
        $g = $this->getGreen($hexCode);
        $b = $this->getBlue($hexCode);

        return $r < 192 || $g < 192 || $b < 192;
    }
}