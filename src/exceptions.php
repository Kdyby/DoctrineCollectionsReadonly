<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Doctrine\Collections\Readonly;

interface Exception
{

}

class ReadOnlyCollectionException extends \LogicException implements \Kdyby\Doctrine\Collections\Readonly\Exception
{

	/**
	 * @param string $what
	 * @return static
	 */
	public static function invalidAccess($what)
	{
		return new static(sprintf('Could not %s read-only collection, write/modify operations are forbidden.', $what));
	}

}

class NotSupportedException extends \LogicException implements \Kdyby\Doctrine\Collections\Readonly\Exception
{

}
