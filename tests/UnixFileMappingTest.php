<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace Mediact\FileMapping\Tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Mediact\FileMapping\UnixFileMapping;

/**
 * @coversDefaultClass \Mediact\FileMapping\UnixFileMapping
 */
class UnixFileMappingTest extends TestCase
{
    /**
     * @return string[][]
     */
    public function mappingProvider(): array
    {
        return [
            [
                'deploy.php',
                'deploy.php',
                'deploy.php'
            ],
            [
                'bitbucket-pipelines.yml{.dist,}',
                'bitbucket-pipelines.yml.dist',
                'bitbucket-pipelines.yml'
            ]
        ];
    }

    /**
     * @dataProvider mappingProvider
     *
     * @param string $mapping
     * @param string $expectedSource
     * @param string $expectedDestination
     *
     * @return UnixFileMapping
     *
     * @covers ::__construct
     * @covers ::getRelativeSource
     * @covers ::getRelativeDestination
     */
    public function testMapping(
        string $mapping,
        string $expectedSource,
        string $expectedDestination
    ): UnixFileMapping {
        $mapping = new UnixFileMapping('.', '.', $mapping);

        $this->assertEquals($expectedSource, $mapping->getRelativeSource());
        $this->assertEquals($expectedDestination, $mapping->getRelativeDestination());

        return $mapping;
    }

    /**
     * @return void
     * @covers ::getSource
     * @covers ::getDestination
     */
    public function testDirectoryResolving()
    {
        $fs = vfsStream::setup(
            sha1(__METHOD__),
            null,
            [
                'source' => [
                    'foo' => 'FooContents'
                ],
                'destination' => []
            ]
        );

        $mapping = new UnixFileMapping(
            $fs->getChild('source')->url(),
            $fs->getChild('destination')->url(),
            'foo'
        );

        $this->assertFileExists($mapping->getSource());
        $this->assertFileNotExists($mapping->getDestination());
    }
}