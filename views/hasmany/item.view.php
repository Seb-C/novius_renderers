<?php
/**
 * @var $fieldset Fieldset
 */
\Nos\I18n::current_dictionary('novius_renderers::default');
?>
<div class="hasmany_item" data-item-index="<?= $index ?>">
    <?= $fieldset->build_hidden_fields() ?>
    <div class="head ui-widget-header">
        <div class="left">
            <?php if (!isset($options['duplicate']) || $options['duplicate']): ?>
                <button class="dupli-item-js button-dupli-item" <?= array_to_attr(array(
                    'data-icon' => 'copy',
                    'data-model' => \Arr::get($options, 'model'),
                    'data-view' => \Arr::get($options, 'content_view'),
                    'data-order' => intval(\Arr::get($options, 'order', 0)),
                )) ?>><?= __('Duplicate this item') ?></button>
            <?php endif ?>
        </div>
        <div class="right">
            <?php if (!empty($options['order'])): ?>
                <button class="hasmany_button_move_down item-down-js" data-icon="triangle-1-s">&nbsp;</button>
                <button class="hasmany_button_move_up item-up-js" data-icon="triangle-1-n">&nbsp;</button>
            <?php endif ?>

            <?php if (!isset($options['delete']) || $options['delete']): ?>
                <button class="hasmany_button_delete ui-state-error item-delete-js" data-icon="trash" <?= array_to_attr(array(
                    'data-question' => __('Are you sure you want to delete this item?'),
                    'data-removed' => empty($is_new) ? __('This item will be deleted when the form is saved') : '',
                )) ?>>&nbsp;</button>
            <?php endif ?>
        </div>
    </div>
    <div class="hasmany_content">
        <?= \View::forge($options['content_view'], array('fields' => $fields), false) ?>
    </div>
    <?= $fieldset->build_append() ?>
</div>
