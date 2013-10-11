<?php
/*
 * This file is part of the iMOControl package.
 *
 * (c) Michael Ofner <michael@imocontrol.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMOControl\M3\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IMOControlM3AdminBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataAdminBundle';
    }

}
