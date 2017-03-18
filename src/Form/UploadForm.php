<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 05/03/2017
 * Time: 11:46
 */

namespace Drupal\transmission\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class UploadForm
 * @package Drupal\transmission\Form
 */
class UploadForm extends FormBase {

    /**
     * @return string
     */
    public function getFormId() {
        return 'transmission_upload_form';
    }

    /**
     * @param array $form
     * @param FormStateInterface $form_state
     * @return array
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['torrent_file'] = array(
            '#type' => 'file',
            '#title' => $this->t('Torrent file'),
            '#attributes' => array('class' => array('toto'))
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Start this torrent'),
            '#button_type' => 'primary',
        );
        return $form;
    }

    /**
     * @param array $form
     * @param FormStateInterface $form_state
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        $file = file_save_upload(
            'torrent_file',
            array('file_validate_extensions' => array('torrent'),
            FALSE, 0, FILE_EXISTS_REPLACE)
        );

        if ($file) {
            if (is_array($file)) {
                $file = $file[0];
            }
            if ($file = file_move($file, 'public://')) {
                $form_state->setStorage(array($file));
            }
            else {
                $form_state->setError($form['torrent_file'], $this->t("Erreur d'écriture."));
            }
        }
    }

    /**
     * @param array $form
     * @param FormStateInterface $form_state
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $file = $form_state->getStorage();
        if ($file) {
            if (is_array($file)) {
                $file = $file[0];
            }
            $commander = \Drupal::service('transmission.command');
            $return_status = $commander->addTorrent($file);

            if ($return_status === 1) {
                drupal_set_message(t('An error occured'));
            }
            else {
                drupal_set_message(t('Torrent started'));
            }

            return new \Symfony\Component\HttpFoundation\RedirectResponse(\Drupal\Core\Url::fromRoute('transmission.list')->toString());
        }
    }
}