<?php

/**
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 */

namespace Twitter\Text;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Twitter\Text\Extractor;

/**
 * Twitter Extractor Class Unit Tests
 *
 * @author     Nick Pope <nick@nickpope.me.uk>
 * @copyright  Copyright © 2010, Mike Cochrane, Nick Pope
 * @license    http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package    Twitter.Text
 * @param     Extractor $extractor
 */
class ExtractorTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->extractor = Extractor::create();
    }

    protected function tearDown()
    {
        unset($this->extractor);
        parent::tearDown();
    }

    /**
     * A helper function for providers.
     *
     * @param string  $test  The test to fetch data for.
     *
     * @return array  The test data to provide.
     */
    protected function providerHelper($test)
    {
        $data = Yaml::parse(DATA . '/extract.yml');
        return isset($data['tests'][$test]) ? $data['tests'][$test] : array();
    }

    /**
     * @group Extractor
     */
    public function testExtractURLsWithoutProtocol()
    {
        $extracted = Extractor::create()->extractUrlWithoutProtocol(false)->extractURLs('text: example.com http://foobar.example.com');
        $this->assertSame(array('http://foobar.example.com'), $extracted, 'Unextract url without protocol');
    }

    /**
     * @group Extractor
     */
    public function testExtractURLsWithIndicesWithoutProtocol()
    {
        $extracted = Extractor::create()->extractUrlWithoutProtocol(false)->extractURLsWithIndices('text: example.com');
        $this->assertSame(array(), $extracted, 'Unextract url without protocol');
    }

    public function testUrlWithSpecialCCTLDWithoutProtocol()
    {
        $text = 'MLB.tv vine.co';
        $this->assertSame(array('MLB.tv', 'vine.co'), $this->extractor->extractURLs($text), 'Extract Some ccTLD(co|tv) URLs without protocol');

        $extracted = $this->extractor->extractURLsWithIndices($text);
        $this->assertSame(array(0, 6), $extracted[0]['indices']);
        $this->assertSame(array(7, 14), $extracted[1]['indices']);

        $extracted = $this->extractor->extractUrlWithoutProtocol(false)->extractURLsWithIndices($text);
        $this->assertSame(array(), $extracted, 'Unextract url without protocol');
    }

    public function testExtractURLsWithEmoji()
    {
        $text = "@ummjackson 🤡 https://i.imgur.com/I32CQ81.jpg";
        $extracted = $this->extractor->extractURLsWithIndices($text);
        $this->assertSame(array(14, 45), $extracted[0]['indices']);
        $this->assertSame('https://i.imgur.com/I32CQ81.jpg', StringUtils::substr($text, $extracted[0]['indices'][0], $extracted[0]['indices'][1]));
    }

    public function testExtractURLsPrecededByEllipsis()
    {
        $extracted = $this->extractor->extractURLs('text: ...http://www.example.com');
        $this->assertSame(array('http://www.example.com'), $extracted, 'Unextract url preceded by ellipsis');
    }

    public function testExtractURLsWith64CharDomainWithoutProtocol()
    {
        $text = 'randomurlrandomurlrandomurlrandomurlrandomurlrandomurlrandomurls.com';
        $extracted = $this->extractor->extractURLsWithIndices($text);

        $this->assertSame(array(), $extracted, 'Handle a 64 character domain without protocol');
    }

    public function testExtractURLsHandleLongUrlWithInvalidDomainLabelsAndShortUrl()
    {
        $text = 'Long url with invalid domain labels and a short url: https://somesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurl.com/foo https://somesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurl.com/foo https://somesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurlsomesuperlongurl.com/foo https://validurl.com';
        $extracted = $this->extractor->extractURLsWithIndices($text);
        $this->assertSame(array(
            array(
                'url' => 'https://validurl.com',
                'indices' => array(12056, 12076),
            ),
        ), $extracted, 'Handle long url with invalid domain labels and short url');
    }
}
