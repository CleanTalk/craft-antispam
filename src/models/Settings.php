<?php

namespace Cleantalk\CraftAntispam\models;

class Settings extends \craft\base\Model
{
    public $ctApiKey = '';

    public function rules(): array
    {
        return [
            [['ctApiKey'], 'required'],
        ];
    }
}
