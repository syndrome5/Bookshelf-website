<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('nidras_bookshelf_home', new Route('/', array(
    '_controller' => 'NidrasBookshelfBundle:Book:index',
)));

$collection->add('nidras_bookshelf_about', new Route('/about/', array(
    '_controller' => 'NidrasBookshelfBundle:Book:about',
)));

$collection->add('nidras_bookshelf_list', new Route('/list/{search}', array(
    '_controller' => 'NidrasBookshelfBundle:Book:list',
	'search' => null,
)));

$collection->add('nidras_bookshelf_view', new Route('/book/{id}', array(
    '_controller' => 'NidrasBookshelfBundle:Book:view',
	),array(
	'id' => '\d+',
)));

return $collection;