<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Tests;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Test\Constraint\EmailHeaderSame;

class EmailTest extends TestCase
{
    public function testSubject()
    {
        $e = new Email();
        $e->subject('Subject');
        $this->assertEquals('Subject', $e->getSubject());
    }

    public function testDate()
    {
        $e = new Email();
        $e->date($d = new \DateTimeImmutable());
        $this->assertSame($d, $e->getDate());
    }

    public function testReturnPath()
    {
        $e = new Email();
        $e->returnPath('fabien@symfony.com');
        $this->assertEquals(new Address('fabien@symfony.com'), $e->getReturnPath());
    }

    public function testSender()
    {
        $e = new Email();
        $e->sender('fabien@symfony.com');
        $this->assertEquals(new Address('fabien@symfony.com'), $e->getSender());

        $e->sender($fabien = new Address('fabien@symfony.com'));
        $this->assertSame($fabien, $e->getSender());
    }

    public function testFrom()
    {
        $e = new Email();
        $helene = new Address('helene@symfony.com');
        $thomas = new Address('thomas@symfony.com', 'Thomas');
        $caramel = new Address('caramel@symfony.com');

        $this->assertSame($e, $e->from('fabien@symfony.com', $helene, $thomas));
        $v = $e->getFrom();
        $this->assertCount(3, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);

        $this->assertSame($e, $e->addFrom('lucas@symfony.com', $caramel));
        $v = $e->getFrom();
        $this->assertCount(5, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);
        $this->assertEquals(new Address('lucas@symfony.com'), $v[3]);
        $this->assertSame($caramel, $v[4]);

        $e = new Email();
        $e->addFrom('lucas@symfony.com', $caramel);
        $this->assertCount(2, $e->getFrom());

        $e = new Email();
        $e->from('lucas@symfony.com');
        $e->from($caramel);
        $this->assertSame([$caramel], $e->getFrom());
    }

    public function testReplyTo()
    {
        $e = new Email();
        $helene = new Address('helene@symfony.com');
        $thomas = new Address('thomas@symfony.com', 'Thomas');
        $caramel = new Address('caramel@symfony.com');

        $this->assertSame($e, $e->replyTo('fabien@symfony.com', $helene, $thomas));
        $v = $e->getReplyTo();
        $this->assertCount(3, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);

        $this->assertSame($e, $e->addReplyTo('lucas@symfony.com', $caramel));
        $v = $e->getReplyTo();
        $this->assertCount(5, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);
        $this->assertEquals(new Address('lucas@symfony.com'), $v[3]);
        $this->assertSame($caramel, $v[4]);

        $e = new Email();
        $e->addReplyTo('lucas@symfony.com', $caramel);
        $this->assertCount(2, $e->getReplyTo());

        $e = new Email();
        $e->replyTo('lucas@symfony.com');
        $e->replyTo($caramel);
        $this->assertSame([$caramel], $e->getReplyTo());
    }

    public function testTo()
    {
        $e = new Email();
        $helene = new Address('helene@symfony.com');
        $thomas = new Address('thomas@symfony.com', 'Thomas');
        $caramel = new Address('caramel@symfony.com');

        $this->assertSame($e, $e->to('fabien@symfony.com', $helene, $thomas));
        $v = $e->getTo();
        $this->assertCount(3, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);

        $this->assertSame($e, $e->addTo('lucas@symfony.com', $caramel));
        $v = $e->getTo();
        $this->assertCount(5, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);
        $this->assertEquals(new Address('lucas@symfony.com'), $v[3]);
        $this->assertSame($caramel, $v[4]);

        $e = new Email();
        $e->addTo('lucas@symfony.com', $caramel);
        $this->assertCount(2, $e->getTo());

        $e = new Email();
        $e->to('lucas@symfony.com');
        $e->to($caramel);
        $this->assertSame([$caramel], $e->getTo());
    }

    public function testCc()
    {
        $e = new Email();
        $helene = new Address('helene@symfony.com');
        $thomas = new Address('thomas@symfony.com', 'Thomas');
        $caramel = new Address('caramel@symfony.com');

        $this->assertSame($e, $e->cc('fabien@symfony.com', $helene, $thomas));
        $v = $e->getCc();
        $this->assertCount(3, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);

        $this->assertSame($e, $e->addCc('lucas@symfony.com', $caramel));
        $v = $e->getCc();
        $this->assertCount(5, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);
        $this->assertEquals(new Address('lucas@symfony.com'), $v[3]);
        $this->assertSame($caramel, $v[4]);

        $e = new Email();
        $e->addCc('lucas@symfony.com', $caramel);
        $this->assertCount(2, $e->getCc());

        $e = new Email();
        $e->cc('lucas@symfony.com');
        $e->cc($caramel);
        $this->assertSame([$caramel], $e->getCc());
    }

    public function testBcc()
    {
        $e = new Email();
        $helene = new Address('helene@symfony.com');
        $thomas = new Address('thomas@symfony.com', 'Thomas');
        $caramel = new Address('caramel@symfony.com');

        $this->assertSame($e, $e->bcc('fabien@symfony.com', $helene, $thomas));
        $v = $e->getBcc();
        $this->assertCount(3, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);

        $this->assertSame($e, $e->addBcc('lucas@symfony.com', $caramel));
        $v = $e->getBcc();
        $this->assertCount(5, $v);
        $this->assertEquals(new Address('fabien@symfony.com'), $v[0]);
        $this->assertSame($helene, $v[1]);
        $this->assertSame($thomas, $v[2]);
        $this->assertEquals(new Address('lucas@symfony.com'), $v[3]);
        $this->assertSame($caramel, $v[4]);

        $e = new Email();
        $e->addBcc('lucas@symfony.com', $caramel);
        $this->assertCount(2, $e->getBcc());

        $e = new Email();
        $e->bcc('lucas@symfony.com');
        $e->bcc($caramel);
        $this->assertSame([$caramel], $e->getBcc());
    }

    public function testPriority()
    {
        $e = new Email();
        $this->assertEquals(3, $e->getPriority());

        $e->priority(1);
        $this->assertEquals(1, $e->getPriority());
        $e->priority(10);
        $this->assertEquals(5, $e->getPriority());
        $e->priority(-10);
        $this->assertEquals(1, $e->getPriority());
    }

    public function testGenerateBodyThrowsWhenEmptyBody()
    {
        $this->expectException(\LogicException::class);
        (new Email())->getBody();
    }

    public function testGetBody()
    {
        $e = new Email();
        $e->setBody($text = new TextPart('text content'));
        $this->assertEquals($text, $e->getBody());
    }

    public function testGenerateBodyWithTextOnly()
    {
        $text = new TextPart('text content');
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->text('text content');
        $this->assertEquals($text, $e->getBody());
        $this->assertEquals('text content', $e->getTextBody());
    }

    public function testGenerateBodyWithHtmlOnly()
    {
        $html = new TextPart('html content', 'utf-8', 'html');
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html('html content');
        $this->assertEquals($html, $e->getBody());
        $this->assertEquals('html content', $e->getHtmlBody());
    }

    public function testGenerateBodyWithTextAndHtml()
    {
        $text = new TextPart('text content');
        $html = new TextPart('html content', 'utf-8', 'html');
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html('html content');
        $e->text('text content');
        $this->assertEquals(new AlternativePart($text, $html), $e->getBody());
    }

    public function testGenerateBodyWithTextAndHtmlNotUtf8()
    {
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html('html content', 'iso-8859-1');
        $e->text('text content', 'iso-8859-1');
        $this->assertEquals('iso-8859-1', $e->getTextCharset());
        $this->assertEquals('iso-8859-1', $e->getHtmlCharset());
        $this->assertEquals(new AlternativePart(new TextPart('text content', 'iso-8859-1'), new TextPart('html content', 'iso-8859-1', 'html')), $e->getBody());
    }

    public function testGenerateBodyWithTextContentAndAttachedFile()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->attach($file);
        $e->text('text content');
        $this->assertEquals(new MixedPart($text, $filePart), $e->getBody());
    }

    public function testGenerateBodyWithHtmlContentAndAttachedFile()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->attach($file);
        $e->html('html content');
        $this->assertEquals(new MixedPart($html, $filePart), $e->getBody());
    }

    public function testGenerateBodyWithHtmlContentAndInlineImageNotreferenced()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $imagePart = new DataPart($image = fopen(__DIR__.'/Fixtures/mimetypes/test.gif', 'r'));
        $imagePart->asInline();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->embed($image);
        $e->html('html content');
        $this->assertEquals(new MixedPart($html, $imagePart), $e->getBody());
    }

    public function testGenerateBodyWithAttachedFileOnly()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->attach($file);
        $this->assertEquals(new MixedPart($filePart), $e->getBody());
    }

    public function testGenerateBodyWithInlineImageOnly()
    {
        $imagePart = new DataPart($image = fopen(__DIR__.'/Fixtures/mimetypes/test.gif', 'r'));
        $imagePart->asInline();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->embed($image);
        $this->assertEquals(new MixedPart($imagePart), $e->getBody());
    }

    public function testGenerateBodyWithEmbeddedImageOnly()
    {
        $imagePart = new DataPart($image = fopen(__DIR__.'/Fixtures/mimetypes/test.gif', 'r'));
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->embed($image);
        $imagePart->asInline();
        $this->assertEquals(new MixedPart($imagePart), $e->getBody());
    }

    public function testGenerateBodyWithTextAndHtmlContentAndAttachedFile()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html('html content');
        $e->text('text content');
        $e->attach($file);
        $this->assertEquals(new MixedPart(new AlternativePart($text, $html), $filePart), $e->getBody());
    }

    public function testGenerateBodyWithTextAndHtmlAndAttachedFileAndAttachedImageNotReferenced()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html('html content');
        $e->text('text content');
        $e->attach($file);
        $e->attach($image, 'test.gif');
        $this->assertEquals(new MixedPart(new AlternativePart($text, $html), $filePart, $imagePart), $e->getBody());
    }

    public function testGenerateBodyWithTextAndAttachedFileAndAttachedImageNotReferenced()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->text('text content');
        $e->attach($file);
        $e->attach($image, 'test.gif');
        $this->assertEquals(new MixedPart($text, $filePart, $imagePart), $e->getBody());
    }

    public function testGenerateBodyWithTextAndHtmlAndAttachedFileAndAttachedImageNotReferencedViaCid()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html($content = 'html content <img src="test.gif">');
        $e->text('text content');
        $e->attach($file);
        $e->attach($image, 'test.gif');
        $fullhtml = new TextPart($content, 'utf-8', 'html');
        $this->assertEquals(new MixedPart(new AlternativePart($text, $fullhtml), $filePart, $imagePart), $e->getBody());
    }

    public function testGenerateBodyWithTextAndHtmlAndAttachedFileAndAttachedImageReferencedViaCid()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html($content = 'html content <img src="cid:test.gif">');
        $e->text('text content');
        $e->attach($file);
        $e->attach($image, 'test.gif');
        $body = $e->getBody();
        $this->assertInstanceOf(MixedPart::class, $body);
        $this->assertCount(2, $related = $body->getParts());
        $this->assertInstanceOf(RelatedPart::class, $related[0]);
        $this->assertEquals($filePart, $related[1]);
        $this->assertCount(2, $parts = $related[0]->getParts());
        $this->assertInstanceOf(AlternativePart::class, $parts[0]);
        $generatedHtml = $parts[0]->getParts()[1];
        $this->assertStringContainsString('cid:'.$parts[1]->getContentId(), $generatedHtml->getBody());
    }

    public function testGenerateBodyWithTextAndHtmlAndAttachedFileAndAttachedImagePartAsInlineReferencedViaCid()
    {
        [$text, $html, $filePart, $file, $imagePart, $image] = $this->generateSomeParts();
        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html($content = 'html content <img src="cid:test.gif">');
        $e->text('text content');
        $e->attach($file);
        $e->attachPart((new DataPart($image, 'test.gif'))->asInline());
        $body = $e->getBody();
        $this->assertInstanceOf(MixedPart::class, $body);
        $this->assertCount(2, $related = $body->getParts());
        $this->assertInstanceOf(RelatedPart::class, $related[0]);
        $this->assertEquals($filePart, $related[1]);
        $this->assertCount(2, $parts = $related[0]->getParts());
        $this->assertInstanceOf(AlternativePart::class, $parts[0]);
        $generatedHtml = $parts[0]->getParts()[1];
        $this->assertStringContainsString('cid:'.$parts[1]->getContentId(), $generatedHtml->getBody());
    }

    public function testGenerateBodyWithHtmlAndInlinedImageTwiceReferencedViaCid()
    {
        // inline image (twice) referenced in the HTML content
        $content = 'html content <img src="cid:test.gif">';
        $r = fopen('php://memory', 'r+', false);
        fwrite($r, $content);
        rewind($r);

        $e = (new Email())->from('me@example.com')->to('you@example.com');
        $e->html($r);
        // embedding the same image twice results in one image only in the email
        $image = fopen(__DIR__.'/Fixtures/mimetypes/test.gif', 'r');
        $e->embed($image, 'test.gif');
        $e->embed($image, 'test.gif');
        $body = $e->getBody();
        $this->assertInstanceOf(RelatedPart::class, $body);
        // 2 parts only, not 3 (text + embedded image once)
        $this->assertCount(2, $parts = $body->getParts());
        $this->assertStringMatchesFormat('html content <img src=3D"cid:%s@symfony">', $parts[0]->bodyToString());
    }

    private function generateSomeParts(): array
    {
        $text = new TextPart('text content');
        $html = new TextPart('html content', 'utf-8', 'html');
        $filePart = new DataPart($file = fopen(__DIR__.'/Fixtures/mimetypes/test', 'r'));
        $imagePart = new DataPart($image = fopen(__DIR__.'/Fixtures/mimetypes/test.gif', 'r'), 'test.gif');

        return [$text, $html, $filePart, $file, $imagePart, $image];
    }

    public function testAttachments()
    {
        // inline part
        $contents = file_get_contents($name = __DIR__.'/Fixtures/mimetypes/test', 'r');
        $att = new DataPart($file = fopen($name, 'r'), 'test');
        $inline = (new DataPart($contents, 'test'))->asInline();
        $e = new Email();
        $e->attach($file, 'test');
        $e->embed($contents, 'test');
        $this->assertEquals([$att, $inline], $e->getAttachments());

        // inline part from path
        $att = DataPart::fromPath($name, 'test');
        $inline = DataPart::fromPath($name, 'test')->asInline();
        $e = new Email();
        $e->attachFromPath($name);
        $e->embedFromPath($name);
        $this->assertEquals([$att->bodyToString(), $inline->bodyToString()], array_map(function (DataPart $a) { return $a->bodyToString(); }, $e->getAttachments()));
        $this->assertEquals([$att->getPreparedHeaders(), $inline->getPreparedHeaders()], array_map(function (DataPart $a) { return $a->getPreparedHeaders(); }, $e->getAttachments()));
    }

    public function testSerialize()
    {
        $r = fopen('php://memory', 'r+', false);
        fwrite($r, 'Text content');
        rewind($r);

        $e = new Email();
        $e->from('fabien@symfony.com');
        $e->to('you@example.com');
        $e->text($r);
        $e->html($r);
        $name = __DIR__.'/Fixtures/mimetypes/test';
        $file = fopen($name, 'r');
        $e->attach($file, 'test');
        $expected = clone $e;
        $n = unserialize(serialize($e));
        $this->assertEquals($expected->getHeaders(), $n->getHeaders());
        $this->assertEquals($e->getBody(), $n->getBody());
    }

    public function testMissingHeaderDoesNotThrowError()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that the Email has header "foo" with value "bar" (value is null).');

        $e = new Email();
        $emailHeaderSame = new EmailHeaderSame('foo', 'bar');
        $emailHeaderSame->evaluate($e);
    }

    public function testAttachBodyExpectStringOrResource()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The body must be a string or a resource (got "bool").');

        (new Email())->attach(false);
    }

    public function testEmbedBodyExpectStringOrResource()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The body must be a string or a resource (got "bool").');

        (new Email())->embed(false);
    }

    public function testHtmlBodyExpectStringOrResourceOrNull()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The body must be a string, a resource or null (got "bool").');

        (new Email())->html(false);
    }

    public function testHtmlBodyAcceptedTypes()
    {
        $email = new Email();

        $email->html('foo');
        $this->assertSame('foo', $email->getHtmlBody());

        $email->html(null);
        $this->assertNull($email->getHtmlBody());

        $contents = file_get_contents(__DIR__.'/Fixtures/mimetypes/test', 'r');
        $email->html($contents);
        $this->assertSame($contents, $email->getHtmlBody());
    }

    public function testTextBodyExpectStringOrResourceOrNull()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The body must be a string, a resource or null (got "bool").');

        (new Email())->text(false);
    }

    public function testTextBodyAcceptedTypes()
    {
        $email = new Email();

        $email->text('foo');
        $this->assertSame('foo', $email->getTextBody());

        $email->text(null);
        $this->assertNull($email->getTextBody());

        $contents = file_get_contents(__DIR__.'/Fixtures/mimetypes/test', 'r');
        $email->text($contents);
        $this->assertSame($contents, $email->getTextBody());
    }

    public function testBodyCache()
    {
        $email = new Email();
        $email->from('fabien@symfony.com');
        $email->to('fabien@symfony.com');
        $email->text('foo');
        $body1 = $email->getBody();
        $body2 = $email->getBody();
        $this->assertSame($body1, $body2, 'The two bodies must reference the same object, so the body cache ensures that the hash for the DKIM signature is unique.');

        $email = new Email();
        $email->from('fabien@symfony.com');
        $email->to('fabien@symfony.com');
        $email->text('foo');
        $body1 = $email->getBody();
        $email->html('<b>bar</b>'); // We change a part to reset the body cache.
        $body2 = $email->getBody();
        $this->assertNotSame($body1, $body2, 'The two bodies must not reference the same object, so the body cache does not ensure that the hash for the DKIM signature is unique.');
    }
}
