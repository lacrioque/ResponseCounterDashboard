<?php

class ResponseCounterDashboard  extends PluginBase{

    protected $storage = 'DbStorage';
        static protected $name = 'ResponseCounterDashboard';
        static protected $description = 'Add a response counter to the dashboard';
        
        protected $settings = array(
        );
        
        public function init()
        {
            /**
             * Here you should handle subscribing to the events your plugin will handle
             */
            $this->subscribe('surveyDashboardRender');
        }

        public function surveyDashboardRender() {
            $event = $this->getEvent();

            $possiblePanels = $event->get('possiblePanels');
            $definedVars = $event->get('definedVars');

            $responseCount = $this->getResponseCount($definedVars['sid']);

            $definedVars['responseCount'] = $responseCount;

            $possiblePanels[]  = $this->relativePath(
                Yii::getPathOfAlias('webroot'), 
                __DIR__.DIRECTORY_SEPARATOR."responseCounter.twig"
            );

            $event->set('definedVars', $definedVars);
            $event->set('possiblePanels', $possiblePanels);
        }

        private function getResponseCount($sid) 
        {
            $oSurvey = Survey::model()->findByPk($sid);
            if($oSurvey->isActive !== true) {
                return "N/A";
            }

            $iResponseCount = SurveyDynamic::model($sid)->count();
            return $iResponseCount;
        }

        private function relativePath($from, $to, $ps = DIRECTORY_SEPARATOR)
        {
            $arFrom = explode($ps, rtrim($from, $ps));
            $arTo = explode($ps, rtrim($to, $ps));
            while(count($arFrom) && count($arTo) && ($arFrom[0] == $arTo[0]))
            {
                array_shift($arFrom);
                array_shift($arTo);
            }
            return DIRECTORY_SEPARATOR.str_pad("", count($arFrom) * 3, '..'.$ps).implode($ps, $arTo);
        }
}