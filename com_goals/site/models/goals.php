<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class GoalsModelGoals extends JModelList
{
    var $user;
    var $allowQuery = array( 'easysocial' );
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'title', 'g.title',
                'deadline', 'g.deadline',
                'id', 'g.id',
                'catid', 'g.catid', 'category_title',
            );

            $app = JFactory::getApplication();
            $assoc = JLanguageAssociations::isEnabled();
            if ($assoc)
            {
                $config['filter_fields'][] = 'association';
            }
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout'))
        {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        // List state information.
        parent::populateState('g.title', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string  $id    A prefix for the store id.
     *
     * @return  string  A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.category_id');

        return parent::getStoreId($id);
    }

    public function getCategories(){
        $db		= $this->getDbo();
        $query	= $db->getQuery(true)
            ->select('`title` as `text`, `id` as `value`')
            ->from('`#__goals_categories`');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$user = $this->getQueryUser();
		//Select required fields from the categories.
        $query = GoalsHelperFE::getListQuery("goals",$db, $user);

        // Filter by a single or group of categories.
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId))
        {
            $query->where('g.cid = ' . (int) $categoryId);
        }
        elseif (is_array($categoryId))
        {
            JArrayHelper::toInteger($categoryId);
            $categoryId = implode(',', $categoryId);
            $query->where('g.cid IN (' . $categoryId . ')');
        }

        // Filter by search in name.
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(g.title LIKE ' . $search . ')');
        }

        $orderCol = $this->state->get('list.ordering', 'g.title');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn.', g.ordering ' . $orderDirn));

        $query->order('`g`.`is_complete` DESC,`g`.`deadline` DESC');

		return $query;
	}

    public function setQueryUser($user_id){
        $GLOBALS["viewed_user"] = JFactory::getUser($user_id);
    }

    protected function getQueryUser(){

        return $GLOBALS["viewed_user"];
    }

    public function getQuery(){
        $jinput = JFactory::getApplication()->input;
        $option = $jinput->get('option');
        if(in_array($option, $this->allowQuery)) return $this->getListQuery();
        else return false;
    }

}