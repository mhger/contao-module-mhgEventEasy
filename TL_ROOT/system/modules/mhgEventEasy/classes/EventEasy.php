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

namespace mhg;


/**
 * class mhg\EventEasy
 */
class EventEasy extends \Contao\Backend {

    /**
     * Is EventEasy enabled
     * @var     bool
     */
    protected $blnEventEasyEnabled = true;

    /**
     * Initialize the object, import the user class
     */
    public function __construct() {
        $this->import('BackendUser', 'User');
        parent::__construct();

        if (!$this->User->hasAccess('create', 'calendarp') || $this->User->eventEasyEnable != 1) {
            $this->blnEventEasyEnabled = false;
        }
    }

    /**
     * Add CSS and Javascript
     * 
     * @param   string
     * @return  boolean
     */
    public function loadLanguageFileHook($strName, $strLanguage) {
        if (!$this->blnEventEasyEnabled) {
            return false;
        }

        if ($this->User->eventEasyEnable == 1) {
            $GLOBALS['TL_CSS'][] = 'system/modules/mhgEventEasy/assets/css/backend.css?v='.time().'|screen';
            $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/mhgEventEasy/assets/js/backend.js?v='.time();

            \System::loadLanguageFile('tl_calendar');
        }

        // make sure the hook is only executed once
        unset($GLOBALS['TL_HOOKS']['loadLanguageFile']['EventEasyHook']);

        return true;
    }

    /**
     * Add the container
     * 
     * @param   string
     * @param   string
     * @return  string
     */
    public function parseBackendTemplate($strContent, $strTemplate) {
        if (!$this->blnEventEasyEnabled || $this->User->eventEasyMode !== 'inject') {
            return $strContent;
        }

        if ($strTemplate == 'be_main') {
            $strContent = str_replace('<div id="container">', '<div id="container">' . "\r\n" . $this->generateContainerContent(), $strContent);
        }

        return $strContent;
    }

    /**
     * Generate the container content
     * 
     * @return string
     */
    protected function generateContainerContent() {
        $arrCalendars = $this->getCalendars();

        if (empty($arrCalendars) || !is_array($arrCalendars)) {
            return '';
        }

        $objTemplate = new \BackendTemplate('be_eventeasy');
        $objTemplate->mode = $this->User->eventEasyMode;
        $objTemplate->class = 'eventeasy_level_2';
        $objTemplate->calendars = $arrCalendars;
        $strReturn = $objTemplate->parse();

        return $strReturn;
    }

    /**
     * Set the GET-Param for the user id so the subpalette can work
     * 
     * @param   string
     * @return  void
     */
    public function loadDataContainerHook($strTable) {
        if ($strTable == 'tl_user' && \Input::get('do') == 'login') {
            \Input::setGet('id', \BackendUser::getInstance()->id);
        }
    }

    /**
     * Prepares an array for the backend navigation
     * 
     * @param   boolean
     * @return  array
     */
    protected function getCalendars() {
        $arrCalendars = array();

        // get all event calendars
        $objCalendars = \Database::getInstance()
                ->prepare("SELECT id, title, eventEasyTitle FROM tl_calendar WHERE eventEasyHide<>1 AND title <> '' ORDER BY title ASC")
                ->execute();

        while ($objCalendars->next()) {
            $strKey = 'calendarEvents' . $objCalendars->id;
            $arrCalendars[$strKey] = array(
                'title' => $objCalendars->title,
                'label' => empty($objCalendars->eventEasyTitle) ? $objCalendars->title : $objCalendars->eventEasyTitle,
                'href' => \Environment::get('script') . '?do=calendar&amp;table=tl_calendar_events&amp;id=' . $objCalendars->id . '&amp;rt=' . REQUEST_TOKEN,
                'class' => 'navigation calendarEvents'
            );
        }

        return $arrCalendars;
    }

    /**
     * Modifies the user navigation
     * 
     * @param   array the modules
     * @param   boolean show all
     * @return  array
     */
    public function getUserNavigationHook($arrModules, $blnShowAll) {
        if (!$this->blnEventEasyEnabled) {
            return $arrModules;
        }

        // if not backend_mode, get out
        if ($this->User->eventEasyMode != 'mod') {
            // add some CSS classes to the content module
            $arrModules['content']['class'].= ' eventeasy_toggle' .
                    ($arrModules['content']['icon'] == 'modPlus.gif' ? ' eventeasy_collapsed' : ' eventeasy_expanded');

            return $arrModules;
        }

        // get event calendars, return standard and get out if empty
        $arrCalendars = $this->getCalendars();
        if (empty($arrCalendars) || !is_array($arrCalendars)) {
            return $arrModules;
        }

        $session = $this->Session->getData();
        $isHidden = isset($session['backend_modules']['calendarEvents']) && $session['backend_modules']['calendarEvents'] < 1;

        $arrNavigation = array(
            'calendarEvents' => array(
                'icon' => $isHidden ? 'modPlus.gif' : 'modMinus.gif',
                'title' => $isHidden ? $GLOBALS['TL_LANG']['MSC']['expandNode'] : $GLOBALS['TL_LANG']['MSC']['collapseNode'],
                'label' => $GLOBALS['TL_LANG']['tl_calendar']['calendarEvents'],
                'href' => $this->addToUrl('mtg=calendarEvents'),
                'modules' => $isHidden ? false : $arrCalendars
            )
        );

        // Insert at a given position if reference is given OR prepend
        if ($this->User->eventEasyReference) {
            $intPosition = array_search(\BackendUser::getInstance()->eventEasyReference, array_keys($arrModules));
            $intPosition++;
            array_insert($arrModules, $intPosition, $arrNavigation);

            $arrReturn = $arrModules;
        } else {
            $arrReturn = array_merge($arrNavigation, $arrModules);
        }

        return $arrReturn;
    }
}
