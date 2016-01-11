<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

JHTML::_('behavior.tooltip');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'components/com_goals/assets/css/template_goals.css');
/**
 * Canvas view for Textbook app
 *
 * @since	1.0
 * @access	public
 */
class GoalsmanagerViewProfile extends SocialAppsView
{
    /**
     * This method is invoked automatically and must exist on this view.
     *
     * @since	1.0.0
     * @access	public
     * @param	int		The user id that is currently being viewed.
     * @return 	void
     */
    public function display( $userId )
    {
        // Requires the viewer to be logged in to access this app
        Foundry::requireLogin();

        JFactory::getLanguage()->load( 'com_goals' , JPATH_ROOT );
        // We want the user object from EasySocial so we can do funky stuffs.
        $user 	= Foundry::user( $userId );

        // Since we are on the canvas page, we have the flexibility to change the page title.
        if( $user->isViewer() )
        {
            $title 	= JText::_( 'Your Goals Manager' );
        }
        else
        {
            $title	= JText::sprintf( 'Goals from %1s' , $user->getName() );
        }

        // Set the page title. You can use JFactory::getDocument()->setTitle( 'title' ) as well.
        Foundry::page()->title( $title );

        $jinput = JFactory::getApplication()->input;

        $vprefix = $jinput->get('easyview');
        if(!$vprefix) $vprefix = 'dashboard';

        // Load up the model
        $model 	= $this->getModel( $vprefix );

        require_once(JPATH_BASE . '/components/com_goals/views/'.$vprefix.'/view.html.php');

        $vclass = 'GoalsView'.$vprefix;
        $view = new $vclass();

        // Get the list of goals created by the user.
        // result is set object of data
        $result = $model->getItems( $userId );
        $pagination = $model->getPagination();

        // result is set array of data
        $data = $view->prepareData($result);

        // Assign the goals to the theme files.
        // This option is totally optional, you can use your own theme object to output files.
        foreach($data as $var => $value)
            $this->set( $var , $value );

        $this->set( 'pagination' , $pagination );
        $this->set( 'user'		, $user );

        // If you use the built in theme manager, the namespace is relative to the following folder,
        // /media/com_easysocial/apps/user/textbook/themes/default

        $namespace 	= $vprefix.'/default';

        // Output the contents
        echo parent::display( $namespace );
    }
}