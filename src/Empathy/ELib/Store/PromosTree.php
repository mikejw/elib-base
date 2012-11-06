<?php

namespace Empathy\ELib\Store;

use Empathy\ELib\Tree;

class PromosTree extends Tree
{
    private $category;
    private $data;
    private $category_ancestors;

    public function __construct($category, $collapsed)
    {
        $this->category = $category;
        $current_id = $this->category->id;
        $this->category_ancestors = array(0);

        if (!$collapsed) {
            array_push($this->category_ancestors, $current_id);
        }

        if ($current_id != 0) {
            $this->category_ancestors = $this->category->getAncestorIDs($current_id, $this->category_ancestors);
        }

        if (!$collapsed) {
            array_push($this->category_ancestors, $current_id);
        }

        $this->data = $this->buildTree(0, $this);
        $this->markup = $this->buildMarkup($this->data, 0, $current_id, 0);
    }

    public function buildTree($id, $tree)
    {
        $nodes = array();
        $nodes = $tree->category->buildTree($id, $tree);

        return $nodes;
    }

    private function buildMarkup($data, $level, $current_id, $last_id)
    {
        $markup = "\n<ul";

        $ancestors = $this->category_ancestors;

        if (!in_array($last_id, $ancestors)) {
            $markup .= " class=\"hidden_sections\"";
        }
        if ($level == 0) {
            $markup .= " id=\"tree\"";
            $level++;
        }
        $markup .=">\n";
        foreach ($data as $index => $value) {
            $toggle = '+';
            $folder = 't_folder_closed.gif';
            $url = 'promo_category';

            if (in_array($value['id'], $ancestors)) {
                $toggle = '-';
                $folder = 't_folder_open.gif';
            }

            $children = sizeof($value['children']);
            $markup .= "<li";

            $markup .= ">\n";
            if ($children > 0) {
                $markup .= "<a class=\"toggle\" href=\"http://".WEB_ROOT.PUBLIC_DIR."/admin/$url/".$value['id'].'/?page=1';
                if ($toggle == '-') {
                    $markup .= '&amp;collapsed=1';
                }
                $markup .= "\">$toggle</a>";
            } else {
                $markup .= "<span class=\"toggle\">&nbsp;</span>";
            }
            $markup .= "<img src=\"http://".WEB_ROOT.PUBLIC_DIR."/elib/$folder\" alt=\"\" />\n";
            if ($current_id == $value['id']) // && $value['data'] != $current_is_section) {
                $markup .= "<span class=\"label current\">".$value['label']."</span>";
            } else {
                $markup .= "<span class=\"label\"><a href=\"http://".WEB_ROOT.PUBLIC_DIR."/admin/$url/".$value['id']."/?page=1\">".$value['label']."</a></span>";
            }
            if ($children > 0) {
                $markup .= $this->buildMarkup($value['children'], $level, $current_id, $value['id']);
            }
            $markup .= "</li>\n";
        }
        $markup .= "</ul>\n";

        return $markup;
    }
}
