<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 05/03/2017
 * Time: 00:20
 */

namespace Drupal\transmission;

use Drupal\file\Entity\File;

/**
 * Class Command
 * @package Drupal\transmission
 */
class Command {
    private $login = 'XXXXX';
    private $password = 'XXXXX';

    private $command = 'transmission-remote';

    public function __construct() {

    }

    /**
     * List all torrents.
     *
     * @param $output the output of the command.
     *  Array indexed by
     *      - header
     *      - items
     *      - footer
     * @see Drupal\transmission\Parser
     *
     * @return int Return status of the executed command.
     */
    public function listCurrents(&$output) {
        $command_output = '';
        $status = $this->execute('-l', $command_output);

        $parser = \Drupal::service('transmission.parser');
        $output = $parser->parseList($command_output);

        return $status;
    }

    /**
     * Add a torrent by its file.
     *
     * @param File $file the torrent file
     * @param $output
     * @return int
     */
    public function addTorrent(File $file) {
        $status = 1;
        if ($file && $path = \Drupal::service('file_system')->realpath($file->getFileUri())) {
            $output = array();
            $status = $this->execute("--add $path", $output);
            $parser = \Drupal::service('transmission.parser');
            $status &= $parser->parseBoolean($output);
        }

        return $status;
    }

    /**
     * Start an existing torrent added with addTorrent
     *
     * @param $id The torrent ID.
     * @return int
     */
    public function startTorrent($id) {
        $output = array();
        $status = $this->execute("-t $id --start", $output);
        $parser = \Drupal::service('transmission.parser');
        $status &= $parser->parseBoolean($output);
        return $status;
    }

    /**
     * Delete an existing torrent added with addTorrent
     *
     * @param $id The torrent ID.
     * @return int
     */
    public function deleteTorrent($id) {
        $output = array();
        $status = $this->execute("-t $id --remove-and-delete", $output);
        $parser = \Drupal::service('transmission.parser');
        $status &= $parser->parseBoolean($output);
        return $status;
    }

    /**
     * Stop an existing torrent added with addTorrent
     *
     * @param $id The torrent ID.
     * @return int
     */
    public function stopTorrent($id) {
        $output = array();
        $status = $this->execute("-t $id --stop", $output);
        $parser = \Drupal::service('transmission.parser');
        $status &= $parser->parseBoolean($output);
        return $status;
    }

    /**
     * Execute a command for transmission
     *
     * @param string $command the command to execute
     * @param array $output will be filled with every line of output from the command.
     *
     * @return int Return status of the executed command.
     */
    private function execute($command, &$output = array()) {
        $exec = "$this->command -n '$this->login:$this->password' $command";
        $return_status = 1;
        exec($exec, $output, $return_status);

        return $return_status;
    }
}