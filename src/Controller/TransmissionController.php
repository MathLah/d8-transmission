<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 04/03/2017
 * Time: 22:02
 */

namespace Drupal\transmission\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Class TransmissionController
 * @package Drupal\transmission\Controller
 */
class TransmissionController extends ControllerBase {

    /**
     * List Action
     *
     * @return array
     *  return a transmission_list themed filled with
     *      - transmission_form
     */
    public function list() {
        return array(
            '#theme' => 'transmission_list_page',
            '#attached' => array(
                'library'=> array('transmission/transmission-list')
            )
        );
    }
    /**
     * List Ajax Action
     *
     * @return array
     *  return a transmission_list themed filled with
     *      - transmission_element for the existing torrents.
     */
    public function listonly() {
        $output = NULL;
        $commander = \Drupal::service('transmission.command');
        $return_status = $commander->listCurrents($output);
        if ($return_status === 1) {
            return 'ERROR';
        }

        $data = array();
        for ($i = 0; $i < count($output['items']); $i++) {
            $o = $output['items'][$i];
            $d = array(
                '#theme' => 'transmission_element',
                '#header' => $output['header'],
                '#torrent_data' => $o
            );
            $data[] = render($d);
        }
        $render = array(
            '#theme' => 'transmission_list',
            '#data' => $data,
        );

        $response = new AjaxResponse();
        $response->addCommand(new HtmlCommand('body', $render));
        return $response;
    }

    /**
     * Start a torrent Action and redirect to list
     *
     * @param Request $request
     * @param $torrent_id int
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function start(Request $request, $torrent_id) {
        $commander = \Drupal::service('transmission.command');
        $return_status = $commander->startTorrent($torrent_id);
        if ($return_status === 1) {
            drupal_set_message(t('An error occured'));
        }
        else {
            drupal_set_message(t('Torrent started'));
        }

        return new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal\Core\Url::fromRoute('transmission.list')->toString());
    }

    /**
     * Stop a torrent Action and redirect to list
     *
     * @param Request $request
     * @param $torrent_id int
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function stop(Request $request, $torrent_id) {
        $commander = \Drupal::service('transmission.command');
        $return_status = $commander->stopTorrent($torrent_id);
        if ($return_status === 1) {
            drupal_set_message(t('An error occured'));
        }
        else {
            drupal_set_message(t('Torrent stoped'));
        }
        return new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal\Core\Url::fromRoute('transmission.list')->toString());
    }

    /**
     * Delete a torrent Action and redirect to list
     *
     * @param Request $request
     * @param $torrent_id int
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, $torrent_id) {
        $commander = \Drupal::service('transmission.command');
        $return_status = $commander->deleteTorrent($torrent_id);
        if ($return_status === 1) {
            drupal_set_message(t('An error occured'));
        }
        else {
            drupal_set_message(t('Torrent deleted'));
        }
        return new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal\Core\Url::fromRoute('transmission.list')->toString());
    }
}