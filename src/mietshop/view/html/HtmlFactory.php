<?php

namespace src\mietshop\view\html;

class HtmlFactory implements IHtmlFactory
{
    /**
     * Generates the whole class string,
     * containing all necessary classes,
     * for the given element type (aka "color element", as defined in AppearanceModel by getApperanceData(...) )
     *
     * @param array $colorElementsCaseSensitive E.g. [ "Frame", "Window" ] or just [ "Text" ]
     * @param array $appearanceData
     * @param array $classes
     * @return string
     */
    private function genClasses(array $colorElementsCaseSensitive, array $appearanceData, array $classes) : string
    {
        $allClasses = "";

        for($i = 0; $i < sizeof($classes); $i++)
        {
            $class = $classes[$i];

            if($i < (sizeof($classes) - 1))
            {
                $allClasses .= "$class ";
            }
            else
            {
                $allClasses .= $class; // Last custom class to attach to the string
            }
        }

        if(empty($appearanceData))
        {
            return $allClasses;
        }

        foreach($colorElementsCaseSensitive as $colorElementCaseSensitive)
        {
            $savedBootstrapColorClass = $appearanceData["Color"][$colorElementCaseSensitive][0];

            if(!empty($savedBootstrapColorClass))
            {
                if(!empty($allClasses))
                {
                    $allClasses .= " $savedBootstrapColorClass";
                }
                else
                {
                    $allClasses = $savedBootstrapColorClass;
                }
            }
        }

        return $allClasses;
    }

    /**
     * @param array $colorElementsCaseSensitive E.g. [ "Frame", "Window" ] or just [ "Text" ]
     * @param array $appearanceData
     * @param array $styles Every single style (like "backgroud-color: red;") provided as elements in an array. Make sure every style is closed with a semicolon!
     * @return string
     */
    private function genStyles(array $colorElementsCaseSensitive, array $appearanceData, array $styles) : string
    {
        $allStyles = "";

        for($i = 0; $i < sizeof($styles); $i++)
        {
            $style = $styles[$i];

            if($i < (sizeof($styles) - 1))
            {
                $allStyles .= "$style ";
            }
            else
            {
                $allStyles .= $style; // Last custom style to attach to the string
            }
        }

        if(empty($appearanceData))
        {
            return $allStyles;
        }

        foreach($colorElementsCaseSensitive as $colorElementCaseSensitive)
        {
            $savedColorStyle = $appearanceData["Color"][$colorElementCaseSensitive][1];

            if(!empty($savedColorStyle))
            {
                if(!empty($allStyles))
                {
                    $allStyles .= " $savedColorStyle";
                }
                else
                {
                    $allStyles = $savedColorStyle;
                }
            }
        }

        return $allStyles;
    }

    public function genSelfClosingElement(string $tag, array $colorElementsCaseSensitive, array $appearanceData, array $styles, array $classes, string $optionalHref = "", string $optionalMoreTagInformation = "") : string
    {
        $allClasses = $this->genClasses($colorElementsCaseSensitive, $appearanceData, $classes);
        $possibleClassTagProperty = "";
        if(!empty($allClasses))
        {
            $possibleClassTagProperty = "class=\"$allClasses\"";
        }

        $allStyles = $this->genStyles($colorElementsCaseSensitive, $appearanceData, $styles);
        $possibleStyleTagProperty = "";
        if(!empty($allStyles))
        {
            $possibleStyleTagProperty = "style=\"$allStyles\"";
        }

        $optionalSecondWhitespace = (!empty($possibleStyleTagProperty) && (!empty($possibleClassTagProperty) || !empty($optionalHrefTagProperty))) ? " " : "";

        $optionalHrefTagProperty = empty($optionalHref) ? "" : "href=\"$optionalHref\"";
        $optionalThirdWhitespace = (!empty($possibleClassTagProperty) && $optionalHrefTagProperty) ? " " : "";

        $optionalFourthWhitespace = ((!empty($possibleStyleTagProperty) || !empty($possibleClassTagProperty) || !empty($optionalHrefTagProperty)) && !empty($optionalMoreTagInformation)) ? " " : "";

        $optionalFirstWhitespace = (!empty($possibleStyleTagProperty) || !empty($possibleClassTagProperty) || !empty($optionalHrefTagProperty) || !empty($optionalMoreTagInformation)) ? " " : "";

        return "<$tag$optionalFirstWhitespace$possibleStyleTagProperty$optionalSecondWhitespace$possibleClassTagProperty$optionalThirdWhitespace$optionalHrefTagProperty$optionalFourthWhitespace$optionalMoreTagInformation>";
    }

    public function genNonSelfClosingElement(string $tag, array $colorElementsCaseSensitive, array $appearanceData, array $styles, array $classes, string $content, string $optionalHref = "", string $optionalMoreTagInformation = "") : string
    {
        $preGeneratedElement = $this->genSelfClosingElement($tag, $colorElementsCaseSensitive, $appearanceData, $styles, $classes, $optionalHref, $optionalMoreTagInformation);

        return "$preGeneratedElement$content</$tag>";
    }

    public function genSpanText(array $appearanceData, string $text) : string
    {
        return $this->genNonSelfClosingElement("span", [ "Text" ], $appearanceData, [], [ "fs-6" ], $text);
    }

    public function genPText(array $appearanceData, string $text) : string
    {
        return $this->genNonSelfClosingElement("p", [ "Text" ], $appearanceData, [], [ "fs-6" ], $text);
    }

    public function genALink(array $appearanceData, string $text, string $href) : string
    {
        return $this->genNonSelfClosingElement("a", [ "Link" ], $appearanceData, [], [ "fs-6", "darker-interact" ], $text, $href);
    }

    public function genAButtonInteresting(array $appearanceData, string $text, string $href, array $classes = []) : string
    {
        array_push($classes, "btn", "fs-6");

        return $this->genNonSelfClosingElement("a", [ "Interesting" ], $appearanceData, [], $classes, $text, $href);
    }

    public function genAButtonUninteresting(array $appearanceData, string $text, string $href, array $classes = []) : string
    {
        array_push($classes, "btn", "fs-6");

        return $this->genNonSelfClosingElement("a", [ "Uninteresting" ], $appearanceData, [], $classes, $text, $href);
    }

    public function genLabelText(array $appearanceData, string $text, string $for) : string
    {
        return $this->genNonSelfClosingElement("label", [ "Text" ], $appearanceData, [], [ "form-label", "p-0" ], $text, "", "for=\"$for\"");
    }

    public function genHr(array $appearanceData) : string
    {
        return $this->genSelfClosingElement("hr", [ "Frame" ], $appearanceData, [ "border-style: dashed;" ], [ "w-100", "border-2" ]);
    }

    // TODO Replace $svgLocalPath through $svgXML (which was loaded or required - implement that on the other hand in the utils folder or something "AssetLoader"...)
    public function genTextSVG(string $svgLocalPath, string $color = "", array $classes = []) : string
    {
        // TODO View should not load something from the harddisk. Or move this function into a utility class (maybe with all other functions because it is slowly getting out of hands)
        $svgFileContent = file_get_contents($_SERVER['DOCUMENT_ROOT'].$svgLocalPath);

        if(is_bool($svgFileContent))
        {
            return "";
        }

        if(!empty($color))
        {
            // Replace color.
            $svgFileContent = str_replace("currentColor", $color, $svgFileContent);

            if(is_array($svgFileContent))
            {
                return "";
            }
        }

        $style = "style=\"width: auto; height: 1em;\"";

        $classesPrepared = "d-inline-block p-0 m-0 ";

        foreach($classes as $class)
        {
            $classesPrepared = "$class $classesPrepared";
        }

        // Modify class correctly if it exists yet as a property.
        if(str_contains($svgFileContent, "class=\""))
        {
            $svgFileContent = str_replace("class=\"", "$style class=\"$classesPrepared ", $svgFileContent);
        }
        else
        {
            $svgFileContent = str_replace("<svg ", "<svg $style class=\"$classesPrepared\" ", $svgFileContent);
        }

        if(is_array($svgFileContent))
        {
            return "";
        }
        else
        {
            return $svgFileContent;
        }
    }
}