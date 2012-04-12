<?php

/**
* FeedGator - Aggregate RSS newsfeed content into a Joomla! database
* @version 2.3.1
* @package FeedGator
* @author Matt Faulds
* @email mattfaulds@gmail.com
* @copyright (C) 2010 Matthew Faulds - All rights reserved
* @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
*
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class ContentModelArticlesFG extends ContentModelArticles
{
	public function getContentItems($where)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'c.id AS id, c.title AS title, c.alias AS alias, c.checked_out AS checked_out, c.catid AS catid' .
				', c.state AS state, c.access AS access, c.created AS created, c.created_by AS created_by, c.created_by_alias AS created_by_alias, c.ordering AS ordering, c.featured AS featured' . //, c.language, c.hits'
				', c.featured AS frontpage' .
				', \'null\' AS section_name' .
				', \'null\' AS sectionid' .
				', c.publish_up AS publish_up, c.publish_down AS publish_down'
			)
		);
		$query->from('#__content AS c');

		// Join over the language
	//	$query->select('l.title AS language_title');
	//	$query->join('LEFT', '`#__languages` AS l ON l.lang_code = c.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=c.checked_out');

		// Join over the asset groups.
	//	$query->select('ag.title AS access_level');
		$query->select('ag.title AS groupname');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = c.access');

		// Join over the categories.
		$query->select('cc.title AS cat_name');
		$query->join('LEFT', '#__categories AS cc ON cc.id = c.catid');

		// Join over the users for the author.
		$query->select('uc.name AS author');
		$query->join('LEFT', '#__users AS ua ON uc.id = c.created_by');
		
		// Join FG imports and feed details
		$query->select('fi.feed_id AS feedid, fg.title AS feed_title, fg.content_type AS content_type');
		$query->join('LEFT', '#__capturadorfeed_imports AS fi ON fi.content_id = c.id AND fi.plugin = \'com_content\'');
		$query->join('LEFT', '#__capturadorfeed AS fg ON fg.id = fi.feed_id');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('c.access = ' . (int) $access);
		}

		// Filter by published state
		/*$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('c.state = ' . (int) $published);
		}
		else if ($published === '') {
			$query->where('(c.state = 0 OR c.state = 1)');
		}
		if ($filter_state = $this->getState('filter.state')) {
			if ($filter_state == 'P') {
				$where[] = 'c.state = 1';
			} else {
				if ($filter_state == 'U') {
					$where[] = 'c.state = 0';
				} else if ($filter_state == 'A') {
					$where[] = 'c.state = 2';
				} else if ($filter_state == 'T') {
					$where[] = 'c.state = -2';
				}else {
					$where[] = 'c.state != -2';
				}
			}
		}*/

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('c.catid = '.(int) $categoryId);
		}
		else if (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('c.catid IN ('.$categoryId.')');
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('c.created_by '.$type.(int) $authorId);
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.id = '.(int) substr($search, 3));
			}
			else if (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
				$query->where('(uc.name LIKE '.$search.' OR uc.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(c.title LIKE '.$search.' OR c.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('c.language = '.$db->quote($language));
		}
		$where = str_replace(array('WHERE c.state != -2 AND','WHERE'),'',$where);
		$query->where($where);

		// Add the list ordering clause.
	//	$orderCol	= $this->state->get('list.ordering');
	//	$orderDirn	= $this->state->get('list.direction');
	//	if ($orderCol == 'c.ordering' || $orderCol == 'category_title') {
	//		$orderCol = 'category_title '.$orderDirn.', c.ordering';
	//	}
	//	$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		// echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}