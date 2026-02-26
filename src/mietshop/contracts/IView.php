<?php

namespace src\mietshop\contracts;

interface IView
{
    /**
     * Displays the view.
     *
     * Expects an associative array containing all necessary data for rendering.
     * The exact structure of the array depends on the specific implementation.
     *
     * Security Guidelines (XSS / Code Injection Prevention)
     * -----------------------------------------------------
     *
     * When outputting data from the database or user input, always encode content
     * appropriately depending on the context:
     *
     * 1. HTML output (text inside tags):
     *
     *
     *      htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
     *
     *      // Converts special HTML characters to entities.
     *      // Protects against injection of HTML / JS in your page.
     *
     * 2. URL parameters / href attributes:
     *
     *
     *      urlencode($text)
     *
     *      // Encodes unsafe URL characters.
     *      // Prevents malicious alteration of <a href="..."> links.
     *
     * 3. JavaScript variables / JSON data:
     *
     *
     *      json_encode($text)
     *
     *      // Produces a safe JS string literal or JSON output.
     *      // Useful inside <script> tags or AJAX JSON responses.
     *
     * Cheat Prevention Examples:
     * --------------------------
     *
     * 1. HTML
     *
     *
     *      <h1><?php htmlspecialchars($title) ?></h1>
     *
     * 2. URL
     *
     *
     *      <a href="page.php?id=<?php urlencode($id) ?>">Link</a>
     *
     * 3. JS
     *
     *
     *      <script>let data = <?php json_encode($jsData) ?>;</script>
     *
     * This approach allows you to:
     *
     * - Save raw, unescaped content in the database.
     * - Prevent XSS attacks when rendering content in the view.
     * - Decide intentionally where code should be executed.
     *
     * For a different way of describing the possible security issues and more examples,
     * read the **documentation of the interface IDatabaseAdapter**.
     *
     * @param array $data Associative array of data to be used by the view (or leave out parameter => no data to show)
     * @return void
     */
    public function show(array $data = []) : void;
}