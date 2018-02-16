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

namespace mhg;


/**
 * class mhg\EventsEasy
 */
class EventsEasy extends \Contao\Backend {

    /**
     * Is EventsEasy enabled
     * @var     bool
     */
    protected $blnEventsEasyEnabled = true;

    /**
     * Initialize the object, import the user class
     */
    public function __construct() {
        $this->import('BackendUser', 'User');
        parent::__construct();

        if (!$this->User->hasAccess('create', 'calendarp') || $this->User->eventsEasyEnable != 1) {
            $this->blnEventsEasyEnabled = false;
        }
    }

    /**
     * Add CSS and Javascript
     * 
     * @param   string
     * @return  boolean
     */
    public function loadLanguageFileHook($strName, $strLanguage) {
        if (!$this->blnEventsEasyEnabled) {
            return false;
        }

        if ($this->User->eventsEasyEnable == 1) {
            $GLOBALS['TL_CSS'][] = 'system/modules/mhgEventsEasy/assets/css/backend.css?v='.time().'|screen';
            $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/mhgEventsEasy/assets/js/backend.js?v='.time();

            \System::loadLanguageFile('tl_calendar');
        }

        // make sure the hook is only executed once
        unset($GLOBALS['TL_HOOKS']['loadLanguageFile']['EventsEasyHook']);

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
        if (!$this->blnEventsEasyEnabled || $this->User->eventsEasyMode !== 'inject') {
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

        $objTemplate = new \BackendTemplate('be_eventseasy');
        $objTemplate->mode = $this->User->eventsEasyMode;
        $objTemplate->class = 'eventseasy_level_2';
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

        /* get all news archives */
        $objCalendars = \Database::getInstance()
                ->prepare("SELECT id, title, eventsEasyTitle FROM tl_calendar WHERE eventsEasyHide<>1 AND title <> '' ORDER BY title ASC")
                ->execute();

        while ($objCalendars->next()) {
            $strKey = 'calendarEvents' . $objCalendars->id;
            $arrCalendars[$strKey] = array(
                'title' => $objCalendars->title,
                'label' => empty($objCalendars->eventsEasyTitle) ? $objCalendars->title : $objCalendars->eventsEasyTitle,
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
        if (!$this->blnEventsEasyEnabled) {
            return $arrModules;
        }

        // if not backend_mode, get out
        if ($this->User->eventsEasyMode != 'mod') {
            // add some CSS classes to the content module
            $arrModules['content']['class'].= ' eventseasy_toggle' .
                    ($arrModules['content']['icon'] == 'modPlus.gif' ? ' eventseasy_collapsed' : ' eventseasy_expanded');

            return $arrModules;
        }

        // get the news archive. if empty, return standard
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
        if ($this->User->eventsEasyReference) {
            $intPosition = array_search(\BackendUser::getInstance()->eventsEasyReference, array_keys($arrModules));
            $intPosition++;
            array_insert($arrModules, $intPosition, $arrNavigation);

            $arrReturn = $arrModules;
        } else {
            $arrReturn = array_merge($arrNavigation, $arrModules);
        }

        return $arrReturn;
    }
}
