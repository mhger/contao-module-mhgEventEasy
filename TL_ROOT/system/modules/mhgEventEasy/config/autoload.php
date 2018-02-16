<?php
/**
 * Contao 3 Extension [mhgEventEasy]
 *
 * Copyright (c) 2018 Medienhaus Gersöne UG (haftungsbeschränkt) | Pierre Gersöne
 *
 * @package     mhgEventEasy
 * @author      Pierre Gersöne <mail@medienhaus-gersoene.de>
 * @link        https://www.medienhaus-gersoene.de Medienhaus Gersöne - Agentur für Neue Medien: Web, Design & Marketing
 * @license     LGPL-3.0+
 */
/**
 * Register the classes
 */
ClassLoader::addClasses(array(
    // Classes
    'mhg\EventEasy' => 'system/modules/mhgEventEasy/classes/EventEasy.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array(
    // Backend
    'be_eventeasy' => 'system/modules/mhgEventEasy/templates/backend',
));
