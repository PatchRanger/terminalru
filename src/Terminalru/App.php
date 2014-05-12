<?php

/**
 * This file contains a class extending Silex app for better reuse.
 *
 * @category Terminalru
 * @package  Terminalru
 * @author   Dmitry Danilson <patchranger@gmail.com>
 * @license  GPL2 (http://www.gnu.org/licenses/gpl-2.0)
 */

namespace Terminalru;

use Silex\Application;
use Terminalru\Entity\Product as Product;
use Terminalru\Entity\Category as Category;

/**
 * The Terminalru application main class.
 *
 * @category Terminalru
 * @package  Terminalru
 * @author   Dmitry Danilson <patchranger@gmail.com>
 * @license  GPL2 (http://www.gnu.org/licenses/gpl-2.0)
 */
class App extends \Silex\Application
{
    /**
     * Creates default content.
     * To be used during tests or by direct call from script.
     *
     * @return NULL
     */
    public function defaultContent()
    {
        $em = $this['orm.em'];
        // Add categories.
        // Category without products.
        $category0 = new Category();
        $category0->setName('category0');
        $em->persist($category0);
        // Category with 1 product.
        $category1 = new Category();
        $category1->setName('category1');
        $em->persist($category1);
        // Category with 2 products.
        $category2 = new Category();
        $category2->setName('category2');
        $em->persist($category2);
        // Add products.
        // Product out of any category.
        $product = new Product();
        $product->setName('product0');
        $em->persist($product);
        $product = new Product();
        $product->setName('product11');
        $product->addCategory($category1);
        $em->persist($product);
        $product = new Product();
        $product->setName('product21');
        $product->addCategory($category2);
        $em->persist($product);
        $product = new Product();
        $product->setName('product22');
        $product->addCategory($category2);
        $em->persist($product);
        $em->flush();
    }
}