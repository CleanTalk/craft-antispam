<?php

namespace Cleantalk\CraftAntispam\models;

use Cleantalk\Common\API;
use Craft;
use craft\helpers\UrlHelper;

class Settings extends \craft\base\Model
{
    public string $ctApiKey = '';
    public int $keyIsOk = 0;
    public string $errorMsg = '';

    public function rules(): array
    {
        return [
            [['ctApiKey'], 'required'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true) : bool
    {
        if ( $this->ctApiKey && preg_match('/^[a-z\d]{3,30}$/', $this->ctApiKey) ) {
            $npt_result = API::methodNoticePaidTill($this->ctApiKey, UrlHelper::baseUrl());
            if ( empty($npt_result['error']) ) {
                if ( $npt_result['valid'] && $npt_result['moderate']) {
                    $this->keyIsOk = 1;
                    $this->errorMsg = '';
                    return parent::validate($attributeNames, $clearErrors);
                }
            }
        }

        $this->keyIsOk = 0;
        $this->errorMsg = Craft::t('app', 'Api key is invalid or expired.');
        return false;
    }
}
