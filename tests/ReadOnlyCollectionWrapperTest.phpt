<?php

/**
 * @testCase
 */

namespace KdybyTests\Doctrine\Collections\Readonly;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionWrapper;
use Tester\Assert;

require_once __DIR__ . '/bootstrap.php';

class ReadOnlyCollectionWrapperTest extends \Tester\TestCase
{

	/** @var \Doctrine\Common\Collections\Collection */
	private $inner;

	/** @var \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionWrapper */
	private $readonly;

	protected function setUp()
	{
		parent::setUp();

		$this->inner = new ArrayCollection();
		$this->readonly = new ReadOnlyCollectionWrapper($this->inner);
	}

	public function testAdd()
	{
		Assert::true($this->inner->isEmpty());

		Assert::exception(function () {
			$this->readonly->add('foo');
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% add an element to %A%');

		Assert::true($this->inner->isEmpty());
	}

	public function testClear()
	{
		$this->inner->set(1, 1);

		Assert::exception(function () {
			$this->readonly->clear();
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% clear %A%');

		Assert::same([1 => 1], $this->inner->toArray());
	}

	public function testContains()
	{
		$this->inner->add(1);
		Assert::true($this->readonly->contains(1));
		Assert::false($this->readonly->contains(2));
	}

	public function testIsEmpty()
	{
		Assert::true($this->readonly->isEmpty());
		$this->inner->add(1);
		Assert::false($this->readonly->isEmpty());
	}

	public function testRemove()
	{
		$this->inner->set(1, 1);

		Assert::exception(function () {
			$this->readonly->remove(1);
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% remove an element from %A%');

		Assert::same([1 => 1], $this->inner->toArray());
	}

	public function testRemoveElement()
	{
		$this->inner->set(1, 1);

		Assert::exception(function () {
			$this->readonly->removeElement(1);
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% remove an element from %A%');

		Assert::same([1 => 1], $this->inner->toArray());
	}

	public function testContainsKey()
	{
		Assert::false($this->readonly->containsKey(1));
		$this->inner->set(1, 1);
		Assert::true($this->readonly->containsKey(1));
	}

	public function testGet()
	{
		Assert::null($this->readonly->get(1));
		$this->inner->set(1, 2);
		Assert::same(2, $this->readonly->get(1));
	}

	public function testGetKeys()
	{
		Assert::same([], $this->readonly->getKeys());
		$this->inner->set(1, 2);
		Assert::same([1], $this->readonly->getKeys());
	}

	public function testGetValues()
	{
		Assert::same([], $this->readonly->getValues());
		$this->inner->set(1, 2);
		Assert::same([2], $this->readonly->getValues());
	}

	public function testSet()
	{
		Assert::exception(function () {
			$this->readonly->set(1, 2);
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% set an element in %A%');

		Assert::same([], $this->inner->toArray());
	}

	public function testToArray()
	{
		Assert::same([], $this->readonly->toArray());
		$this->inner->set(1, 2);
		Assert::same([1 => 2], $this->readonly->toArray());
	}

	public function testFirst()
	{
		Assert::false($this->readonly->first());
		$this->inner->set(1, 2);
		$this->inner->set(3, 4);
		Assert::same(2, $this->readonly->first());
	}

	public function testLast()
	{
		Assert::false($this->readonly->last());
		$this->inner->set(1, 2);
		$this->inner->set(3, 4);
		Assert::same(4, $this->readonly->last());
	}

	public function testKey()
	{
		Assert::null($this->readonly->key());
		$this->inner->set(1, 2);
		$this->inner->set(3, 4);
		Assert::same(1, $this->readonly->key());
	}

	public function testCurrent()
	{
		Assert::false($this->readonly->current());
		$this->inner->set(1, 2);
		$this->inner->set(3, 4);
		Assert::same(2, $this->readonly->current());
	}

	public function testNext()
	{
		Assert::false($this->readonly->next());
		$this->inner->set(1, 2);
		$this->inner->set(3, 4);
		Assert::same(4, $this->readonly->next());
	}

	public function testExists()
	{
		$this->inner->set(1, 2);
		Assert::false($this->readonly->exists(function ($key, $element) {
			return is_string($element);
		}));
		$this->inner->set(3, '4');
		Assert::true($this->readonly->exists(function ($key, $element) {
			return is_string($element);
		}));
	}

	public function testFilter()
	{
		$this->inner->set(1, 2);
		$this->inner->set(3, '4');
		$filtered = $this->readonly->filter(function ($element) {
			return is_string($element);
		});
		Assert::notSame($this->readonly, $filtered);
		Assert::notSame($this->inner, $filtered);
		Assert::type(ArrayCollection::class, $filtered);
		Assert::same([3 => '4'], $filtered->toArray());
	}

	public function testForAll()
	{
		$cond = function ($key, $element) {
			return is_int($element);
		};

		$this->inner->set(1, 2);
		Assert::true($this->inner->forAll($cond));
		Assert::true($this->readonly->forAll($cond));
		$this->inner->set(3, '4');
		Assert::false($this->inner->forAll($cond));
		Assert::false($this->readonly->forAll($cond));
	}

	public function testMap()
	{
		$this->inner->set(1, 2);
		$this->inner->set(3, '4');
		$mapped = $this->readonly->map(function ($element) {
			return $element + 1;
		});
		Assert::notSame($this->readonly, $mapped);
		Assert::notSame($this->inner, $mapped);
		Assert::type(ArrayCollection::class, $mapped);
		Assert::same([1 => 3, 3 => 5], $mapped->toArray());
	}

	public function testPartition()
	{
		$this->inner->set(1, 2);
		$this->inner->set(3, '4');

		/** @var \Doctrine\Common\Collections\Collection $matched */
		/** @var \Doctrine\Common\Collections\Collection $notMatched */
		list($matched, $notMatched) = $this->readonly->partition(function ($key, $element) {
			return is_int($element);
		});

		Assert::notSame($this->readonly, $matched);
		Assert::notSame($this->inner, $matched);
		Assert::type(ArrayCollection::class, $matched);
		Assert::same([1 => 2], $matched->toArray());

		Assert::notSame($this->readonly, $notMatched);
		Assert::notSame($this->inner, $notMatched);
		Assert::type(ArrayCollection::class, $notMatched);
		Assert::same([3 => '4'], $notMatched->toArray());
	}

	public function testIndexOf()
	{
		Assert::false($this->readonly->indexOf(2));
		$this->inner->set(1, 2);
		Assert::same(1, $this->readonly->indexOf(2));
	}

	public function testSlice()
	{
		$this->inner->set(1, 2);
		$this->inner->set(3, '4');
		$this->inner->set(5, 6);
		$slice = $this->readonly->slice(1, 1);
		Assert::same([3 => '4'], $slice);
	}

	public function testGetIterator()
	{
		$this->inner->set(1, 2);
		$this->inner->set(3, '4');
		$this->inner->set(5, 6);
		Assert::same([1 => 2, 3 => '4', 5 => 6], iterator_to_array($this->inner->getIterator()));
		Assert::same([1 => 2, 3 => '4', 5 => 6], iterator_to_array($this->readonly->getIterator()));
	}

	public function testOffsetExists()
	{
		Assert::false($this->readonly->offsetExists(1));
		$this->inner->set(1, 2);
		Assert::true($this->readonly->offsetExists(1));
	}

	public function testOffsetGet()
	{
		Assert::null($this->readonly->offsetGet(1));
		$this->inner->set(1, 2);
		Assert::same(2, $this->readonly->offsetGet(1));
	}

	public function testOffsetSet()
	{
		Assert::exception(function () {
			$this->readonly->offsetSet(1, 2);
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% set an element in %A%');

		Assert::same([], $this->inner->toArray());
	}

	public function testOffsetUnset()
	{
		$this->inner->set(1, 2);
		Assert::exception(function () {
			$this->readonly->offsetUnset(1);
		}, \Kdyby\Doctrine\Collections\Readonly\ReadOnlyCollectionException::class, '%A% remove an element from %A%');

		Assert::same([1 => 2], $this->inner->toArray());
	}

	public function testCount()
	{
		Assert::same(0, $this->readonly->count());
		Assert::same(0, count($this->readonly));
		Assert::same(0, iterator_count($this->readonly));

		$this->inner->set(1, 2);
		$this->inner->set(3, 4);

		Assert::same(2, $this->readonly->count());
		Assert::same(2, count($this->readonly));
		Assert::same(2, iterator_count($this->readonly));
	}

	public function testMatching()
	{
		$this->inner->add($n = (object) ['foo' => 'nope']);
		$this->inner->add($b = (object) ['foo' => 'bar']);

		$criteria = Criteria::create()
			->andWhere(Criteria::expr()->eq('foo', 'bar'));
		$matched = $this->readonly->matching($criteria);

		Assert::notSame($this->readonly, $matched);
		Assert::notSame($this->inner, $matched);
		Assert::type(ArrayCollection::class, $matched);
		Assert::same([1 => $b], $matched->toArray());
	}

}

(new ReadOnlyCollectionWrapperTest())->run();
