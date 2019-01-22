<?php

defined('_JEXEC') or die;

/**
 * Goals Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_goals
 * @since       1.5
 */
abstract class GoalsHelperRoute
{
    protected static $default_view = 'dashboard';

    protected static $dinasty = array(
        'goal' => 'goals',
        'editgoal' => 'goals',
        'records' => 'goals',
        'milistones' => 'goals',
        'editrecord' => 'goals',
        'editmilistone' => 'goals',
        'plan' => 'plans',
        'editplan' => 'plans',
        'plantasks' => 'plans',
        'stages' => 'plans',
        'plantask' => 'plans',
        'editplantask' => 'plans',
        'editstage' => 'plans'
    );

    protected static $table_assoc = array(
        'goal'=>'#__goals',
        'editgoal'=>'#__goals',
        'goals'=>'#__goals',
        'milistone'=>'#__goals_milistones',
        'editmilistone'=>'#__goals_milistones',
        'milistones'=>'#__goals_milistones',
        'record'=>'#__goals_tasks',
        'editrecord'=>'#__goals_tasks',
        'records'=>'#__goals_tasks',
        'plan'=>'#__goals_plans',
        'editplan'=>'#__goals_plans',
        'plans'=>'#__goals_plans',
        'task'=>'#__goals_plantasks',
        'edittask'=>'#__goals_plantasks',
        'tasks'=>'#__goals_plantasks',
        'stage'=>'#__goals_stages',
        'editstage'=>'#__goals_stages',
        'stages'=>'#__goals_stages'
    );

    protected static $lookup = array();

    protected static $lang_lookup = array();

    public static function getDinasty(){
        return self::$dinasty;
    }

    public static function str_replace_once($search, $replace, $text){
        $pos = strpos($text, $search);
        return $pos!==false ? substr_replace($text, $replace, $pos, strlen($search)) : $text;
    }

    public static function getTableByView($view){
        $table = false;

        if(array_key_exists($view,self::$table_assoc)){
            $table = self::$table_assoc[$view];
        }

        return $table;
    }

    public static function bakeBread($view, $id){

        $app	= JFactory::getApplication();
        $pathway = $app->getPathway();
        $router = JRouter::getInstance('site');
        $db = JFactory::getDbo();
        $sql = $db->getQuery(true);
        $sql->select('`title`');

        $query = $router->getVars();

        $segments = self::buildRoute($query);

        $sequence = array();
        foreach($segments as &$segment){
            $t = $segment;
            $segment = new stdClass();
            $segment->skip = false;
            $segment->value = self::str_replace_once('-',':',$t);
            $sequence[] = $segment->value;
            unset($t);
            $segment->query = self::parseRoute($sequence);
            if(array_key_exists('id',$segment->query)){
                if($segment->query['id']){
                    $table = self::getTableByView($segment->query['view']);
                    if($table){
                        $sql->from($table);
                        $sql->where('`id` = "'.$segment->query['id'].'"');
                        $db->setQuery($sql);
                        $segment->text = $db->loadResult();
                        $sql->clear('where');
                        $sql->clear('from');
                    }
                }
                else{
                    if($segment->query['view']=='goal' || $segment->query['view']=='plan') $segment->skip = true;
                }
            }
            if(!isset($segment->text) || $segment->text=='') $segment->text = JText::_('COM_GOALS_BREAD_'.str_replace(':','',strtoupper($segment->query['view'])).'_VIEW');
            if(isset($segment->query)){
                $segment->qs = array();
                foreach($segment->query as $key => $val){
                    if($val) $segment->qs[] = $key.'='.$val;
                }
            }

            if(isset($segment->qs)) $segment->link = 'index.php?'.implode('&',$segment->qs);
        }

        for($i=0, $c=count($segments); $i < $c; $i++){
            if($i!=($c-1)){
                if(!$segments[$i]->skip) $pathway->addItem($segments[$i]->text, JRoute::_($segments[$i]->link));
            }else{
                $pathway->addItem($segments[$i]->text);
            }
        }

    }

    /*
     * $vars get parametrs for a link
     * $relative depends if link should be inside other component
     * @return formed link ex. index.php?x=y&...
     */

    public static function buildLink($vars=array(), $relativeLinks=false){

        // get "dinasty" of views

        $router = JRouter::getInstance('site');
        $rv = $router->getVars();
        //we don`t need all so reset
        $query=array();
        $used_params = array('Itemid', 'view');
        foreach($used_params as $que){
            if(isset($rv[$que])) $used_params[$que] = $rv[$que];
        }
        if(isset($query['Itemid'])) $query = array($query['Itemid']);

        $path = array();
        if(!$relativeLinks){
            $path['option'] = 'com_goals';
            if(array_key_exists('Itemid',$query)) $query['Itemid'] = self::getClosesItemId($vars);
        }else{
            if($query['option']=='com_easysocial'){
                $vars['easyview'] = $vars['view'];
                unset($vars['view']);
            }
        }
        $query = array_merge($query, $vars);
        if(!array_key_exists('Itemid',$query)) $query['Itemid'] = self::getClosesItemId($vars);

        $path = array_merge($path, $query);
        if(array_key_exists('Itemid',$vars)) if($vars['Itemid']) $path['Itemid'] = $vars['Itemid'];

        $link='';
        $segments=array();
        foreach($path as $var => $val){
            if($val) $segments[] = $var.'='.$val;
        }
        $link = implode('&',$segments);

        if($link) $link = 'index.php?'.$link;
        else $link = 'index.php';

        return $link;

    }

    public static function buildRoute(&$query){
        $segments = array();

        if(isset($query['Itemid'])) if($query['Itemid']) $ItemQuery = GoalsHelperRoute::getQueryByItemId($query['Itemid']);

        if(isset($query['task'])){
            //$segments[] = 'perfome.'.$query['task'];
            $option = $query['option'];
            unset($query['Itemid']);
        }else{
            if(isset($query['view']))
            {
                if(isset($ItemQuery['view']) && isset($query['view'])) if($ItemQuery['view'] == $query['view']) unset($query['view']);
                if(isset($query['view'])) {
                    if($query['view'] =='dashboard') unset( $query['view'] );

                    if(isset(self::$dinasty[$query['view']]) && isset($ItemQuery['view'])) if(self::$dinasty[$query['view']] && self::$dinasty[$query['view']] != $ItemQuery['view']) $segments[] = self::$dinasty[$query['view']];

                    if($query['view']=='goal' || $query['view']=='plan' || $query['view']=='editgoal' || $query['view']=='editplan'){
                        if(($query['view']=='goal' || $query['view']=='editgoal'))
                        {
                            if($query['view']=='editgoal'){
                                $str = 'edit-goal';
                                if(isset($query['id'])) $str.='-';
                                $segments[] = !empty($query['id']) ? $str.$query['id'] : $str;
                            }
                            else $segments[] = 'goal-'.$query['id'];
                            unset( $query['id'] );
                        };
                        if(($query['view']=='plan' || $query['view']=='editplan'))
                        {
                            if($query['view']=='editplan'){
                                $str = 'edit-plan';
                                if(isset($query['id'])) $str.='-';
                                $segments[] = !empty($query['id']) ? $str.$query['id'] : $str;
                            }
                            else $segments[] = 'plan-'.$query['id'];
                            unset( $query['id'] );
                        };
                        unset( $query['view'] );
                    }

                    if(isset($query['view'])){
                        if($query['view']=='records' || $query['view']=='milistones' || $query['view']=='plantasks' || $query['view']=='stages'){
                            if(isset($query['gid']))
                            {
                                $segments[] = 'goal-'.$query['gid'];
                                unset( $query['gid'] );
                            };
                            if(isset($query['pid']))
                            {
                                $segments[] = 'plan-'.$query['pid'];
                                unset( $query['pid'] );
                            };
                            if(array_key_exists('sid',$query)) {
                                $segments[] = 'stages';
                                unset( $query['sid'] );
                            }

                            if($query['view']=='plantasks')
                            {
                                $query['view'] = str_replace('plantask','task',$query['view']);
                            }else{
                                $query['view'] = str_replace('milistones','milestones',$query['view']);
                                unset( $query['id'] );
                            }

                            $segments[] = $query['view'];
                            unset( $query['view'] );
                        }
                    }

                    if(isset($query['view'])){
                        if(!isset($query['id'])){
                            $modifier = '';
                            if(array_key_exists('pid',$query)) {
                                $modifier = 'plan-'.$query['pid'];
                                unset( $query['pid'] );
                            }
                            if(array_key_exists('gid',$query)) {
                                $modifier = 'goal-'.$query['gid'];
                                unset( $query['gid'] );
                            }
                            if($modifier) $segments[] = $modifier;
                            if($query['view']=='editrecord')
                            {
                                $segments[] = 'records';
                                $segments[] = 'add-record';
                                unset( $query['view'] );
                            }
                            else if($query['view']=='editmilistone')
                            {
                                $segments[] = 'milestones';
                                $segments[] = 'add-milestone';
                                unset( $query['view'] );
                            }
                            else if($query['view']=='editplantask')
                            {
                                $segments[] = 'tasks';
                                $modifier = '';
                                if(isset($query['sid'])){
                                    $modifier = '-to-stage-'.$query['sid'];
                                    unset( $query['sid'] );
                                }
                                $segments[] = 'add-task'.$modifier;
                                unset( $query['view'] );
                            }
                            else if($query['view']=='editstage')
                            {
                                $segments[] = 'add-stage';
                                unset( $query['view'] );
                            }
                        }
                        else{
                            $modifier = '';
                            if(array_key_exists('pid',$query)) {
                                $modifier = 'plan-'.$query['pid'];
                                unset( $query['pid'] );
                            }
                            if(array_key_exists('gid',$query)) {
                                $modifier = 'goal-'.$query['gid'];
                                unset( $query['gid'] );
                            }
                            if($modifier) $segments[] = $modifier;
                            if($query['view']=='editrecord')
                            {
                                $segments[] = 'records';
                                $segments[] = 'edit-record-'.$query['id'];
                                unset( $query['view'] );
                                unset( $query['id'] );
                            }
                            else if($query['view']=='editmilistone')
                            {
                                $segments[] = 'milestones';
                                $segments[] = 'edit-milestone-'.$query['id'];
                                unset( $query['view'] );
                                unset( $query['id'] );
                            }
                            else if($query['view']=='editplantask')
                            {
                                $segments[] = 'tasks';
                                $segments[] = 'edit-task-'.$query['id'];
                                unset( $query['view'] );
                                unset( $query['id'] );
                            }
                            else if($query['view']=='plantask')
                            {
                                $segments[] = 'tasks';
                                $segments[] = 'task-'.$query['id'];
                                unset( $query['view'] );
                                unset( $query['id'] );
                            }
                            else if($query['view']=='editstage')
                            {
                                $segments[] = 'edit-stage-'.$query['id'];
                                unset( $query['view'] );
                                unset( $query['id'] );
                            }
                        }
                    }

                    if(isset($query['view'])){
                        $segments[] = $query['view'];
                    }
                    unset( $query['view'] );


                }
            }
        }
        return $segments;
    }


    public static function parseRoute( $segments )
    {
        $length=count($segments)-1;
        $vars = array();
        if(strpos($segments[$length],'record')!==false || strpos($segments[$length],'milestone')!==false || strpos($segments[$length],'task')!==false || strpos($segments[$length],'stage')!==false){
            $segments[$length] = str_replace('milestone','milistone',$segments[$length]);
            $segments[$length] = str_replace('task','plantask',$segments[$length]);
            if(strpos($segments[$length],'edit')!==false || strpos($segments[$length],'add')!==false){
                $segments[$length] = str_replace('add','edit',$segments[$length]);
                if(strpos($segments[$length],'task')!==false){
                    $segments[$length] = explode('-to-stage-',$segments[$length]);
                    if(isset($target[1])) $vars['sid'] = $segments[$length][1];
                    $segments[$length] = $segments[$length][0];
                }

                $target = explode('-',$segments[$length]);
                $vars['view'] = str_replace(':','',$target[0]);
                if(isset($target[1])) $vars['id'] = str_replace('-','',$target[1]);
            }
            if(strpos($segments[$length],'plantask:')!==false){
                $target = explode(':',$segments[$length]);
                $vars['view'] = str_replace(':','',$target[0]).'s';
                if(isset($target[1])) $vars['id'] = str_replace('-','',$target[1]);
            }
            /*
            if(strpos($segments[$length],'stage:')!==false){
                $target = explode(':',$segments[$length]);
                $vars['view'] = 'plantasks';
                $vars['sid'] = str_replace('-','',$target[1]);
                $target = explode(':',$segments[$length-2]);
                $vars['pid'] = $target[1];
            }
            */
            if(strpos($segments[$length],'task')!==false){
                if(strpos($segments[$length],'tasks')===false){
                    $length -=1;
                }
                $target = explode(':',$segments[$length-1]);
                if($target[0]=='stage') if(isset($target[1])) $vars['sid'] = $target[1];
            }
            /*
            if(strpos($segments[$length],'stages')!==false){
                $segments[$length] = str_replace('stages','plantasks',$segments[$length]);
                $target = explode(':',$segments[$length-1]);
                $vars['pid'] = $target[1];
            }else
            */
            if(strpos($segments[$length],'milistone')!==false || strpos($segments[$length],'record')!==false){
                if(($length-2) > 0) {
                    $target = explode(':', $segments[$length - 2]);
                    if (isset($target[1])) {
                        $vars['gid'] = $target[1];
                    }
                }
            }
            if(!isset($vars['view'])){
                $target = explode('-',$segments[$length]);
                if(!isset($vars['view'])) $vars['view'] = str_replace(':','',$target[0]);
            }

            if(($length-1) > 0) {
                $target = explode(':', $segments[$length - 1]);
                if (strpos($target[0], 'goal') !== false) {
                    if (!isset($vars['gid']) && !isset($vars['pid'])) {
                        if (isset($target[1])) {
                            $vars['gid'] = $target[1];
                        }
                    }
                } else {
                    if (strpos($target[0], 'stage') !== false) {
                        /*
                        if(strpos($target[0],'stages')===false){
                            $target = explode(':',$segments[$length-1]);
                        }
                        */
                        if (!isset($vars['sid'])) {
                            if (isset($target[1])) {
                                $vars['sid'] = $target[1];
                            }
                        }
                        if(($length-3) > 0) {
                            $target = explode(':', $segments[$length - 3]);
                            if (!isset($vars['pid'])) {
                                if (isset($target[1])) {
                                    $vars['pid'] = $target[1];
                                }
                            }
                        }
                    } else {
                        if (!isset($vars['pid']) && !isset($vars['gid'])) {
                            if (isset($target[1])) {
                                $vars['pid'] = $target[1];
                            }
                        }
                    }
                }
            }

        }else{
            if(strpos($segments[$length],'edit')!==false || strpos($segments[$length],'add')!==false){
                $target = explode('-',$segments[$length]);
            }else{
                $target = explode(':',$segments[$length]);
            }
            $vars['view'] = $target[0];
            if(isset($target[1])) $vars['id'] = $target[1];
        }

        return $vars;
    }

    public static function getQueryByItemId($id){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("`id`,`link`")
            ->from('#__menu ')
            ->where('`id`="'.$id.'"')
            ->where('`published` = "1"');
        $db->setQuery($query);
        $link = $db->loadObject();
        $link->vars = explode('?', $link->link);
        $link->vars = $link->vars[1];
        $link->vars = explode('&', $link->vars);
        foreach($link->vars as $var){
            list($key, $value) = explode('=',$var);
            $link->params[$key] = $value;
        }
        return $link->params;
    }

    public static function getClosesItemId($needles = array(),$dinasty = array()){
        if(empty($dinasty)) $dinasty = self::$dinasty;

        if(!array_key_exists('option',$needles)) $needles['option'] = 'com_goals';
        else if(!$needles['option']) $needles['option'] = 'com_goals';

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("`id`,`link`")
            ->from('#__menu ')
            ->where('`published` = "1"');
        if(isset($needles['view'])) $query->where('`link` LIKE "%option='.$needles['option'].'%view='.$needles['view'].'%"');
        else $query->where('`link` LIKE "%option='.$needles['option'].'%"');
        $db->setQuery($query);
        $links = $db->loadObjectList();
        if(empty($links)){
            if(array_key_exists($needles['view'],$dinasty)){
                $query->clear('where')
                    ->where('`link` LIKE "%option='.$needles['option'].'%view='.$dinasty[$needles['view']].'%"')
                    ->where('`published` = "1"');
                $db->setQuery($query);
                $links = $db->loadObjectList();
            }
            if(empty($links)){
                $query->clear('where')
                    ->where('`link` LIKE "%option='.$needles['option'].'%view='.self::$default_view.'%"')
                    ->where('`published` = "1"');
                $db->setQuery($query,0,1);
                $link = $db->loadObject();
                if(!$link){
                    $query->clear('where')
                        ->where('`link` LIKE "%option='.$needles['option'].'%"')
                        ->where('`published` = "1"');
                    $db->setQuery($query,0,1);
                    $link = $db->loadObject();
                }
                return $link->id;
            }
        }
        $defItemId='';
        $itemId='';

        if($links){
            foreach($links as &$link){
                $link->vars = explode('?', $link->link);
                $link->vars = $link->vars[1];
                $link->vars = explode('&', $link->vars);
                foreach($link->vars as $var){
                    list($key, $value) = explode('=',$var);
                    // ??? but may work
                    if($key!='view' || $value != self::$default_view){
                        $link->params[$key] = $value;
                        $itemId = $link->id;
                    }
                }
                if(array_key_exists('view',$link->vars)){
                    $defItemId = $link->id;
                }
                unset($link->vars);
            }
            /*
            $looking_for = array();
            $looking_for['view'] = array_key_exists('view', $needles);
            $looking_for['id'] = array_key_exists('id', $needles);
            $looking_for['task'] = array_key_exists('task', $needles);
            $looking_for['layout'] = array_key_exists('layout', $needles);

            $fallback = false;
            */
            unset($link);
            /*
             * need to be worked on for much depare sef routing from menu
             *
            foreach($links as $link){
                $have = array();
                $have['id'] = array_key_exists('id', $link->params);
                //$have['task'] = array_key_exists('task', $link->params);
                $have['layout'] = array_key_exists('layout', $link->params);

                if($have['id'] && $looking_for['id']){
                    if($link->params['id'] == $needles['id']){
                        $itemId = $link->id;
                        if($have['layout'] && $looking_for['layout']){
                            if($link->params['layout'] == $needles['layout']){
                                $itemId = $link->id;
                            }
                        }
                    }
                }
                else{
                    //fall back to shortest
                    //$fallback = true;
                }
            }
            if($fallback){
                $ch = count($links[0]->params);
                $itemId = $links[0]->id;
                foreach($links as $link){
                    if($ch > count($link->params)){
                        echo $ch;
                        $ch = count($link->params);
                        $itemId = $link->id;
                    }
                }
            }
            */

            /*
            echo '<pre>';
            print_r($itemId);
            echo '</pre>';
            echo '<pre>';
            print_r($fallback);
            echo '</pre>';
            */
            return $itemId;
        }
        else return false;
    }

    /**
     * @param   integer  The route of the content item
     */
    public static function getGoalRoute($id, $language = 0, $catid = 0)
    {
        $needles = array(
            'goal'  => array((int) $id)
        );
        //Create the link
        $link = 'index.php?option=com_goals&view=goal&id='. $id;
        /*
        if ((int) $catid > 1)
        {
            $categories = JCategories::getInstance('Content');
            $category = $categories->get((int) $catid);
            if ($category)
            {
                $needles['category'] = array_reverse($category->getPath());
                $needles['categories'] = $needles['category'];
                $link .= '&catid='.$catid;
            }
        }
        */
        if ($language && $language != "*" && JLanguageMultilang::isEnabled())
        {
            self::buildLanguageLookup();

            if (isset(self::$lang_lookup[$language]))
            {
                $link .= '&lang=' . self::$lang_lookup[$language];
                $needles['language'] = $language;
            }
        }

        if ($item = self::_findItem($needles))
        {
            $link .= '&Itemid='.$item;
        }

        return $link;
    }
/*
    public static function getCategoryRoute($catid, $language = 0)
    {
        if ($catid instanceof JCategoryNode)
        {
            $id = $catid->id;
            $category = $catid;
        }
        else
        {
            $id = (int) $catid;
            $category = JCategories::getInstance('Content')->get($id);
        }

        if ($id < 1 || !($category instanceof JCategoryNode))
        {
            $link = '';
        }
        else
        {
            $needles = array();

            $link = 'index.php?option=com_content&view=category&id='.$id;

            $catids = array_reverse($category->getPath());
            $needles['category'] = $catids;
            $needles['categories'] = $catids;

            if ($language && $language != "*" && JLanguageMultilang::isEnabled())
            {
                self::buildLanguageLookup();

                if(isset(self::$lang_lookup[$language]))
                {
                    $link .= '&lang=' . self::$lang_lookup[$language];
                    $needles['language'] = $language;
                }
            }

            if ($item = self::_findItem($needles))
            {
                $link .= '&Itemid='.$item;
            }
        }

        return $link;
    }
*/
    public static function getFormRoute($type, $id)
    {
        //Create the link
        if ($id)
        {
            $link = 'index.php?option=com_goals&task='.$type.'.edit&id='. $id;
        }
        else
        {
            $link = 'index.php?option=com_goals&task='.$type.'.edit&id=0';
        }

        return $link;
    }

    protected static function buildLanguageLookup()
    {
        if (count(self::$lang_lookup) == 0)
        {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('a.sef AS sef')
                ->select('a.lang_code AS lang_code')
                ->from('#__languages AS a');

            $db->setQuery($query);
            $langs = $db->loadObjectList();

            foreach ($langs as $lang)
            {
                self::$lang_lookup[$lang->lang_code] = $lang->sef;
            }
        }
    }

    protected static function _findItem($needles = null)
    {
        $app		= JFactory::getApplication();
        $menus		= $app->getMenu('site');
        $language	= isset($needles['language']) ? $needles['language'] : '*';

        // Prepare the reverse lookup array.
        if (!isset(self::$lookup[$language]))
        {
            self::$lookup[$language] = array();

            $component	= JComponentHelper::getComponent('com_goals');

            $attributes = array('component_id');
            $values = array($component->id);

            if ($language != '*')
            {
                $attributes[] = 'language';
                $values[] = array($needles['language'], '*');
            }

            $items		= $menus->getItems($attributes, $values);

            foreach ($items as $item)
            {
                if (isset($item->query) && isset($item->query['view']))
                {
                    $view = $item->query['view'];
                    if (!isset(self::$lookup[$language][$view]))
                    {
                        self::$lookup[$language][$view] = array();
                    }
                    if (isset($item->query['id'])) {

                        // here it will become a bit tricky
                        // language != * can override existing entries
                        // language == * cannot override existing entries
                        if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
                        {
                            self::$lookup[$language][$view][$item->query['id']] = $item->id;
                        }
                    }
                }
            }
        }

        if ($needles)
        {
            foreach ($needles as $view => $ids)
            {
                if (isset(self::$lookup[$language][$view]))
                {
                    foreach ($ids as $id)
                    {
                        if (isset(self::$lookup[$language][$view][(int) $id]))
                        {
                            return self::$lookup[$language][$view][(int) $id];
                        }
                    }
                }
            }
        }

        // Check if the active menuitem matches the requested language
        $active = $menus->getActive();
        if ($active && $active->component == 'com_goals' && ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
        {
            return $active->id;
        }

        // If not found, return language specific home link
        $default = $menus->getDefault($language);
        return !empty($default->id) ? $default->id : null;
    }
}
