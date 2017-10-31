<?php
    \Nos\I18n::current_dictionary(array('novius_renderers::appdeskpicker'));
    $uniqid = uniqid('tabs_');
?>
<style type="text/css">
    #<?= $uniqid ?> {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        height: 100%;
    }
    #<?= $uniqid ?> > ul {
        width: 18%;
    }
    #<?= $uniqid ?> > div {
        width: 81%;
        height: 100%;
        position: relative;
    }
</style>
<div id="<?= $uniqid ?>">
    <ul>
        <?php foreach ($models as $index => $model): ?>
            <li>
                <a
                    href="#<?= $uniqid ?>-select-type-<?= $index ?>"
                    data-appdesk-url="<?= e(\Arr::get($model, 'appdesk')) ?>"
                    data-appdesk-model="<?= e(\Arr::get($model, 'model')) ?>"
                >
                    <?= \Arr::get($model, 'label', \Arr::get($model, 'model')) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
    <?php foreach ($models as $index => $model): ?>
        <div id="<?= $uniqid ?>-select-type-<?= $index ?>">
        </div>
    <?php endforeach ?>
</div>
<script type="text/javascript">
    require([
        'jquery-nos',
        'wijmo.wijtabs',
    ], function($) {
        var loadAppdesk = function ($wijtab, target) {
            var $tab = $(target.tab);
            if (!$tab.data('loaded')) {
                $(target.panel).load($tab.data('appdesk-url'), function (responseData) {
                    try {
                        var data = JSON.parse(responseData);
                        if (data.hasOwnProperty('error')) {
                            // An error happened, but it's glitchy and the error structure is inconsistent
                            // across all appdesks, so we have to output a generic error message...
                            $(target.panel).html(<?= json_encode(__('You are not allowed to access to this application.')) ?>);
                        }
                    } catch (e) {
                        // It's not a valid JSON, so we have nothing to do
                    }
                });

                $(target.panel).closest('.ui-dialog-content')
                    .bind('appdesk_pick_' + $tab.data('appdesk-model'), function(e, item) {
                        // On item selected, transmit the picked item to the renderer js through an event
                        $(target.panel).closest('.ui-dialog-content').trigger('appdeskpicker-item-picked', item);
                    });

                $tab.data('loaded', true);
            }
        };

        $(function() {
            var $container = $('#<?= $uniqid ?>');

            // Initializing wijtabs
            $container.wijtabs({
                alignment: 'left',
                select: loadAppdesk,
            }).end().nosFormUI();

            // Loading first appdesk
            loadAppdesk(null, {
                tab: $('a[href="#<?= $uniqid ?>-select-type-0"]'),
                panel: $('#<?= $uniqid ?>-select-type-0'),
            });
        });
    });
</script>

