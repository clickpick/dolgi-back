<?php

namespace App\Services;

class VkButton {

    protected const PRIMARY = 'primary';
    protected const SECONDARY = 'secondary';
    protected const NEGATIVE = 'negative';
    protected const POSITIVE = 'positive';

    protected $type;
    protected $payload;

    protected $color = self::PRIMARY;

    public function toArray() {
        $attributes = get_object_vars($this);
        unset($attributes['color']);


        $result = [
            'action' => $attributes
        ];

        if ($this instanceof VkTextButton) {
            $result['color'] = $this->color;
        }

        return $result;
    }

    public function setPrimaryColor() {
        $this->color = self::PRIMARY;
    }

    public function setSecondaryColor() {
        $this->color = self::SECONDARY;
    }

    public function setNegativeColor() {
        $this->color = self::NEGATIVE;
    }

    public function setPositiveColor() {
        $this->color = self::POSITIVE;
    }
}
