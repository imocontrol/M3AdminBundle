<?php
/*
 * This file is part of the iMOControl package.
 *
 * (c) Michael Ofner <michael@imocontrol.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMOControl\M3\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Pool as SonataAdminPool;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Pool extends SonataAdminPool
{
    /**
     * This function checks if current groupName of the request is active.
     * 
     * @param string Name of current admin dashboard group
     * @return boolean
     */
    public function isActiveGroup($groupName)
    {
        $request = $this->getContainer()->get('request');
        $check = $request->get('_sonata_admin');
        $codes = $this->getAdminGroups();
        
        if (isset($codes[$groupName])) {
            foreach($codes[$groupName]['items'] as $key => $item) {
                if ($item == $check) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
