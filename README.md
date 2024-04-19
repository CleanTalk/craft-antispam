# Anti-Spam protection by CleanTalk plugin for Craft CMS

## Anti-Spam protection by CleanTalk Overview

Anti-Spam by CleanTalk is a cloud spam protection. Spam protection works invisible for visitors, and they do not need to prove that they are not bots.

Cloud capabilities allow you to view all processed requests in the anti-spam log and control the operation of the service.

Additional features of personal lists expand your options for protecting the site.

At the moment, protection is implemented only for spam protection for the Contact Form plugin.
https://plugins.craftcms.com/contact-form

If you need to protect another types of forms, please, look at the [Cloud Gray Pty Ltd](https://github.com/cloudgrayau/cleantalk/blob/craft4/README.md) solution, that let you use the CleanTalk service: https://plugins.craftcms.com/cleantalk?craft4

## Requirements

* Craft CMS 3.0.0 or later
* CleanTalk account https://cleantalk.org/register?product=anti-spam

## Installation

To install the plugin, follow these instructions.

```bash
composer require cleantalk/craft-antispam
craft plugin/install craft-antispam
```

## Configuring Anti-Spam protection by CleanTalk

To configure the plugin, just add the api-key to the plugin's setting located at /admin/settings/plugins/craft-antispam
