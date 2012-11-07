<?php

namespace Empathy\ELib\Store;

use Empathy\ELib\Model;

class PromoController extends AdminController
{
    public function assertID()
    {
        if (!isset($_GET['id'] ) || !is_numeric($_GET['id'])) {
            $this->redirect('admin/category');
        }
    }

    public function edit()
    {
        $this->setTemplate('elib://admin/promo.tpl');
        $p = Model::load('PromoItem');
        //$pr = new ProductRange($this);

        if (isset($_POST['save'])) {
            $p->id = $_POST['id'];

            /*
              if (!isset($_POST['range'])) {
              $_POST['range'] = array();
              }
              $range = $_POST['range'];
              $pr->updateForProduct($p->id, $range);
            */

            $p->load();
            $old_promo = $p->name;
            $p->name = $_POST['name'];
            $p->url = $_POST['url'];

            $p->validates();
            if ($p->hasValErrors()) {
                // old_product_name along with code in admin_header
                // prevents breadcrumb from breaking on errors
                $this->presenter->assign('promo', $p);
                $this->presenter->assign('old_promo', $old_promo);
                $this->presenter->assign('errors', $p->getValErrors());
            } else {
                //$p->price = $_POST['price'];
                $p->save(Model::getTable('PromoItem'), array(), 1);
                $this->redirect('admin/promo/'.$p->id);
            }
        } else {
            $p->id = $_GET['id'];
            $p->load();
            //$product_ranges = $pr->loadForProduct($p->id);

            //$r = new RangeItem($this);
            //$ranges = $r->loadAllIndexed();
            $this->presenter->assign('promo', $p);
        }
    }

    public function default_event()
    {
        $this->setTemplate('elib://admin/promo.tpl');
        $p = Model::load('PromoItem');
        $p->id = $_GET['id'];
        $p->load();

        $this->presenter->assign("promo", $p);
    }

    public function upload_image()
    {
        $this->setTemplate('elib://admin/promo.tpl');
        if (isset($_POST['upload'])) {
            $_GET['id'] = $_POST['id'];
        }

        $p = Model::load('PromoItem');
        $p->id = $_GET['id'];
        $p->load();

        $this->presenter->assign("promo", $p);

        if (isset($_POST['upload'])) {
            $d = array(array('mid_', 770, 300));
            $u = new ImageUpload('', true, $d);

            if ($u->error != '') {
                $this->presenter->assign("error", $u->error);
            } else {
                if ($p->image != "") {
                    $u->remove(array($p->image));
                }
                // update db
                $p->image = $u->file;
                $p->save(Model::getTable('PromoItem'), array(), 2);

                //$this->redirect_cgi('archive.cgi?id='.$p->id);
                //$this->execScript('archive', array($p->id));
                $this->redirect('admin/promo/'.$p->id);
            }
        }
    }

    public function delete()
    {
        $p = Model::load('PromoItem');
        $p->id = $_GET['id'];
        $p->load();

        $images_removed = false;
        if ($p->image != '') {
            $u = new ImageUpload('', false, array());
            if ($u->remove(array($p->image))) {
                $images_removed = true;
            }
        }
        if ($p->image == '' || $images_removed) {
            $p->delete();
            $this->redirect('admin/promo_category/'.$p->category_id);
        }
    }

}
