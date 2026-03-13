<?php

namespace src\mietshop\contracts;

interface ITextFormatView
{
    /**
     * Formats all enumerations in a text which begin with exactly one asterisk,
     * followed by one space symbol and the corresponding text.
     * E.g.
     *
     * \* My text to enumerate
     *
     * The asterisk will then be replaced by a nicer filled dot.
     *
     * @param string $text Text to format (plain is best to avoid false formatting)
     * @return string Formatted text (used HTML and Bootstrap). Otherwise, the given text if formatting failed for technical reasons.
     */
    public function formatEnumerations(string $text): string;

    /**
     * Formats all text appearances semi bold if the corresponding text is surrounded by exactly two asterisks at the beginning and end,
     * e.g.
     *
     * \*\*My text appearance to make semi bold\*\*
     *
     * @param string $text Text to format (plain is best to avoid false formatting)
     * @return string Formatted text (used HTML and Bootstrap). Otherwise, the given text if formatting failed for technical reasons.
     */
    public function formatTextAreasSemiBold(string $text): string;

    /**
     * Formats all lines semi bold if they begin with exactly two hashtags,
     * e.g.
     *
     * \#\# My line text
     *
     * @param string $text Text to format (plain is best to avoid false formatting)
     * @return string Formatted text (used HTML and Bootstrap). Otherwise, the given text if formatting failed for technical reasons.
     */
    public function formatLinesSemiBold(string $text): string;

    /**
     * Formats all lines bold if they begin with exactly one hashtag,
     * e.g.
     *
     * \# My line text
     *
     * @param string $text Text to format (plain is best to avoid false formatting)
     * @return string Formatted text (used HTML and Bootstrap). Otherwise, the given text if formatting failed for technical reasons.
     */
    public function formatLinesBold(string $text): string;

    /**
     * Formats all lines italic / cursively if the corresponding text is surrounded by exactly one asterisk form both sides,
     * e.g.
     *
     * \*My line text\*
     *
     * @param string $text Text to format (plain is best to avoid false formatting)
     * @return string Formatted text (used HTML and Bootstrap). Otherwise, the given text if formatting failed for technical reasons.
     */
    public function formatTextAreasItalic(string $text): string;

    /**
     * Uses all format functions to format the given text,
     * using HTML and Bootstrap.
     *
     * @param string $text Text to format (plain is best to avoid false formatting)
     * @return string Formatted text (used HTML and Bootstrap). Otherwise, the given text if formatting failed for technical reasons.
     */
    public function formatAll(string $text): string;
}