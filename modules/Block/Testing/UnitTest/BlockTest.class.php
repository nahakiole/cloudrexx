<?php

/**
 * BlockTest
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @author      SS4U <ss4u.comvation@gmail.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  module_block
 */

namespace Cx\Modules\Block\Testing\UnitTest;
use \Cx\Core\Json\Adapter\Block\JsonBlock as JsonBlock;

/**
 * BlockTest
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @author      SS4U <ss4u.comvation@gmail.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  module_block
 */
class BlockTest extends \Cx\Core\Test\Model\Entity\DoctrineTestCase {
    /**
     * @covers \Cx\Core\Json\Adapter\Block\JsonBlock::getBlockContent
     * @expectedException \Cx\Core\Json\Adapter\Block\NoPermissionException
     */
    public function testGetBlockContentNoPermission() {
        global $sessionObj;
        $sessionObj = !$sessionObj ? \cmsSession::getInstance() : $sessionObj;
        $jsonBlock = new JsonBlock();
        $jsonBlock->getBlockContent(array('get' => array('block' => 1, 'lang' => 'de')));
    }
    
    /**
     * @covers \Cx\Core\Json\Adapter\Block\JsonBlock::getBlockContent
     * @expectedException \Cx\Core\Json\Adapter\Block\NotEnoughArgumentsException
     */
    public function testGetBlockContentNotEnoughArguments() {
        global $sessionObj;
        $sessionObj = !$sessionObj ? \cmsSession::getInstance() : $sessionObj;
        $user = \FWUser::getFWUserObject()->objUser->getUser(1);
        \FWUser::loginUser($user);
        
        $jsonBlock = new JsonBlock();
        $jsonBlock->getBlockContent(array());
    }
    
    /**
     * @covers \Cx\Core\Json\Adapter\Block\JsonBlock::getBlockContent
     * @expectedException \Cx\Core\Json\Adapter\Block\NoBlockFoundException
     */
    public function testGetBlockContentNoBlockFound() {
        global $sessionObj;
        $sessionObj = !$sessionObj ? \cmsSession::getInstance() : $sessionObj;
        $user = \FWUser::getFWUserObject()->objUser->getUser(1);
        \FWUser::loginUser($user);
        
        $jsonBlock = new JsonBlock();
        $jsonBlock->getBlockContent(array('get' => array('block' => 999, 'lang' => 'de')));
    }
    
    /**
     * @covers \Cx\Core\Json\Adapter\Block\JsonBlock::getBlockContent
     */
    public function testGetBlockContent() {
        global $sessionObj;
        $sessionObj = !$sessionObj ? \cmsSession::getInstance() : $sessionObj;
        $user = \FWUser::getFWUserObject()->objUser->getUser(1);
        \FWUser::loginUser($user);
        
        $jsonBlock = new JsonBlock();
        $result = $jsonBlock->getBlockContent(array('get' => array('block' => 32, 'lang' => 'de')));
        $this->assertArrayHasKey('content', $result);
    }
    
    /**
     * @covers \Cx\Core\Json\Adapter\Block\JsonBlock::saveBlockContent
     * @expectedException \Cx\Core\Json\Adapter\Block\NotEnoughArgumentsException
     */
    public function testSaveBlockContentNotEnoughArguments() {
        global $sessionObj;
        $sessionObj = !$sessionObj ? \cmsSession::getInstance() : $sessionObj;
        $user = \FWUser::getFWUserObject()->objUser->getUser(1);
        \FWUser::loginUser($user);
        
        $jsonBlock = new JsonBlock();
        $jsonBlock->saveBlockContent(array());
    }
    
    /**
     * @covers \Cx\Core\Json\Adapter\Block\JsonBlock::saveBlockContent
     */
    public function testSaveBlockContent() {
        global $sessionObj;
        $sessionObj = !$sessionObj ? \cmsSession::getInstance() : $sessionObj;
        $user = \FWUser::getFWUserObject()->objUser->getUser(1);
        \FWUser::loginUser($user);
        
        $jsonBlock = new JsonBlock();
        $jsonBlock->saveBlockContent(array('get' => array('block' => 32, 'lang' => 'de'), 'post' => array('content' => 'bla')));
        
        $result = $jsonBlock->getBlockContent(array('get' => array('block' => 32, 'lang' => 'de')));
        $this->assertEquals('bla', $result['content']);
    }
}