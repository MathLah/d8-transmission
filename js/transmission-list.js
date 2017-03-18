(function ($, Drupal, drupalSettings) {
    'use strict';
    Drupal.behaviors.TransmissionList = {
        attach: function (context, settings) {
            Drupal.behaviors.TransmissionList.refresh();
            setInterval(function () {
                Drupal.behaviors.TransmissionList.refresh();
            }, 5000);
        },
        refresh: function(context, settings) {
            $.get('/transmission/listonly', function(data) {
                $('#torrents-list', context).html(data[0].data);
            })
        }
    };
}(jQuery, Drupal, drupalSettings));