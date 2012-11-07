<?php

namespace Empathy\ELib\Storage;

use Empathy\ELib\Model,
    Empathy\MVC\Entity;


class ContainerImageSize extends Entity
{
    const TABLE = 'container_image_size';

    public $container_id;
    public $image_size_id;

    public function getImageSizes($container_id)
    {
        $sizes = array();
        $sql = 'SELECT prefix, width, height FROM '.Model::getTable('ImageSize').' i, '
            .Model::getTable('ContainerImageSize').' c WHERE c.image_size_id = i.id'
            .' AND c.container_id = '.$container_id;
        $error = 'Could not get image sizes for container.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                array_push($sizes, array($row['prefix'].'_', $row['width'], $row['height']));
            }
        }

        return $sizes;
    }

    public function getContainerPrefixes($container_id)
    {
        $prefix = array();
        $sql = 'SELECT prefix FROM '.Model::getTable('ImageSize').' i, '
            .Model::getTable('ContainerImageSize').' c WHERE c.image_size_id = i.id'
            .' AND c.container_id = '.$container_id;
        $error = 'Could not get image sizes for container.';
        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                array_push($prefix, $row['prefix'].'_');
            }
        }

        return $prefix;
    }

}
