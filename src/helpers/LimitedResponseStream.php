<?php
namespace verbb\socialshare\helpers;

use LengthException;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

class LimitedResponseStream implements StreamInterface
{
    // Traits
    // =========================================================================

    use StreamDecoratorTrait;


    // Properties
    // =========================================================================

    private StreamInterface $stream;
    private int $maximumBytes;
    private int $bytesWritten;


    // Public Methods
    // =========================================================================

    public function __construct(StreamInterface $stream, int $maximumBytes)
    {
        $this->stream = $stream;
        $this->maximumBytes = $maximumBytes;
        $this->bytesWritten = $stream->getSize() ?? $stream->tell();

        if ($this->bytesWritten > $this->maximumBytes) {
            $this->_throwResponseTooLarge();
        }
    }

    public function read($length): string
    {
        $remaining = $this->maximumBytes - $this->stream->tell();

        if ($remaining <= 0) {
            $contents = '';
        } else {
            $contents = $this->stream->read(min($length, $remaining));
        }

        // Probe once at the boundary so a one-shot read cannot silently truncate an oversized body.
        if ($this->stream->tell() >= $this->maximumBytes && $this->stream->read(1) !== '') {
            $this->_throwResponseTooLarge();
        }

        return $contents;
    }

    public function write($string): int
    {
        if ($this->bytesWritten + strlen($string) > $this->maximumBytes) {
            $this->_throwResponseTooLarge();
        }

        $written = $this->stream->write($string);
        $this->bytesWritten += $written;

        return $written;
    }


    // Private Methods
    // =========================================================================

    private function _throwResponseTooLarge(): never
    {
        throw new LengthException(sprintf('Provider response exceeded the %d-byte limit.', $this->maximumBytes));
    }
}
