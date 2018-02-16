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
 * alter DCA palettes
 */
mhg\Dca::modifyPalettes('jumpTo;', 'jumpTo;{eventsEasy_legend},eventsEasyTitle,eventsEasyHide;', 'tl_calendar');


/**
 * add DCA fields
 */
mhg\Dca::addField('tl_calendar', 'eventsEasyTitle', array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['eventsEasyTitle'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'text',
    'eval' => array('mandatory' => false, 'decodeEntities' => true, 'maxlength' => 100, 'tl_class' => 'w50'),
    'sql' => "varchar(100) NOT NULL default ''"
));

mhg\Dca::addField('tl_calendar', 'eventsEasyHide', array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar']['eventsEasyHide'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('tl_class' => 'w50 clr'),
    'sql' => "char(1) NOT NULL default '0'"
));
