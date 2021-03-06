<?php

/**
 * DataSet Test
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  core_modules_listing
 */

namespace Cx\Core_Modules\Listing\Testing\UnitTest;

/**
 * DataSet Test
 * 
 * @copyright   CONTREXX CMS - COMVATION AG
 * @author      Michael Ritter <michael.ritter@cloudrexx.com>
 * @version     1.0.0
 * @package     contrexx
 * @subpackage  core_modules_listing
 */
class DataSetTest extends \Cx\Core\Test\Model\Entity\ContrexxTestCase
{
    /**
     * @var array
     * This is our test array. It is built like a real-live example:
     * There's the data of one entity per row
     */
    protected $testArray = array(
        0 => array('field1' => 'value1', 'field2' => 'value2'),
        1 => array('field1' => 'value3', 'field2' => 'value4'),
        2 => array('field1' => 'value3', 'field2' => 'value6'),
    );
    
    /**
     * @var array
     * This is the expected output after flipping the array
     */
    protected $flippedArray = array(
        'field1' => array(0 => 'value1', 1 => 'value3', 2 => 'value3'),
        'field2' => array(0 => 'value2', 1 => 'value4', 2 => 'value6'),
    );
    
    /**
     * @var array
     * This is the expected output after sorting for "field1" descending
     * followed by "field2" ascending
     */
    protected $sortedArray = array(
        1 => array('field1' => 'value3', 'field2' => 'value4'),
        2 => array('field1' => 'value3', 'field2' => 'value6'),
        0 => array('field1' => 'value1', 'field2' => 'value2'),
    );
    
    /**
     * @var array
     * This is the expected output after sorting columns descending
     */
    protected $sortedColumnsArray = array(
        0 => array('field2' => 'value2', 'field1' => 'value1'),
        1 => array('field2' => 'value4', 'field1' => 'value3'),
        2 => array('field2' => 'value6', 'field1' => 'value3'),
    );

    public function testFlip() {
        $testSet = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($this->testArray);
        $flippedSet = $testSet->flip();
        $this->assertEquals($this->flippedArray, $flippedSet->toArray());
    }
    
    public function testSort() {
        $testSet = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($this->testArray);
        $sortedSet = $testSet->sort(array(
            'field1' => SORT_DESC,
            'field2' => SORT_ASC,
        ));
        $this->assertEquals($this->sortedArray, $sortedSet->toArray());
    }
    
    public function testSortColumns() {
        $testSet = new \Cx\Core_Modules\Listing\Model\Entity\DataSet($this->testArray);
        $testSet->sortColumns(array('field2', 'field1'));
        $this->assertEquals($this->sortedColumnsArray, $testSet->toArray());
    }
}

