<?php

namespace Cleantalk\CraftAntispam;

use Craft;
use craft\contactform\models\Submission;
use craft\contactform\events\SendEvent;
use craft\elements\Entry;
use craft\base\Element;
use craft\contactform\Mailer;
use craft\helpers\UrlHelper;
use yii\base\Event;

class CraftAntispam extends \craft\base\Plugin
{
    private string $apiKey;
    private int $keyIsOk;

    public function init()
    {
        parent::init();

        // Don't do anything if the api key is empty or invalid
        $this->apiKey = $this->getSettings()->ctApiKey;
        $this->keyIsOk = $this->getSettings()->keyIsOk;
        if ( ! $this->apiKey || ! $this->keyIsOk) {
            return;
        }

        $this->setComponents([
            'antispam' => \Cleantalk\CraftAntispam\services\CleantalkAntispamService::class
        ]);

        $this->registerEvents();

        if ($this->request->getIsSiteRequest()) {
            \Craft::$app->view->registerJsFile('https://moderate.cleantalk.org/ct-bot-detector-wrapper.js');
        }
    }

    protected function createSettingsModel(): \craft\base\Model
    {
        return new \Cleantalk\CraftAntispam\models\Settings();
    }

    protected function settingsHtml(): string
    {
        return \Craft::$app->getView()->renderTemplate(
            'craft-antispam/settings',
            [ 'settings' => $this->getSettings() ]
        );
    }

    private function registerEvents()
    {
        /**
         * Checking for spam
         * Integration for the Contact Form plugin
         */
        Event::on(
            Submission::class,
            Submission::EVENT_AFTER_VALIDATE,
            function(Event $e) {
                /** @var Submission $submission */
                $submission = $e->sender;

                $params['email'] = $submission->fromEmail;
                $params['message'] = $submission->message;
                $this->antispam->checkSpam($params);
            }
        );

        /**
         * Checking user custom forms for spam
         */
        Event::on(
            Entry::class,
            Element::EVENT_BEFORE_SAVE,
            function (Event $e) {
                $entry = $e->sender;
                // Replace "YOUR_FORM_FIELD" with the name of your "email" and "message" fields in the form.
                $params['email'] = $entry->YOUR_FORM_FIELD;
                $params['message'] = $entry->YOUR_FORM_FIELD;
                if ($this->request->getIsSiteRequest()) {
                    $this->antispam->checkSpam($params);
                }
            },
        );
    }

    public function afterSaveSettings(): void
    {
        parent::afterSaveSettings();
        $url = \craft\helpers\UrlHelper::cpUrl('settings/plugins/craft-antispam');
        try {
            Craft::$app->response
                ->redirect(UrlHelper::url($url))
                ->send();
        } catch (\yii\base\InvalidRouteException ) {
            return;
        }
    }
}
