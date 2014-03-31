<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the delete-action, it will delete an item.
 *
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 * @author Koen Vinken <twitter.com/ikoene>
 */
class BackendContentBlocksDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');
		$this->lang = $this->getParameter('lang', 'str');

		// does the item exist
		if($this->id !== null && BackendContentBlocksModel::exists($this->id))
		{
			parent::execute();

			// get record
			$this->record = (array) BackendContentBlocksModel::get($this->id);

			// grab languages settings
			$activeLanguages = (array) BackendModel::getModuleSetting('core', 'active_languages');

			// update content block for every languages
			foreach ($activeLanguages as $language) {

				// get extra id for language
				$this->record['extra_id'] = BackendContentBlocksModel::getExtraIdForLanguage($this->id, $language);

				// delete item
				BackendContentBlocksModel::delete($this->id, $this->record['extra_id'], $language);
			}

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['title']));
		}

		// no item found, redirect to the overview with an error
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
