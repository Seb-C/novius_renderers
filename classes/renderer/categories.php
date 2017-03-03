<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Novius\Renderers;

class Renderer_Categories extends \Nos\Renderer_Selector
{
    public $context = null;
    /**
     * Add a class and an id with a prefix to the renderer attributes
     * @param $attributes
     * @param $rules
     */

    public function before_construct(&$attributes, &$rules)
    {
        $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] : '').' category-selector';

        if (empty($attributes['id'])) {
            $attributes['id'] = uniqid('category_');
        }

        if (isset($attributes['renderer_options']['instance'])) {
            $this->context = $attributes['renderer_options']['instance']->get_context();
        }

        if (isset($attributes['renderer_options']) && isset($attributes['renderer_options']['parents'])) {
            $this->renderer_options['parents'] = $attributes['renderer_options']['parents'];
            unset($attributes['renderer_options']['parents']);
        }
    }

    public function build()
    {
        $options = $this->renderer_options;
        if (!empty($options) && isset($options['multiple'])&& $options['multiple']) {

            //it is necessary to construct the "selected values" array with keys written like "namespace\model|id"
            // because it must be considered as JS Object when transformed to json (see modeltree_checkbox)
            // and this is the syntax used in this renderer.
            $ids = (array) $this->value;
            $selected = array();
            $pre_selected = array();
            $disabled =  array();
            if (!empty($options) && isset($options['parents'])) {
                $pre_selected = $options['parents'];
                unset($options['parents']);
            }
            $model_name = $options['namespace'].'\\'.$options['class'];
            foreach ($ids as $id => $value) {
                $selected[$model_name.'|'.$id] = array(
                    'id' => $id,
                    'model' => $model_name,
                );
                if (in_array($id, $pre_selected)) {
                    $disabled[$model_name.'|'.$id] = array(
                        'id' => $id,
                        'model' => $model_name,
                    );
                }
            }
        } else {
            $id = $this->value;
            $selected = array('id'=>$id);
            $disabled = array();
        }

        $context = \Arr::get($options, 'context', $this->context);

        return $this->template(static::renderer(array(
            'input_name' => $this->name,
            'selected' => $selected,
            'disabled' => $disabled,
            'multiple' => isset($options['multiple']) ? $options['multiple'] : 0,
            'sortable' => isset($options['sortable']) ? $options['sortable'] : 0,
            'folder' => $options['folder'],
            'inspector_tree' => $options['inspector_tree'],
            'treeOptions' => array(
                'context' => $context == null ? '' : $context,
            ),
            'columns' => \Arr::get($options, 'columns', array(
                    array(
                        'dataKey' => 'title'
                    )
                )
            ),
            'height' => \Arr::get($options, 'height', '150px'),
            'width' => \Arr::get($options, 'width', null),
        )));
    }

    /**
     * Construct the radio selector renderer
     * When using a fieldset,
     * build() method should be overwritten to call the template() method on renderer() response
     * @static
     * @abstract
     * @param array $options
     */

    public static function renderer($options = array(), $attributes = array())
    {
        $view = 'inspector/modeltree_radio';
        $defaultSelected = null;
        if (isset($options['multiple']) && $options['multiple']) {
            $view = 'inspector/modeltree_checkbox';
            $defaultSelected = array();
        }

        $save_options = $options;

        $options = \Arr::merge(array(
            'urlJson' => 'admin/'.$options['folder'].'/'.$options['inspector_tree'].'/json',
            'input_name' => null,
            'selected' => $defaultSelected,
            'disabled' => array(
            ),
            'treeOptions' => array(
                'context' => null,
            ),
            'height' => '150px',
            'width' => null,
            'reset_default_column' => false, //Pour éviter de garder la column cate_titre par défaut, on permet de ne récupérer que les columns souhaitées
        ), $options);

        if (!isset($options['columns']) || count($options['columns']) == 0) {
            $options['columns'] = array(
                array(
                    'datakey' => 'title'
                )
            );
        }

        if ($options['reset_default_column']) {
            $options['columns'] = $save_options['columns'];
        }

        try {
            return \Request::forge('admin/'.$options['folder'].'/'.$options['inspector_tree'].'/list')->execute(
                array(
                    $view,
                    array(
                        'attributes' => $attributes,
                        'params' => $options,
                    )
                )
            );
        } catch (\HttpNotFoundException  $e) {
            return $e->response();
        }
    }
}
