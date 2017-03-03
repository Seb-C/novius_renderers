<?php
\Nos\I18n::current_dictionary('novius_renderers::default');

// Gets the related items
$listItems = null;
if (!empty($relation) && !empty($item)) {
    $listItems = $item->{$relation};
} elseif (!empty($value)) {
    $listItems = array();
    foreach ($value as $elem) {
        $newModel = $model::forge();
        foreach ($elem as $property => $elemValue) {
            $newModel->$property = $elemValue;
        }
        $listItems[] = $newModel;
    }
}

// Sorts the related items if there is an order property
$orderProperty = \Arr::get($options, 'order_property');
if (!empty($orderProperty)) {
    uasort($listItems, function ($a, $b) use ($orderProperty) {
        return intval($a->{$orderProperty}) - intval($b->{$orderProperty});
    });
}

$defaultItem = \Arr::get($options, 'default_item', true);

?>
<div class="hasmany_items count-items-js" <?= array_to_attr(array(
    'data-nb-items' => empty($listItems) ? (int) $defaultItem : count($listItems),
    'data-order' => \Arr::get($options, 'order') ? 1 : 0,
    'data-order-property' => \Arr::get($options, 'order_property'),
)) ?>>
    <div class="item_list">
        <?php
        $i = 0;
        if (!empty($listItems)) {
            foreach ($listItems as $o) {
                // Build fieldset and return view
                echo \Novius\Renderers\Renderer_HasMany::render_fieldset($o, $relation, $i, $options, $data);
                $i++;
            }
        } elseif ($defaultItem) {
            // Display an empty item in case none have already been added
            echo \Novius\Renderers\Renderer_HasMany::render_fieldset($model::forge(), $relation, $i, $options, $data);
        }
        ?>
    </div>

    <?php
    $attr = array(
        'data-icon' => 'plus',
        'data-model' => $model,
        'data-context' => $context,
        'data-relation' => $relation,
        'data-view' => \Arr::get($options, 'content_view'),
        'data-order' => !empty($options['order']) ? 1 : 0,
        'data-duplicate' => !empty($options['duplicate']) ? 1 : 0,
    );

    if (\Arr::get($options, 'inherit_context', true)) {
        if (method_exists($item, 'behaviours') && ($item::behaviours('Nos\Orm_Behaviour_Contextable') || $item::behaviours('Nos\Orm_Behaviour_Twinnable'))) {
            $attr['data-context'] = $item->get_context();
        }
    }

    $attr = \Arr::merge($attr, $data);
    ?>
    <?php if (!isset($options['add']) || $options['add']): ?>
        <button class="add-item-js button-add-item" <?= array_to_attr($attr) ?>><?= __('Add one item') ?></button>
    <?php endif ?>
</div>
