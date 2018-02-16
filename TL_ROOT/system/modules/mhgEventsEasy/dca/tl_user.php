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
 * Modify DCA palette
 */
$GLOBALS['TL_DCA']['tl_user']['config']['onload_callback'][] = array('tl_user_eventseasy', 'buildPalette');


/**
 * add DCA fields
 */
mhg\Dca::addField('tl_user', 'eventsEasyEnable', array(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['eventsEasyEnable'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true, 'tl_class' => 'tl_checkbox_single_container'),
    'sql' => "char(1) NOT NULL default '0'"
));

mhg\Dca::addField('tl_user', 'eventsEasyMode', array(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['eventsEasyMode'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => array('inject', 'mod'),
    'reference' => &$GLOBALS['TL_LANG']['tl_user']['eventsEasyModes'],
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true),
    'sql' => "varchar(32) NOT NULL default 'inject'"
));

mhg\Dca::addField('tl_user', 'eventsEasyReference', array(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['eventsEasyReference'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => array_keys($GLOBALS['BE_MOD']),
    'reference' => &$GLOBALS['TL_LANG']['MOD'],
    'eval' => array('tl_class' => 'clr', 'includeBlankOption' => true),
    'sql' => "varchar(32) NOT NULL default ''"
));


/**
 * Class tl_user_eventseasy
 */
class tl_user_eventseasy extends Backend {

    /**
     * Build the palette string
     * 
     * @param   DataContainer
     * @return  void
     */
    public function buildPalette(DataContainer $dc) {
        $objUser = \Database::getInstance()->prepare('SELECT * FROM tl_user WHERE id=?')
                ->execute($dc->id);

        foreach ($GLOBALS['TL_DCA']['tl_user']['palettes'] as $palette => $v) {
            if ($palette == '__selector__') {
                continue;
            }

            if (BackendUser::getInstance()->hasAccess('create', 'calendarp')) {
                $arrPalettes = explode(';', $v);
                $arrPalettes[] = '{eventsEasy_legend},eventsEasyEnable;';
                $GLOBALS['TL_DCA']['tl_user']['palettes'][$palette] = implode(';', $arrPalettes);
            }
        }

        // extend selector
        $GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'eventsEasyEnable';

        // extend subpalettes
        $strSubpalette = 'eventsEasyMode';

        if ($objUser->eventsEasyMode == 'mod') {
            $strSubpalette .= ',eventsEasyReference';
        }
        $GLOBALS['TL_DCA']['tl_user']['subpalettes']['eventsEasyEnable'] = $strSubpalette;
    }
}
