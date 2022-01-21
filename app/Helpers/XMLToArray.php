<?php

namespace App\Helpers;

use Exception;
use SimpleXMLElement;

/**
 * Class XMLToArray
 *
 * @package App\Helpers
 */
class XMLToArray
{
    /**
     * @param $resource
     *
     * @return array|void
     * @throws Exception
     */
    public static function convert($resource) : array
    {
        if (filter_var($resource, FILTER_VALIDATE_URL)) {
            $handle = curl_init($resource);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_exec($handle);
            $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $path   = $resource;
                $xml    = simplexml_load_file($path);
                $result = self::xmlToArray($xml);
            } else {
                throw new Exception("Failed Loading XML" . PHP_EOL . "The URL you provided does not point to an valid XML feed.");
            }
        } else {
            libxml_use_internal_errors(true);

            if (simplexml_load_string($resource)) {
                $xml    = simplexml_load_string($resource);
                $result = self::xmlToArray($xml);
            } else {
                $detail = [];
                foreach (libxml_get_errors() as $error) {
                    $detail[] = str_replace("\n", "", $error->message);
                }

                throw new Exception("Failed Loading XML" . PHP_EOL . implode(PHP_EOL, $detail));
            }
        }

        return $result;

    }

    /**
     * @param SimpleXMLElement $xml     XML code to convert to JSON
     * @param array            $options Options of the API to change JSON output
     *
     * @return array The JSON response
     */
    public static function xmlToArray(SimpleXMLElement $xml, $options = []) : array
    {
        $defaults       = [
            'namespaceRecursive' => false, // Get XML doc namespaces recursively
            'removeNamespace'    => false, // Remove namespace from resulting keys (recommend setting namespaceSeparator = '' when true)
            'namespaceSeparator' => ':', // Change separator to something other than a colon
            'attributePrefix'    => '@', // Distinguish between attributes and nodes with the same name
            'alwaysArray'        => [], // Array of XML tag names which should always become arrays
            'autoArray'          => true, // Create arrays for tags which appear more than once
            'textContent'        => '#text', // Key used for the text content of elements
            'autoText'           => true, // Skip textContent key if node has no attributes or child nodes
            'keySearch'          => false, // (Optional) search and replace on tag and attribute names
            'keyReplace'         => false, // (Optional) replace values for above search values
        ];
        $options        = array_merge($defaults, $options);
        $namespaces     = $xml->getDocNamespaces($options['namespaceRecursive']);
        $namespaces[''] = null; // Add empty base namespace

        // Get attributes from all namespaces
        $attributesArray = [];
        foreach ($namespaces as $prefix => $namespace) {
            if ($options['removeNamespace']) {
                $prefix = '';
            }
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                // (Optional) replace characters in attribute name
                if ($options['keySearch']) {
                    $attributeName = str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                }
                $attributeKey                   = $options['attributePrefix'] . ($prefix
                        ? $prefix . $options['namespaceSeparator'] : '') . $attributeName;
                $attributesArray[$attributeKey] = (string) $attribute;
            }
        }

        // Get child nodes from all namespaces
        $tagsArray = [];
        foreach ($namespaces as $prefix => $namespace) {
            if ($options['removeNamespace']) {
                $prefix = '';
            }

            foreach ($xml->children($namespace) as $childXml) {
                // Recurse into child nodes
                $childArray      = self::xmlToArray($childXml, $options);
                $childTagName    = key($childArray);
                $childProperties = current($childArray);

                // Replace characters in tag name
                if ($options['keySearch']) {
                    $childTagName = str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                }

                // Add namespace prefix, if any
                if ($prefix) {
                    $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
                }

                if (!isset($tagsArray[$childTagName])) {
                    // Only entry with this key
                    // Test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] = in_array($childTagName, $options['alwaysArray'], true) || !$options['autoArray']
                        ? [$childProperties] : $childProperties;
                } else {
                    if (is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName]) === range(0, count($tagsArray[$childTagName]) - 1)) {
                        // Key already exists and is integer indexed array
                        $tagsArray[$childTagName][] = $childProperties;
                    } else {
                        // Key exists so convert to integer indexed array with previous value in position 0
                        $tagsArray[$childTagName] = [$tagsArray[$childTagName], $childProperties];
                    }
                }
            }
        }

        // Get text content of node
        $textContentArray = [];
        $plainText        = trim((string) $xml);
        if ($plainText !== '') {
            $textContentArray[$options['textContent']] = $plainText;
        }

        // Stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || $plainText === ''
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        // Return node as array
        return [
            $xml->getName() => $propertiesArray,
        ];
    }

    /**
     * Check string is valid xml
     *
     * @param $xml
     *
     * @return bool
     */
    public static function isValidXML($xml) : bool
    {
        $doc = @simplexml_load_string($xml);

        return !!$doc;
    }

    /**
     * @param $content
     *
     * @return array|string[]
     * @throws Exception
     */
    public static function parseXml($content) : array
    {
        if (XMLToArray::isValidXML($content)) {
            return XMLToArray::convert($content);
        }

        if (XMLToArray::isJson($content)) {
            return json_decode($content, true);
        }

        return [];
    }

    public static function isJson($string) : bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
