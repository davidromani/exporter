<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Exporter\Tests\Writer;

use PHPUnit\Framework\TestCase;
use Sonata\Exporter\Writer\XmlExcelWriter;

class XmlExcelWriterTest extends TestCase
{
    protected $filename;

    protected function setUp(): void
    {
        $this->filename = 'foobar.csv';

        if (is_file($this->filename)) {
            unlink($this->filename);
        }
    }

    protected function tearDown(): void
    {
        unlink($this->filename);
    }

    public function testWriter(): void
    {
        $writer = new XmlExcelWriter($this->filename, false);
        $writer->open();

        $writer->write([' john', 'doe &', 'é']);

        $writer->close();

        $expected = '<Row><Cell><Data ss:Type="String"> john</Data></Cell><Cell><Data ss:Type="String">doe &amp;</Data></Cell><Cell><Data ss:Type="String">é</Data></Cell></Row>';

        static::assertTrue(false !== strstr(file_get_contents($this->filename), $expected));
    }

    public function testWithHeaders(): void
    {
        $writer = new XmlExcelWriter($this->filename, true);
        $writer->open();

        $writer->write(['name' => 'john', 'surname' => 'doe ', 'year' => '2001']);

        $writer->close();

        $expected = '<Row><Cell><Data ss:Type="String">name</Data></Cell><Cell><Data ss:Type="String">surname</Data></Cell><Cell><Data ss:Type="String">year</Data></Cell></Row>';
        $expected .= '<Row><Cell><Data ss:Type="String">john</Data></Cell><Cell><Data ss:Type="String">doe </Data></Cell><Cell><Data ss:Type="Number">2001</Data></Cell></Row';

        static::assertTrue(false !== strstr(file_get_contents($this->filename), $expected));
    }

    public function testForceTypes(): void
    {
        // force all cells to have Number type
        $writer = new XmlExcelWriter($this->filename, false, 'Number');
        $writer->open();

        $writer->write(['name' => 'john', 'surname' => 'doe ', 'year' => '2001']);

        $writer->close();

        $expected = '<Row><Cell><Data ss:Type="Number">john</Data></Cell><Cell><Data ss:Type="Number">doe </Data></Cell><Cell><Data ss:Type="Number">2001</Data></Cell></Row>';

        static::assertTrue(false !== strstr(file_get_contents($this->filename), $expected));
    }

    public function testForceTypesWithHeaders(): void
    {
        // force all cells to have Number type
        $writer = new XmlExcelWriter($this->filename, true, 'Number');
        $writer->open();

        $writer->write(['name' => 'john', 'surname' => 'doe ', 'year' => '2001']);

        $writer->close();

        $expected = '<Row><Cell><Data ss:Type="String">name</Data></Cell><Cell><Data ss:Type="String">surname</Data></Cell><Cell><Data ss:Type="String">year</Data></Cell></Row>';
        $expected .= '<Row><Cell><Data ss:Type="Number">john</Data></Cell><Cell><Data ss:Type="Number">doe </Data></Cell><Cell><Data ss:Type="Number">2001</Data></Cell></Row>';

        static::assertTrue(false !== strstr(file_get_contents($this->filename), $expected));
    }

    public function testSpecificTypes(): void
    {
        // define type for specific cell
        $writer = new XmlExcelWriter($this->filename, false, ['year' => 'String', 'surname' => 'Number']);
        $writer->open();

        $writer->write(['name' => 'john', 'surname' => 'doe ', 'year' => '2001']);

        $writer->close();

        $expected = '<Row><Cell><Data ss:Type="String">john</Data></Cell><Cell><Data ss:Type="Number">doe </Data></Cell><Cell><Data ss:Type="String">2001</Data></Cell></Row>';

        static::assertTrue(false !== strstr(file_get_contents($this->filename), $expected));
    }
}
