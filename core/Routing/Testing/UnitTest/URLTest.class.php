<?php

/**
 * ResolverTest
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @author      SS4U <ss4u.comvation@gmail.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  core_resolver
 */

namespace Cx\Core\Routing\Testing\UnitTest;
use Cx\Core\Routing\Url as Url;

/**
 * ResolverTest
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Comvation Development Team <info@comvation.com>
 * @author      SS4U <ss4u.comvation@gmail.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  core_resolver
 */
class URLTest extends \Cx\Core\Test\Model\Entity\ContrexxTestCase {
    public function testDomainAndPath() {
        $url = new Url('http://example.com/');   
        $this->assertEquals('example.com', $url->getDomain());
        $this->assertEquals('', $url->getPath());

        $url = new Url('http://example.com/Test');
        $this->assertEquals('example.com', $url->getDomain());
        $this->assertEquals('Test', $url->getPath());

        $url = new Url('http://example.com/Second/Test/?a=asfd');
        $this->assertEquals('example.com', $url->getDomain());
        $this->assertEquals('Second/Test/?a=asfd', $url->getPath());

        $this->assertEquals(false, $url->isRouted());
    }

    public function testSuggestions() {
        $url = new Url('http://example.com/Test');
        $this->assertEquals('Test', $url->getSuggestedTargetPath());
        $this->assertEquals('', $url->getSuggestedParams());

        $url = new Url('http://example.com/Test?foo=bar');
        $this->assertEquals('Test', $url->getSuggestedTargetPath());
        $this->assertEquals('?foo=bar', $url->getSuggestedParams());
    }
    
    public function testPorts() {
        $url = new Url('http://example.com', true);
        $this->assertEquals('80', $url->getPort());
        
        $url = new Url('http://example.com');
        $this->assertEquals('80', $url->getPort());        
        
        $url = new Url('http://example.com:81', true);
        $this->assertEquals('80', $url->getPort());
        
        $url = new Url('http://example.com:81');
        $this->assertEquals('81', $url->getPort());
        
        $url = new Url('https://example.com:445', true);
        $this->assertEquals('443', $url->getPort());
        
        $url = new Url('https://example.com:445');
        $this->assertEquals('445', $url->getPort());
        
        $url = new Url('http://example.com:81/cadmin/', true);
        $this->assertEquals('80', $url->getPort());
        
        $url = new Url('http://example.com:81/cadmin/');
        $this->assertEquals('81', $url->getPort());
        
        $url = new Url('https://example.com:445/cadmin/', true);
        $this->assertEquals('443', $url->getPort());
        
        $url = new Url('https://example.com:445/cadmin/');
        $this->assertEquals('445', $url->getPort());
    }
    
    public function testFileUrls() {
        $testResult = 'file://' . getcwd();
        
        $url = Url::fromRequest();
        $this->assertEquals($testResult, $url->toString());
        $this->assertEquals(getcwd(), (string) $url);
        
        $url = Url::fromMagic($testResult);
        $this->assertEquals($testResult, $url->toString());
        $this->assertEquals(getcwd(), (string) $url);
    }
}
