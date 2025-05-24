<?php

namespace Empathy\ELib\Plugin;

class SmartyResourceELib extends \Smarty_Internal_Resource_File {


    protected function buildFilepath(\Smarty_Template_Source $source, ?\Smarty_Internal_Template $_template = null)
    {
        $file = $source->name;
        $tplDir = $source->smarty->getTemplateDir(null, $source->isConfig)[0];
        $filename = $tplDir.$file;
        $found = '';

        if (!file_exists($filename)) {
            if (isset($source->smarty->tpl_vars['elibtpl_arr'])) {

                foreach ($source->smarty->tpl_vars['elibtpl_arr']->value as $dir) {
                    $filename = $dir.$file;

                    if (file_exists($filename)) {
                        $found = $filename;
                        break;
                    }
                }
            } else {
                $filename = $source->smarty->tpl_vars['elibtpl']->value.$file;
                if (file_exists($filename)) {
                    $found = $filename;
                }
            }
        } else {
            $found = $filename;
        }

        return $found  === '' ? false : $found;
    }
}
