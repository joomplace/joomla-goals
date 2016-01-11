<?php
/**
* Goals component for Joomla 3.0
* @package Goals
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.table');

class GoalsTablePlanstemplate extends JTable
{
	function __construct(&$db)
	{
		parent::__construct('#__goals_planstemplates', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		$settings = GoalsHelper::getSettings();
		$isnew = false;
		if (!$this->id) $isnew = true;
		
		$form = JRequest::getVar('jform');	
			if (parent::store($updateNulls))
			{

			/*** GOALS XREF ***/
				$userfields = (array)$form['planuserfields'];
					if(sizeof($userfields))
					{
						$db = $this->_db;
						$query = $db->getQuery(true);
							$query->delete('#__goals_plans_xref');
							$query->where('pid='.$this->id);
							$db->setQuery($query);
						$db->query();
							
						foreach ( $userfields as $fid ) 
						{						
							$query = $db->getQuery(true);
								$query->insert('#__goals_plans_xref');
								$query->set('`pid`='.$db->quote($this->id).', `fid`='.$db->quote($fid));
								$db->setQuery($query);
							$db->query();
							
						}
					}
					
			/***/
			
			/*IMAGE*/
                /*
				if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$this->image) && is_file(JPATH_SITE.DIRECTORY_SEPARATOR.$this->image))
				{
					
					$images_width = $settings->images_width?$settings->images_width:250;
					$images_height = $settings->images_height?$settings->images_height:250;
							
					$filepath = JPATH_SITE.DIRECTORY_SEPARATOR.$this->image;
									if (file_exists($filepath) &&  is_file($filepath))
									{
									list($width, $height, $type) = getimagesize($filepath);
											
										if ($width > $images_width){
											$new_width = $images_width;
											$new_height = round(($height*$new_width)/$width);

                                            $image_p = imagecreatetruecolor($new_width, $new_height);

                                            $background_color = imagecolorallocate($image_p, 255, 255, 255);
                                            imagefill ( $image_p, 0, 0, $background_color );
											switch ($type)
											{
												case 3:
													$image = imagecreatefrompng($filepath);
												break;
												case 2:
													$image = imagecreatefromjpeg($filepath);
												break;
												case 1:
													$image = imagecreatefromgif($filepath);
												break;
												case 6:
													$image = imagecreatefromwbmp($filepath);
												break;				
											}	
								
											imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						
											// saving
											switch($type)
											{
												case 3:
													@imagepng($image_p, $filepath);
												break;
												case 2:
													@imagejpeg($image_p, $filepath);
												break;
												case 1:
													@imagegif($image_p, $filepath);
												break;			
											}
										} 
									}
				
				}
                */

				
			return true;
		}else return false;
	}
}