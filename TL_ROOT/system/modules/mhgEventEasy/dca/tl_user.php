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
 * Modify DCA palette
 */
$GLOBALS['TL_DCA']['tl_user']['config']['onload_callback'][] = array('tl_user_eventeasy', 'buildPalette');


/**
 * add DCA fields
 */
mhg\Dca::addField('tl_user', 'eventEasyEnable', array(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['eventEasyEnable'],
    'default' => '',
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true, 'tl_class' => 'tl_checkbox_single_container'),
    'sql' => "char(1) NOT NULL default ''"
));

mhg\Dca::addField('tl_user', 'eventEasyMode', array(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['eventEasyMode'],
    'default' => 'inject',
    'exclude' => true,
    'inputType' => 'select',
    'options' => array('inject', 'mod'),
    'reference' => &$GLOBALS['TL_LANG']['tl_user']['eventEasyModes'],
    'eval' => array('tl_class' => 'clr', 'submitOnChange' => true),
    'sql' => "varchar(32) NOT NULL default 'inject'"
));

mhg\Dca::addField('tl_user', 'eventEasyReference', array(
    'label' => &$GLOBALS['TL_LANG']['tl_user']['eventEasyReference'],
    'exclude' => true,
    'inputType' => 'select',
    'options' => array_keys($GLOBALS['BE_MOD']),
    'reference' => &$GLOBALS['TL_LANG']['MOD'],
    'eval' => array('tl_class' => 'clr', 'includeBlankOption' => true),
    'sql' => "varchar(32) NOT NULL default ''"
));


/**
 * Class tl_user_eventeasy
 */
class tl_user_eventeasy extends Backend {

    /**
     * Build the DCA palette string
     * 
     * @param   object $dc DataContainer
     * @return  void
     */
    public function buildPalette(DataContainer $dc) {
        $objUser = \Database::getInstance()->prepare('SELECT * FROM tl_user WHERE id=?')
                ->execute($dc->id);

        // alter DCA pallettes 
        if (BackendUser::getInstance()->hasAccess('create', 'calendarp')) {
            mhg\Dca::appendPalettes('{eventEasy_legend},eventEasyEnable;', 'tl_user');
        }

        // add selector
        mhg\Dca::addSelector('eventEasyEnable', 'tl_user');

        // extend subpalette if enabled
        if ($objUser->eventEasyEnable) {
            $strSubpalette = 'eventEasyMode';

            if ($objUser->eventEasyMode == 'mod') {
                $strSubpalette.= ',eventEasyReference';
            }

            mhg\Dca::appendPalettes($strSubpalette, 'tl_user', 'eventEasyEnable', true);
        }
    }
}
