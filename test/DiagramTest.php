<?php
namespace Test;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use WebSequenceDiagrams\Diagram;

/**
 * Class DiagramTest
 * @package Test
 */
class DiagramTest extends \PHPUnit_Framework_TestCase
{
    protected $tmpdir;

    protected function rmdirr($path)
    {
        if (is_dir($path)) {
            foreach (scandir($path) as $file) {
                if ('.' === $file || '..' === $file) {
                    continue;
                }
                unlink($path . '/' . $file);
            }
            rmdir($path);
        }
    }

    public function setUp()
    {
        parent::setUp();

        $path = __DIR__ . '/../tmp';
        $this->rmdirr($path);
        mkdir($path);
        $this->tmpdir = $path;
    }

    public function invalidStyleOrFormatDataProvider()
    {
        return [
            ['potato', 'png'],
            ['default', 'potato'],
            [7, 'png'],
        ];
    }

    /**
     * @test
     */
    public function it_should_set_values_from_constructor()
    {
        $diagram = new Diagram('A->B:', 'default', 'png', 'abcd1234');

        $this->assertEquals('A->B:', $diagram->getMessage());
        $this->assertEquals('default', $diagram->getStyle());
        $this->assertEquals('png', $diagram->getFormat());
        $this->assertEquals('abcd1234', $diagram->getApiKey());
    }

    /**
     * @test
     */
    public function it_should_set_values_via_setters()
    {
        $diagram = new Diagram();

        $this->assertEquals('B->C:', $diagram->setMessage('B->C:')->getMessage());
        $this->assertEquals('svg', $diagram->setFormat('svg')->getFormat());
        $this->assertEquals('abcd1234', $diagram->setApiKey('abcd1234')->getApiKey());
        $this->assertEquals('modern-blue', $diagram->setStyle('modern-blue')->getStyle());
    }

    /**
     * @test
     */
    public function it_should_support_retrieving_guzzle_client()
    {
        $diagram = new Diagram();

        $this->assertInstanceOf(Client::class, $diagram->getGuzzleClient());
    }

    /**
     * @test
     */
    public function it_should_support_setting_guzzle_client()
    {
        $client = new Client(['base_uri' => 'http://test.com']);
        $diagram = new Diagram();
        $diagram->setGuzzleClient($client);

        $this->assertSame($client, $diagram->getGuzzleClient());
        $this->assertEquals('http://test.com', $diagram->getGuzzleClient()->getConfig('base_uri'));
    }

    /**
     * @test
     * @dataProvider invalidStyleOrFormatDataProvider
     * @expectedException \InvalidArgumentException
     * @param $style
     * @param $format
     */
    public function it_should_raise_exception_with_invalid_setting_params($style, $format)
    {
        new Diagram('A->B:', $style, $format);
    }

    /**
     * @test
     * @expectedException \GuzzleHttp\Exception\ClientException
     */
    public function it_should_raise_guzzle_errors_on_bad_response()
    {
        $handler = HandlerStack::create(new MockHandler([new Response(404, []),]));
        $client = new Client(['handler' => $handler]);

        $diagram = new Diagram();
        $diagram->setGuzzleClient($client);

        $diagram->render();
    }

    /**
     * @test
     */
    public function it_should_successfully_render_diagram()
    {
        $handler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{img: "?img=msce7p1ec", page: 0, numPages: 1, errors: []}'
            ),
        ]));
        $client = new Client(['handler' => $handler]);

        $diagram = new Diagram();

        // also set API key, for example
        $diagram->setApiKey('abcd1234');

        $diagram->setGuzzleClient($client);

        $response = $diagram->render();
        $this->assertEquals(200, $diagram->getResponse()->getStatusCode());
        $this->assertSame($response, $diagram->getResponse());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function it_should_not_allow_saving_diagrams_before_render()
    {
        $d = new Diagram('B->+C:');
        $d->save();
    }

    /**
     * @test
     */
    public function it_should_save_a_rendered_response()
    {
        $handler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"img": "?img=msce7p1ec", "page": 0, "numPages": 1, "errors": []}'
            ),
            new Response(
                200,
                ['Content-Type' => 'image/png'],
                new Stream(fopen(__DIR__ . '/resources/example.png', 'r'))
            ),
        ]));
        $client = new Client(['handler' => $handler]);

        $diagram = new Diagram('title A Trivial Example\n\nA->+B:\nB->-A:');
        $diagram->setGuzzleClient($client);

        $diagram->render();
        $diagram->save($this->tmpdir);
        $this->assertFileExists("$this->tmpdir/" . md5(trim($diagram->getMessage())) . '.' . $diagram->getFormat());
    }

    /**
     * @test
     */
    public function it_should_allow_file_name_overrides()
    {
        $handler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"img": "?img=msce7p1ec", "page": 0, "numPages": 1, "errors": []}'
            ),
            new Response(
                200,
                ['Content-Type' => 'image/png'],
                new Stream(fopen(__DIR__ . '/resources/example.png', 'r'))
            ),
        ]));
        $client = new Client(['handler' => $handler]);

        $diagram = new Diagram('title A Trivial Example\n\nA->+B:\nB->-A:');
        $diagram->setGuzzleClient($client);

        $diagram->render();
        $diagram->save($this->tmpdir, 'test-image');
        $this->assertFileExists("$this->tmpdir/" . 'test-image' . '.' . $diagram->getFormat());
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->rmdirr($this->tmpdir);
    }

}