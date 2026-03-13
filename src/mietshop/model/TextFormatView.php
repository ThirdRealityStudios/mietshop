<?php

namespace src\mietshop\model;

use src\mietshop\contracts\ITextFormatView;

class TextFormatView implements ITextFormatView
{
    public function formatEnumerations(string $text): string
    {
        /**
         * What happens here with the regex expression (PCRE format) is:
         *
         * #expression# will initiate the validation generally.
         * #expression#m will additionally limit the validation to every single line (no validation across lines possible - only single lines, using multiline mode, shortly m).
         * #^preciseExpression$#m will do what said above but also say: from the beginning to the end it must be strict what the preciseExpression says.
         * #^\*\s+(.*)$#m will say: every line directly has to start (note ^ at the beginning) with an (here correctly escaped) asterisk, followed by at least one whitespace character (note \s with + for at least one symbol).
         *                          (.*) will symbolize a Capturing Group (note the opening and closing curly braces which contain the expression that is extracted - and after that usable with the $1 placeholder in the replacement string parameter).
         *                          .* will symbolize that any symbol in any variable character length is accepted.
         */
        $possiblyFormattedText = preg_replace("#^\*\s+(.*)$#m", "• $1", $text);

        return is_string($possiblyFormattedText) ? $possiblyFormattedText : $text;
    }

    public function formatTextAreasSemiBold(string $text): string
    {
        /**
         * Note: [^mySymbols] will say "allow every symbol possible except those in the edgy brackets after the symbol ^".
         */
        $possiblyFormattedText = preg_replace("#\*\*([^\*]*)\*\*#", "<span class=\"fw-semibold lh-1 p-0 m-0 d-inline\">$1</span>", $text);

        return is_string($possiblyFormattedText) ? $possiblyFormattedText : $text;
    }

    public function formatLinesSemiBold(string $text): string
    {
        $possiblyFormattedText = preg_replace("#^\#\s*(.*)$#m", "<span class=\"fw-semibold lh-md p-0 m-0 d-inline-block\">$1</span>", $text);

        return is_string($possiblyFormattedText) ? $possiblyFormattedText : $text;
    }

    public function formatLinesBold(string $text): string
    {
        $possiblyFormattedText = preg_replace("#^\#\#\s*(.*)$#m", "<span class=\"fw-bold lh-md p-0 m-0 d-inline-block\">$1</span>", $text);

        return is_string($possiblyFormattedText) ? $possiblyFormattedText : $text;
    }

    public function formatTextAreasItalic(string $text): string
    {
        $possiblyFormattedText = preg_replace("#\*([^\*]*)\*#", "<span class=\"fst-italic lh-1 p-0 m-0 d-inline\">$1</span>", $text);

        return is_string($possiblyFormattedText) ? $possiblyFormattedText : $text;
    }

    public function formatAll(string $text): string
    {
        // The order is important here to avoid false formatting.
        // return $this->formatLinesBold($this->formatLinesSemiBold($this->formatEnumerations($this->formatTextAreasItalic($this->formatTextAreasSemiBold($text)))));
        return $this->formatTextAreasItalic($this->formatEnumerations($this->formatTextAreasSemiBold($this->formatLinesSemiBold($this->formatLinesBold($text)))));
    }
}