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
 * Register backend hooks
 */
if (TL_MODE == 'BE' && Input::get('do') !== 'repository_manager' && Input::get('uninstall') !== 'mhgEventEasy') {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('mhg\EventEasy', 'parseBackendTemplate');
    $GLOBALS['TL_HOOKS']['loadLanguageFile']['EventEasyHook'] = array('mhg\EventEasy', 'loadLanguageFileHook');
    $GLOBALS['TL_HOOKS']['getUserNavigation'][] = array('mhg\EventEasy', 'getUserNavigationHook');
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('mhg\EventEasy', 'loadDataContainerHook');
}