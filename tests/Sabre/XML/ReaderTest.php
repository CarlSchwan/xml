<?php

namespace Sabre\XML;

class ReaderTest extends \PHPUnit_Framework_TestCase {

    function testGetClark() {

        $input = <<<BLA
<?xml version="1.0"?>
<root xmlns="http://sabredav.org/ns" />
BLA;
        $reader = new Reader();
        $reader->xml($input);

        $reader->next();

        $this->assertEquals('{http://sabredav.org/ns}root', $reader->getClark());

    }

    function testGetClarkNoNS() {

        $input = <<<BLA
<?xml version="1.0"?>
<root />
BLA;
        $reader = new Reader();
        $reader->xml($input);

        $reader->next();

        $this->assertNull($reader->getClark());

    }

    function testSimple() {

        $input = <<<BLA
<?xml version="1.0"?>
<root xmlns="http://sabredav.org/ns">
  <elem1 attr="val" />
  <elem2>
    <elem3>Hi!</elem3>
  </elem2>
</root>
BLA;

        $reader = new Reader();
        $reader->xml($input);

        $output = $reader->parse();

        $expected = array(
            array(
                'name' => '{http://sabredav.org/ns}root',
                'value' => array(
                    array(
                        'name' => '{http://sabredav.org/ns}elem1',
                        'value' => null,
                        'attributes' => array(
                            'attr' => 'val',
                        ),
                    ),
                    array(
                        'name' => '{http://sabredav.org/ns}elem2',
                        'value' => array(
                            array(
                                'name' => '{http://sabredav.org/ns}elem3',
                                'value' => 'Hi!',
                                'attributes' => array(),
                            ),
                        ),
                        'attributes' => array(),
                    ),

                ),
                'attributes' => array(),

            ),

        );

        $this->assertEquals($expected, $output);

    }

    function testSimpleNamespacedAttribute() {

        $input = <<<BLA
<?xml version="1.0"?>
<root xmlns="http://sabredav.org/ns" xmlns:foo="urn:foo">
  <elem1 foo:attr="val" />
</root>
BLA;

        $reader = new Reader();
        $reader->xml($input);

        $output = $reader->parse();

        $expected = array(
            array(
                'name' => '{http://sabredav.org/ns}root',
                'value' => array(
                    array(
                        'name' => '{http://sabredav.org/ns}elem1',
                        'value' => null,
                        'attributes' => array(
                            '{urn:foo}attr' => 'val',
                        ),
                    ),
                ),
                'attributes' => array(),
            ),

        );

        $this->assertEquals($expected, $output);

    }

    function testMappedElement() {

        $input = <<<BLA
<?xml version="1.0"?>
<root xmlns="http://sabredav.org/ns">
  <elem1 />
</root>
BLA;

        $reader = new Reader();
        $reader->elementMap = array(
            '{http://sabredav.org/ns}elem1' => 'Sabre\\XML\\Element\\Mock'
        );
        $reader->xml($input);

        $output = $reader->parse();

        $expected = array(
            array(
                'name' => '{http://sabredav.org/ns}root',
                'value' => array(
                    array(
                        'name' => '{http://sabredav.org/ns}elem1',
                        'value' => 'foobar',
                        'attributes' => array(),
                    ),
                ),
                'attributes' => array(),
            ),

        );

        $this->assertEquals($expected, $output);

    }


}
