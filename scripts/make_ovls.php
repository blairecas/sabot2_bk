<?php
//
// make overlays for binary data
// assume binary data is from 01000
// so first overlay must not exceed 037000 bytes + 4 bytes bin header
// others by 40000
//

function write_word ($f, $w)
{
    $b1 = ($w & 0x00FF);
    $b2 = ($w & 0xFF00) >> 8;
    fwrite($f, chr($b1).chr($b2), 2);
}


    if (!isset($argv[1])) { echo "Usage: php -f thisscript.php filename.ext\n"; exit(1); }
    $fname = pathinfo(__FILE__, PATHINFO_DIRNAME)."/../".$argv[1];
    if (!file_exists($fname)) { echo "ERROR: file $fname not found\n"; exit(1); }

    $size = filesize($fname);
    $f = fopen($fname, "rb");

    if ($size >= 037000) $cur_size = 037000; else $cur_size = $size;
    $ovl = fread($f, $cur_size);
    $g = fopen($fname.".ov0", "wb");
    write_word($g, 01000);
    write_word($g, 037000);
    fwrite($g, $ovl, $cur_size);
    $size -= $cur_size;
    while ($cur_size < 037000) {
        fwrite($g, chr(0), 1);
        $cur_size++;
    }
    fclose($g);

    $i = 1;
    while ($size > 0)
    {
        if ($size >= 040000) $cur_size = 040000; else $cur_size = $size;
        $ovl = fread($f, $cur_size);
        $g = fopen($fname.".ov".$i, "wb");
        fwrite($g, $ovl, $cur_size);
        $size -= $cur_size;
        while ($cur_size < 040000) {
            fwrite($g, chr(0), 1);
            $cur_size++;
        }
        fclose($g);
        $i++;
    }

    fclose($f);
?>