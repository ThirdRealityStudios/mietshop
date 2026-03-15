<?php

namespace src\mietshop\contracts;

/**
 * https://getbootstrap.com/docs/5.3/customize/color/
 *
 *
 */
interface IAppearanceModel
{
    /**
     * Boostrap color names
     */
    public const ACCEPTED_COLORS = [ "black", "dark", "secondary", "light", "body", "white", "primary", "primary-subtle", "info", "danger", "success", "warning" ];

    public const ACCEPTED_ELEMENTS = [ "color" => [ "frame" => "Farbe für Rahmen", "window" => "Farbe für Fenster", "background" => "Farbe für Hintergrund", "text" => "Text-Farbe", "link" => "Link-Farbe", "interesting" => "Hauptfarbe", "uninteresting" => "Nebenfarbe", "actionExecute" => "Ausführen-Schaltfläche-Farbe", "actionAbort" => "Abbruch-Schaltfläche-Farbe" ], "image" => [ "logo" => "Firmenlogo" ] ];
}