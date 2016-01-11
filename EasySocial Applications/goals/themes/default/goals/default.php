<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$this->goals = $goals;
$this->pagination = $pagination;

include_once(JPATH_BASE . '/components/com_goals/views/goals/tmpl/default.php');

?>

<?php/* if( $goals ){ ?>
	<?php foreach( $goals as $goal ){ ?>
		<pre class="">
			<?php print_r($goal); ?>
		</pre>
	<?php } ?>
<?php } else { ?>
	<p>
		<?php echo JText::_( 'No textbooks found.' );?>
	</p>
<?php } */?>