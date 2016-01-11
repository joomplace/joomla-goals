<?php
/**
* Goals EasySocial widget for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$this->manage_allowed = $manage_allowed;
$this->goals = $goals;
$this->pagination = $pagination;
include_once(JPATH_BASE . '/components/com_goals/views/dashboard/tmpl/default.php');
?>
<?php /*
<ul class="textbooks">
	<?php if( $textbooks ){ ?>
		<?php foreach( $textbooks as $book ){ ?>
		<li class="textboook-item">
			<div><?php echo $book->title;?></div>

			<p><?php echo $book->description;?></p>
		</li>
		<?php } ?>
	<?php } else { ?>
		<li>
			<?php echo JText::_( 'No textbooks found.' );?>
		</li>
	<?php } ?>
</ul>
 */ ?>