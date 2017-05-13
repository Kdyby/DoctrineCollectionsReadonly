Kdyby/DoctrineCollectionsReadonly
======

This package provides an implementation of a readonly collection wrapper, so you can return collections from your entities,
without having to copy the contents of the collection, or exposing the object's state outside of it's control.

[![Build Status](https://travis-ci.org/Kdyby/DoctrineCollectionsReadonly.svg?branch=master)](https://travis-ci.org/Kdyby/DoctrineCollectionsReadonly)
[![Downloads this Month](https://img.shields.io/packagist/dm/kdyby/doctrine-collections-readonly.svg)](https://packagist.org/packages/kdyby/doctrine-collections-readonly)
[![Latest stable](https://img.shields.io/packagist/v/kdyby/doctrine-collections-readonly.svg)](https://packagist.org/packages/kdyby/doctrine-collections-readonly)
[![Coverage Status](https://coveralls.io/repos/github/Kdyby/DoctrineCollectionsReadonly/badge.svg?branch=master)](https://coveralls.io/github/Kdyby/DoctrineCollectionsReadonly?branch=master)



Installation
------------

The best way to install Kdyby/DoctrineCollectionsReadonly is using  [Composer](http://getcomposer.org/):

```sh
$ composer require kdyby/doctrine-collections-readonly
```


Usage
-----

Simply wrap your collection and return it.

```php
public function getComments() : Collection
{
	return new ReadOnlyCollectionWrapper($this->comments);
}
```

Now you can use the `Collection` api as you're used to, you just cannot modifying it :)


-----

Homepage [http://www.kdyby.org](http://www.kdyby.org) and repository [http://github.com/Kdyby/DoctrineCollectionsReadonly](http://github.com/Kdyby/DoctrineCollectionsReadonly).
