<?php

namespace Thumbrise\Toolkit\Opresult\Generate\Internal;

class ReplaceLine
{
    public function do(string $filepath, int $line, $content)
    {
        $filepathTemp = $filepath . '.tmp';

        unlink($filepathTemp);

        $r = fopen($filepath, 'r');
        $w = fopen($filepathTemp, 'w');

        $replaced = false;
        $lineCurrent = 0;

        while (! feof($r)) {
            ++$lineCurrent;

            $lineForWrite = fgets($r);

            if ($lineCurrent === $line) {
                $lineForWrite = $content;
                $replaced = true;
            }

            fputs($w, $lineForWrite);
        }

        fclose($r);
        fclose($w);

        if ($replaced) {
            rename($filepathTemp, $filepath);
        } else {
            unlink($filepathTemp);
        }
    }
}