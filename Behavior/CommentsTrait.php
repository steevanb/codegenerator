<?php

namespace steevanb\CodeGenerator\Behavior;

trait CommentsTrait
{
	/** @var array */
	protected $comments = array();

	/**
	 * @param array $comments
	 * @return $this
	 */
	public function setComments(array $comments)
	{
		$this->comments = $comments;

		return $this;
	}

	/**
	 * @param string $comment
	 * @return $this
	 */
	public function addComment($comment)
	{
		$this->comments[] = $comment;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getComments()
	{
		return $this->comments;
	}

	/**
	 * @return $this
	 */
	public function clearComments()
	{
		$this->comments = array();

		return $this;
	}
}
