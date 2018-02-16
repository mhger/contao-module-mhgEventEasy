<?php
/**
 * Contao 3 Extension [mhgEventsEasy]
 *
 * Copyright (c) 2018 Medienhaus Gersöne UG (haftungsbeschränkt) | Pierre Gersöne
 *
 * @package     mhgEventsEasy
 * @author      Pierre Gersöne <mail@medienhaus-gersoene.de>
 * @link        https://www.medienhaus-gersoene.de Medienhaus Gersöne - Agentur für Neue Medien: Web, Design & Marketing
 * @license     LGPL-3.0+
 */
/**
 * Register the classes
 */
ClassLoader::addClasses(array(
    // Classes
    'mhg\EventsEasy' => 'system/modules/mhgEventsEasy/classes/EventsEasy.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array(
    // Backend
    'be_eventseasy' => 'system/modules/mhgEventsEasy/templates/backend',
));
