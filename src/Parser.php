<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 05/03/2017
 * Time: 00:20
 */

namespace Drupal\transmission;

/**
 * Class Parser
 * @package Drupal\transmission
 */
class Parser {
    public function __construct() {

    }

    /**
     * Parse a simple command to return a boolean
     *
     * @param $output The command output.
     *
     * @return bool TRUE if the command is a success.
     */
    public function parseBoolean($output) {
        if (strpos($output[0], '"success"') !== FALSE) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Parse a command output to get a list of torrent
     *
     * @param $output The command output.
     * @return array is indexed by
     *  (
     *      'header' => $header,
     *      'items' => $items,
     *      'footer' => $footer,
     *  )
     *
     * items are indexed arrays
     * (
     *   'id' => $id,
     *   'done' => $done,
     *   'have' => $have,
     *   'name' => $name,
     *   'upload' => $upload,
     *   'download' => $download,
     *   'ratio' => $ratio,
     *   'eta' => $eta,
     *   'status' => $status
     *   )
     */
    public function parseList($output) {
        $header = preg_split("/\s+/", $output[0]);
        $footer = preg_split("/\s+/", $output[count($output) - 1]);
        $items = array();
        for ($i = 1; $i < count($output) - 1; $i++) {
            $data = preg_split("/\s+/", $output[$i]);
            $index = 0;
            if (empty($data[0])) {
                $index = 1;
            }
            $id = $data[$index];
            $done = $data[$index + 1];
            $have = $data[$index + 2];

            if ($data[$index + 2] !== 'None') {
                $have .= $data[$index + 3];
                $index++;
            }

            $eta = $data[$index + 3];
            if (filter_var($data[$index + 4], FILTER_VALIDATE_FLOAT) === FALSE) {
                $eta .= $data[$index + 4];
                $index++;
            }
            $upload = $data[$index + 4];
            $download = $data[$index + 5];
            $ratio = $data[$index + 6];

            $status = $data[$index + 7];
            $name = '';
            $count = count($data);
            for ($j = $index + 8; $j < $count; $j++) {
                $name .=  $data[$j];
            }

            $items[] = array(
                'id' => $id,
                'done' => $done,
                'have' => $have,
                'name' => $name,
                'upload' => $upload,
                'download' => $download,
                'ratio' => $ratio,
                'eta' => $eta,
                'status' => $status
            );
        }
        
        return array(
            'header' => $header,
            'items' => $items,
            'footer' => $footer,
        );
    }
}