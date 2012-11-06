<?php

namespace Empathy\ELib\Storage;

use Empathy\ELib\Model,
    Empathy\MVC\Entity,
    Empathy\MVC\Validate;



class ProductVariant extends Entity
{
    const TABLE = 'product_variant';

    public $id;
    public $product_id;
    public $image;
    public $sku;
    public $weight_g;
    public $weight_lb;
    public $weight_oz;
    public $price;
    public $status;

    public function validates()
    {
        $this->doValType(Validate::NUM, 'weight_g', $this->weight_g, true);
        $this->doValType(Validate::NUM, 'weight_lb', $this->weight_lb, true);
        $this->doValType(Validate::NUM, 'weight_oz', $this->weight_oz, true);
        $this->doValType(Validate::NUM, 'price', $this->price, false);
    }

    public function getVariantName($id)
    {
        $name = array();
        $sql = 'SELECT option_val FROM '.Model::getTable('ProductVariantPropertyOption')
            .' t1, '.Model::getTable('PropertyOption').' t2 WHERE t2.id = t1.property_option_id'
            .' AND product_variant_id = '.$id;
        $error = 'Could not get option values for variant name.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($resut as $row) {
                array_push($name, $row['option_val']);
            }
        }

        return implode(' / ', $name);
    }

    public function getAllForProduct($product_id, $name)
    {
        $variants = array();
        $variant = array();
        $variant_name = array($name);
        $last_id = 0;

        $sql = 'SELECT t1.id, t4.name AS property_name, t3.option_val, t1.weight_g, t1.weight_lb'
            .' FROM '.Model::getTable('ProductVariant').' t1'
            .' LEFT JOIN '.Model::getTable('ProductVariantPropertyOption').' t2'
            .' ON t2.product_variant_id = t1.id'
            .' LEFT JOIN '.Model::getTable('PropertyOption').' t3'
            .' ON t3.id = t2.property_option_id'
            .' LEFT JOIN '.Model::getTable('Property').' t4'
            .' ON t4.id = t3.property_id'
            .' WHERE t1.product_id IN ('.$product_id.')';

        // old
        /*
          $sql = 'SELECT t1.product_variant_id AS id, t3.name as property_name, t4.option_val, t2.weight_g, t2.weight_lb FROM '
          .ProductVariant::$table.' t2, '.ProductVariantPropertyOption::$table.' t1, '.Property::$table.' t3 LEFT JOIN '.PropertyOption::$table
          .' t4 ON t3.id = t4.property_id WHERE t4.id = property_option_id AND t2.id = t1.product_variant_id'
          .' AND t2.product_id = '.$product_id.' ORDER BY t2.weight_g, t2.weight_lb, t2.id, property_name';
        */

        $error = 'Could not get properties for product.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                if ($last_id != 0) {
                    if ($last_id != $row['id']) {
                        $variant['name'] = implode('-', str_replace(' ', '-', $variant_name));
                        array_push($variants, $variant);
                        $variant = array();
                        $variant_name = array($name);
                    }
                }
                if (!isset($variant['id'])) {
                    $variant['id'] = $row['id'];
                    $variant['weight_g'] = $row['weight_g'];
                    $variant['weight_lb'] = $row['weight_lb'];
                }
                $property_name = $row['property_name'];
                $variant[$property_name] = $row['option_val'];
                array_push($variant_name, $row['option_val']);
                $last_id = $row['id'];
            }
        }
        $variant['name'] = implode('-', $variant_name);
        array_push($variants, $variant);

        return $variants;
    }

    public function getAllForOption($option_id)
    {
        $variants = array();
        $sql = 'SELECT v.id AS id, p.name AS name, v.image AS image FROM '.Model::getTable('ProductVariantPropertyOption').' o, '.Model::getTable('ProductVariant')
            .' v, '.Model::getTable('ProductItem').' p WHERE p.id = v.product_id AND o.product_variant_id = v.id AND o.property_option_id = '.$option_id;
        $error = 'Could not get variants by property.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                array_push($variants, $row);
            }
        }

        return $variants;
    }

    public function getCartData($ids)
    {
        $products = array();

        $sql = 'SELECT t5.name, t1.product_id, t1.price, t1.id, t4.name AS p_name, t3.option_val, t1.weight_g, t1.weight_lb'
            .' FROM '.Model::getTable('ProductItem').' t5, '.Model::getTable('ProductVariant').' t1'
            .' LEFT JOIN '.Model::getTable('ProductVariantPropertyOption').' t2'
            .' ON t2.product_variant_id = t1.id'
            .' LEFT JOIN '.Model::getTable('PropertyOption').' t3'
            .' ON t3.id = t2.property_option_id'
            .' LEFT JOIN '.Model::getTable('Property').' t4'
            .' ON t4.id = t3.property_id'
            .' WHERE t1.id IN '.$ids
            .' AND t5.id = t1.product_id';

        // old
        /*
          $sql = 'SELECT t1.name, t3.name AS p_name, t4.price, t2.option_val, t4.id FROM '.ProductItem::$table.' t1,'
          .' '.PropertyOption::$table.' t2, '
          .' '.Property::$table.' t3, '.ProductVariant::$table.' t4'
          .' LEFT JOIN '.ProductVariantPropertyOption::$table.' t5 ON t5.product_variant_id = t4.id'
          .' WHERE t4.id IN '.$ids
          .' AND t4.product_id = t1.id'
          .' AND t5.property_option_id = t2.id'
          .' AND t3.id = t2.property_id';
        */

        $error = 'Could not load data for shopping cart.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            $name = '';
            $options = array();
            $properties = array();
            $id = 0;
            foreach ($result as $row) {
                /*
                  if ($name != $row['name']) {
                  if ($name != '') {
                */
                if ($id != $row['id']) {
                    if ($id != 0) {
                        $item['name'] = $name;
                        $item['options'] = implode(', ', $options);
                        $item['properties'] = implode(', ', $properties);
                        $item['id'] = $id;
                        $item['price'] = $price;
                        $item['product_id'] = $product_id;
                        array_push($products, $item);
                    }
                    $options = array();
                    $properties = array();
                }
                $name = $row['name'];
                $id = $row['id'];
                $price = $row['price'];
                $product_id = $row['product_id'];
                array_push($options, $row['option_val']);
                array_push($properties, $row['p_name']);
            }
            $item['name'] = $name;
            $item['options'] = implode(', ', $options);
            $item['properties'] = implode(', ', $properties);
            $item['id'] = $id;
            $item['price'] = $price;
            $item['product_id'] = $product_id;
            array_push($products, $item);
        }

        return $products;
    }

    public function getAllColourVariants($product_id)
    {
        $variants = array();
        $sql = 'SELECT t1.id, t1.image, t1.weight_g, t1.weight_lb, t1.weight_oz, t1.price, t3.image AS other_image'
            .' FROM '.Model::getTable('ProductVariant').' t1'
            .' LEFT JOIN '.Model::getTable('ProductVariantPropertyOption').' t2'
            .' ON t2.product_variant_id = t1.id'
            .' LEFT JOIN '.Model::getTable('PropertyOption').' t0'
            .' ON t0.id = t2.property_option_id'
            .' LEFT JOIN '.Model::getTable('ProductColour').' t3'
            .' ON t3.property_option_id = t0.id'
            .' WHERE t1.product_id = '.$product_id
            .' AND t0.property_id = 2'
            .' AND t3.product_id = t1.product_id';

        $error = 'Could not get variants.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                array_push($variants, $row);
            }
        }

        return $variants;
    }

    public function findVariant($options, $product_id)
    {
        $i = 2;
        $variant_id = 0;
        $sql = 'SELECT t1.id FROM '.Model::getTable('ProductVariant').' t1';
        foreach ($options as $option) {
            if ($i == 2) {
                $sql .= ' LEFT JOIN '.Model::getTable('ProductVariantPropertyOption').' t'.$i.' ON t'.$i.'.product_variant_id = t'.($i - 1).'.id';
            } else {
                $sql .= ' LEFT JOIN '.Model::getTable('ProductVariantPropertyOption').' t'.$i.' ON t'.$i.'.product_variant_id = t'.($i - 1).'.product_variant_id';
            }
            $i++;
        }

        $i = 2;
        foreach ($options as $option) {
            if ($i == 2) {
                $sql .= ' WHERE t'.$i.'.property_option_id = '.$option;
            } else {
                $sql .= ' AND t'.$i.'.property_option_id = '.$option;
            }
            $i++;
        }
        $sql .= ' AND t1.product_id = '.$product_id;
        $sql .= ' AND t1.status = '.\ELib\Storage\ProductVariantStatus::AVAILABLE;

        $error = 'Could not do search for variant.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() == 1) {
            $row = $result->fetch();
            $variant_id = $row['id'];
        }

        return $variant_id;
    }

    public function getCategories($ids)
    {
        $cat_ids = array();
        $sql = 'SELECT DISTINCT t1.category_id AS id FROM '.Model::getTable('ProductItem').' t1,'
            .' '.Model::getTable('ProductVariant').' t2'
            .' WHERE t2.product_id = t1.id'
            .' AND t2.id IN'.$this->buildUnionString($ids);
        $error = 'Could not get category ids';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                array_push($cat_ids, $row['id']);
            }
        }

        return $cat_ids;
    }

}
