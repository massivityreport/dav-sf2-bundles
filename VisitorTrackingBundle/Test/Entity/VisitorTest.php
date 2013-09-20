<?php

namespace daveudaimon\VisitorTrackingBundle\Tests\Entity;

use daveudaimon\VisitorTrackingBundle\Entity\Visitor;

class VisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetKeywords()
    {
        $visitor = new Visitor();

        // empty referer
        $visitor->setReferer('');
        $this->assertEquals('', $visitor->getKeywords());

        // empty google referer
        $visitor->setReferer('www.google.com');
        $this->assertEquals('', $visitor->getKeywords());

        // simple google search
        $visitor->setReferer('http://www.google.fr/search?q=cadeau+noel+grand+mere+87+ans&hl=fr&tbo=d&rlz=1T4ACPW_fr___FR359&ei=qybPULvUJOnM0AX59oDoDQ&start=10&sa=N&biw=1440&bih=750');
        $this->assertEquals('cadeau noel grand mere 87 ans', $visitor->getKeywords());

        // simple google search
        $visitor->setReferer('http://www.google.fr/search?source=ig&hl=fr&rlz=1G1GGLQ_FRFR362&q=cadeau+pour+grand+meree+80+ans+&oq=cadeau+pour+grang+mere&gs_l=igoogle.1.2.0i13l4j0i13i30l3j0i13i5i30l3.8658.21844.0.25329.22.19.0.3.3.0.143.2257.0j19.19.0...0.0...1ac.1._u4WnqCCnO8');
        $this->assertEquals('cadeau pour grand meree 80 ans ', $visitor->getKeywords());

        // url adwords
        $visitor->setReferer('http://www.google.com/aclk?sa=L&ai=CDZFbVynPUOjEJYSU0wWuuIHoBP_kueECp7bL-lG-99kLCAAQASCxit8GKANQtLrA9wNg-7nxgvgJyAEBqQJuR9-9TxC2PqoEJE_QyVFGEwsANk6V3CMRAuCOzPjO0UzJJpobXobbhSIHGNlt24AFkE6AB5e_-SM&sig=AOD64_3N9_GFVwn5rLuj5iEwKbmeYUCfOw&ved=0CC8Q0Qw&adurl=http://www.famizine.com/pjt6s8&rct=j&q=foulard%20mamie');
        $this->assertEquals('foulard mamie', $visitor->getKeywords());

        // url adwords
        $visitor->setReferer('http://www.google.fr/aclk?sa=L&ai=ClsyVvCjPUPbNA4-P0wWCuYDoDv_kueECp7bL-lG-99kLCAAQASgDULS6wPcDYPvR6oLgCcgBAakCbkffvU8Qtj6qBCZP0BgsWILALSMzI4PsPZWOn0cO_vgRiYUV9BMsY9FJwpmOQKgCSboFEwiA5oj6z6G0AhXxSLQKHU5IAJPKBQCAB5e_-SM&ei=uyjPUMCcC_GR0QXOkIGYCQ&sig=AOD64_3NIqqJRsexBOBg2j9osX4WgiMyOw&sqi=2&ved=0CC4Q0Qw&adurl=http://www.famizine.com/pjt6s8&rct=j&frm=1&q=cadeau+de+noel+pour+ma+grand+mere');
        $this->assertEquals('cadeau de noel pour ma grand mere', $visitor->getKeywords());
    }
}
