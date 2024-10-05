<?php
namespace Rtbr\Models; 

class Field {
    private $type;
    private $name;
    private $value;
    private $default;
    private $label;
    private $id;
    private $class;
    private $holderClass;
    private $description;
    private $options;
    private $option;
    private $attr;
    private $multiple;
    private $alignment;
    private $placeholder;

    function __construct() { }

    private function setArgument($attr) {
        $this->type = isset($attr['type']) ? ($attr['type'] ? sanitize_text_field( $attr['type'] ) : 'text') : 'text';
        $this->multiple = isset($attr['multiple']) ? ($attr['multiple'] ? sanitize_text_field( $attr['multiple'] ) : false) : false;
        $this->name = isset($attr['name']) ? ($attr['name'] ? sanitize_text_field( $attr['name'] ) : null) : null;

        $this->default = isset($attr['default']) ? ($attr['default'] ? $attr['default'] : null) : null;
        $this->value = isset($attr['value']) ? ($attr['value'] ? $attr['value'] : null) : null;

        if ( !$this->value ) {
            if ($this->multiple) {
                $v = get_post_meta(get_the_ID(), $this->name);
            } else {
                $v = get_post_meta(get_the_ID(), $this->name, true);
            }
            $this->value = ($v ? $v : $this->default);
        } 

        $this->label = isset($attr['label']) ? ($attr['label'] ? sanitize_text_field( $attr['label'] ) : null) : null;
        $this->id = isset($attr['id']) ? ($attr['id'] ? sanitize_text_field( $attr['id'] ) : null) : null;
        $this->class = isset($attr['class']) ? ($attr['class'] ? sanitize_text_field( $attr['class'] ) : null) : null;
        $this->holderClass = isset($attr['holderClass']) ? ($attr['holderClass'] ? sanitize_text_field( $attr['holderClass'] ) : null) : null;
        $this->placeholder = isset($attr['placeholder']) ? ($attr['placeholder'] ? sanitize_text_field( $attr['placeholder'] ) : null) : null;
        $this->description = isset($attr['description']) ? ($attr['description'] ? sanitize_text_field( $attr['description'] ) : null) : null;
        $this->options = isset($attr['options']) ? ($attr['options'] ? array_map( 'sanitize_text_field', $attr['options'] ) : array()) : array();
        $this->option = isset($attr['option']) ? ($attr['option'] ? sanitize_text_field( $attr['option'] ) : null) : null;
        $this->attr = isset($attr['attr']) ? ($attr['attr'] ? sanitize_text_field($attr['attr'] ) : null) : null;
        $this->alignment = isset($attr['alignment']) ? ($attr['alignment'] ? sanitize_text_field( $attr['alignment'] ) : null) : null;
        $this->class = $this->class ? sanitize_text_field( $this->class ) . " rt-form-control" : "rt-form-control";
    } 

    public function Field($attr) {
        $this->setArgument($attr);
        $holderId = $this->name . "_holder";
        $html = null;
        $html .= sprintf("<div class='rt-field-wrapper %s' id='%s'>", esc_attr($this->holderClass), esc_attr($holderId) );
        $html .= sprintf('<div class="rt-label">%s</div>',
            $this->label ? sprintf('<label for="">%s</label>', esc_html($this->label) ) : ''
        );
        $html .= "<div class='rt-field'>";
        switch ($this->type) {
            case 'text':
                $html .= $this->text();
                break;

            case 'url':
                $html .= $this->url();
                break;

            case 'number':
                $html .= $this->number();
                break;

            case 'select':
                $html .= $this->select();
                break; 

            case 'checkbox':
                $html .= $this->checkbox();
                break;

            case 'switch':
                $html .= $this->switch();
                break;

            case 'radio':
                $html .= $this->radioField();
                break;
            
            case 'style':
                $html .= $this->smartStyle();
                break; 
        }
        if ( $this->description ) {
            $html .= "<p class='description'>".esc_html( $this->description )."</p>";
        }
        $html .= "</div>"; // field
        $html .= "</div>"; // field holder

        return $html;
    }

    private function text() {
        $h = null;
        $h .= sprintf("<input
        type='text'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value), esc_attr($this->name), esc_attr($this->placeholder) );
        return $h;
    } 

    private function url() {
        $h = null; 
        $h .= sprintf("<input
        type='url'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_url($this->value), esc_attr($this->name), esc_attr($this->placeholder) );
        return $h;
    }

    private function number() {
        $h = null; 
        $h .= sprintf("<input
        type='number'
        class='%s'
        id='%s'
        value='%s'
        name='%s'
        placeholder='%s' 
        />", esc_attr($this->class), esc_attr($this->id), esc_attr($this->value), esc_attr($this->name), esc_attr($this->placeholder) );
        return $h;
    }

    private function select() {
        $h = null;
        if ($this->multiple) {
            $this->attr = " style='min-width:160px;'";
            $this->name = $this->name . "[]";
            $this->attr = $this->attr . " multiple='multiple'";
            $this->value = (is_array($this->value) && !empty($this->value) ? $this->value : array());
        } else {
            $this->value = array($this->value);
        }
 
        $h .= sprintf("<select name='%s' id='%s' class='%s' %s>", esc_attr($this->name), esc_attr($this->id), esc_attr($this->class), esc_html($this->attr) );
        if (is_array($this->options) && !empty($this->options)) {
            foreach ($this->options as $key => $value) {
                $slt = (in_array($key, $this->value) ? "selected" : null);
                $h .= sprintf("<option %s value='%s'>%s</option>", esc_attr($slt), esc_attr($key), esc_html($value) );
            }
        }
        $h .= "</select>";
        return $h;
    } 

    private function checkbox() {
        $h = null;
        if ($this->multiple) {
            $this->name = $this->name . "[]";
            $this->value = (is_array($this->value) && !empty($this->value) ? $this->value : array());
        }
        if ($this->multiple) {
            $h .= sprintf("<div class='checkbox-group %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id) );
            if (is_array($this->options) && !empty($this->options)) {
                foreach ($this->options as $key => $value) {
                    $checked = (in_array($key, $this->value) ? "checked" : null); 
                    $h .= sprintf('<label for="%1$s-%2$s">
                        <input type="checkbox" id="%1$s-%2$s" %3$s name="%4$s" value="%2$s">%5$s
                        </label>', 
                        esc_attr($this->id), 
                        esc_attr($key), 
                        esc_attr($checked), 
                        esc_attr($this->name), 
                        esc_html($value) );
                }
            }
            $h .= "</div>";
        } else {
            $checked = ($this->value ? "checked" : null);  
            $h .= sprintf("<label><input type='checkbox' %s id='%s' name='%s' value='1' />%s</label>", 
                        esc_attr($checked), 
                        esc_attr($this->id), 
                        esc_attr($this->name),  
                        esc_html($this->option) );
        }
        return $h;
    }

    private function switch() {
        $h = null;        
        $checked = ($this->value ? "checked" : null);  
        
        $h .= sprintf("<label class='rtbr-switch'><input type='checkbox' %s id='%s' name='%s' value='1' /><span class='rtbr-switch-slider round'></span></label>",  
                    esc_attr($checked), 
                    esc_attr($this->id), 
                    esc_attr($this->name),  
                    esc_html($this->option) ); 
        return $h;
    }

    private function radioField() {
        $h = null;
        $h .= sprintf("<div class='radio-group %s' id='%s'>", esc_attr($this->alignment), esc_attr($this->id));
        if (is_array($this->options) && !empty($this->options)) {
            foreach ($this->options as $key => $value) {
                $checked = ($key == $this->value ? "checked" : null);
                $h .= sprintf('<label for="%1$s-%2$s">
                <input type="radio" id="%1$s-%2$s" %3$s name="%4$s" value="%2$s">%5$s
                </label>', 
                esc_attr($this->id), 
                esc_attr($key), 
                esc_attr($checked), 
                esc_attr($this->name), 
                esc_html($value) );
            }
        }
        $h .= "</div>";
        return $h;
    }

    private function smartStyle() {  
        
        $h       = null;
        $sColor  = ! empty( $this->value['color'] ) ? $this->value['color'] : null;
        $sSize   = ! empty( $this->value['size'] ) ? $this->value['size'] : null;
        $sWeight = ! empty( $this->value['weight'] ) ? $this->value['weight'] : null;
        $sAlign  = ! empty( $this->value['align'] ) ? $this->value['align'] : null;
        $h       .= "<div class='rt-multiple-field-container'>";
        // color
        $h .= "<div class='rt-inner-field rt-col-4'>";
        $h .= "<div class='rt-inner-field-container size'>";
        $h .= "<span class='label'>".esc_html__( 'Color', 'business-reviews-wp' )."</span>";
        $h .= "<input type='text' value='" . esc_attr( $sColor ) . "' class='rt-color' name='".esc_attr($this->name)."[color]'>";
        $h .= "</div>";
        $h .= "</div>";

        // Font size
        $h      .= "<div class='rt-inner-field rt-col-4'>";
        $h      .= "<div class='rt-inner-field-container size'>";
        $h      .= "<span class='label'>".esc_html__( 'Font size', 'business-reviews-wp' )."</span>";
        $h      .= "<select name='".esc_attr($this->name)."[size]' class='rt-select2'>";
        $fSizes = $this->fontSize();
        $h      .= "<option value=''>".esc_html__( 'Default', 'business-reviews-wp' )."</option>";
        foreach ( $fSizes as $size => $label ) {
            $sSlt = ( $size == $sSize ? "selected" : null );
            $h    .= sprintf( "<option value='%s' %s>%s</option>", esc_attr($size), esc_attr($sSlt), esc_html($label) );
        }
        $h .= "</select>";
        $h .= "</div>";
        $h .= "</div>";

        // Weight
        $h       .= "<div class='rt-inner-field rt-col-4'>";
        $h       .= "<div class='rt-inner-field-container weight'>";
        $h       .= "<span class='label'>".esc_html__( 'Weight', 'business-reviews-wp' )."</span>";
        $h       .= "<select name='".esc_attr($this->name)."[weight]' class='rt-select2'>";
        $h       .= "<option value=''>".esc_html__( 'Default', 'business-reviews-wp' )."</option>";
        $weights = $this->textWeight();
        foreach ( $weights as $weight => $label ) {
            $wSlt = ( $weight == $sWeight ? "selected" : null ); 
            $h    .= sprintf( "<option value='%s' %s>%s</option>", esc_attr($weight), esc_attr($wSlt), esc_html($label) );
        }
        $h .= "</select>";
        $h .= "</div>";
        $h .= "</div>";

        // Alignment
        $h      .= "<div class='rt-inner-field rt-col-4'>";
        $h      .= "<div class='rt-inner-field-container alignment'>";
        $h      .= "<span class='label'>".esc_html__( 'Alignment', 'business-reviews-wp' )."</span>";
        $h      .= "<select name='".esc_attr($this->name)."[align]' class='rt-select2'>";
        $h      .= "<option value=''>".esc_html__( 'Default', 'business-reviews-wp' )."</option>";
        $aligns = $this->alignment();
        foreach ( $aligns as $align => $label ) {
            $aSlt = ( $align == $sAlign ? "selected" : null ); 
            $h    .= sprintf( "<option value='%s' %s>%s</option>", esc_attr($align), esc_attr($aSlt), esc_html($label) );
        }
        $h .= "</select>";
        $h .= "</div>";
        $h .= "</div>";
        $h .= "</div>";

        return $h;
    }

    private function fontSize() {
        $num = array();
        for ( $i = 10; $i <= 60; $i ++ ) {
            $num[ $i ] = $i . "px";
        }

        return $num;
    }

    private function alignment() {
        return array(
            'left'    => esc_html__( "Left", "business-reviews-wp" ),
            'right'   => esc_html__( "Right", "business-reviews-wp" ),
            'center'  => esc_html__( "Center", "business-reviews-wp" ),
            'justify' => esc_html__( "Justify", "business-reviews-wp" )
        );
    } 

    private function textWeight() {
        return array(
            'normal'  => esc_html__( "Normal", "business-reviews-wp" ),
            'bold'    => esc_html__( "Bold", "business-reviews-wp" ),
            'bolder'  => esc_html__( "Bolder", "business-reviews-wp" ),
            'lighter' => esc_html__( "Lighter", "business-reviews-wp" ),
            'inherit' => esc_html__( "Inherit", "business-reviews-wp" ),
            'initial' => esc_html__( "Initial", "business-reviews-wp" ),
            'unset'   => esc_html__( "Unset", "business-reviews-wp" ),
            100       => '100',
            200       => '200',
            300       => '300',
            400       => '400',
            500       => '500',
            600       => '600',
            700       => '700',
            800       => '800',
            900       => '900',
        );
    } 
}