<?php

namespace steevanb\CodeGenerator\Model;

/**
 * Add comments property and accessors
 */
trait Comments
{
	/**
	 * Comments
	 *
	 * @var array
	 */
	protected $comments = array();

	/**
	 * Define all comments
	 *
	 * @param array $comments
	 * @return $this
	 */
	public function setComments(array $comments)
	{
		$this->comments = $comments;
		return $this;
	}

	/**
	 * Add comment
	 *
	 * @param string $comment
	 * @return $this
	 */
	public function addComment($comment)
	{
		$this->comments[] = $comment;
		return $this;
	}

	/**
	 * Get comments
	 *
	 * @return array
	 */
	public function getComments()
	{
		return $this->comments;
	}

	/**
	 * Clear comments
	 *
	 * @return $this
	 */
	public function clearComments()
	{
		$this->comments = array();
		return $this;
	}

}