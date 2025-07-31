<?php

function mirrorByte ( $b )
{
    return (($b & 128) >> 7) + (($b &  64) >> 5) + (($b &  32) >> 3) + (($b &  16) >> 1) + 
           (($b &   8) << 1) + (($b &   4) << 3) + (($b &   2) << 5) + (($b &   1) << 7);
}

function byteToWord3 ( $b )
{
    $w = ($b &   1 ? 0b0000000000000011 : 0) + 
         ($b &   2 ? 0b0000000000001100 : 0) +
         ($b &   4 ? 0b0000000000110000 : 0) +
         ($b &   8 ? 0b0000000011000000 : 0) +
         ($b &  16 ? 0b0000001100000000 : 0) +
         ($b &  32 ? 0b0000110000000000 : 0) +
         ($b &  64 ? 0b0011000000000000 : 0) +
         ($b & 128 ? 0b1100000000000000 : 0);
    return $w;
}

function autoMaskByte ( $b )
{
    return ($b | ($b << 1) | ($b >> 1)) & 0xFF;
}

function setDots ($b)
{
    global $cur_x, $cur_y, $img, $dot0_color, $dot1_color;
    for ($i=0; $i<8; $i++)
    {
        if ($b & 0x01) imagesetpixel($img, $cur_x+$i, $cur_y, $dot1_color);
            else imagesetpixel($img, $cur_x+$i, $cur_y, $dot0_color);
        $b = $b >> 1;
    }
    $cur_y++;
    if (($cur_y%8)==0) {
        $cur_y = $cur_y-8;
        $cur_x += 8;
        if ($cur_x >= 128) {
            $cur_x = 0;
            $cur_y += 8;
        }
    }
}

// not mirrored (also put on helper picture)
function writeTileSet ( $mirror )
{
    global $tiles, $g, $gsize;
    foreach ($tiles as $tile)
    {
    	for ($i=0; $i<count($tile); $i+=2) {
	        $b1 = $tile[$i];
            $b2 = $tile[$i+1];
            if ($mirror) {
                $b1 = mirrorByte($b1);
                $b2 = mirrorByte($b2);
            }
            $m1 = autoMaskByte($b1);
            $m2 = autoMaskByte($b2);
            fwrite($g, chr($m1).chr($m2), 2);   // write mask word
            fwrite($g, chr($b1).chr($b2), 2);   // write data word
            $gsize += 4;
            setDots($b1);
            setDots($b2);
        }
    }
}

    // create RAM bank 7
    $g = fopen(pathinfo(__FILE__, PATHINFO_DIRNAME)."/../release/sabot2.ov6", "w");
    $gsize = 0;

    //////////////////////////////////////////////////////////////////
    // Nina sprites
    //////////////////////////////////////////////////////////////////
    $img = imagecreate(128, 128);
    $dot0_color = imagecolorallocate($img, 0, 0, 0);
    $dot1_color = imagecolorallocate($img, 255, 255, 255);
    $cur_x = 0;
    $cur_y = 0;
    $tiles = Array();
    $f = fopen("nina_sprites.mac", "r");
    while (!feof($f))
    {
        $s = fgets($f);
        $arr = explode(',', $s);
        if (count($arr) < 8) continue;
        $arr0 = explode("\t", $arr[0]);
        $arr15 = explode(" ", $arr[15]);
        // first sprite
        $bytes = Array();
        $bytes[0] = intval($arr0[2], 8);
        for ($i=1; $i<=7; $i++) $bytes[] = intval($arr[$i], 8);
        $tiles[] = $bytes;
        // second sprite
        $bytes = Array();
        for ($i=8; $i<=14; $i++) $bytes[] = intval($arr[$i], 8);
        $bytes[7] = intval($arr15[0], 8);        
        $tiles[] = $bytes;
    }
    writeTileSet(false);
    while ($gsize < 0x1000) { fwrite($g, chr(0), 1); $gsize++; }
    writeTileSet(true);
    while ($gsize < 0x2000) { fwrite($g, chr(0), 1); $gsize++; }
    fclose($f);
    imagepng($img, "nina_sprites.png");


    //////////////////////////////////////////////////////////////////
    // Guards sprites
    //////////////////////////////////////////////////////////////////
    $img = imagecreate(128, 128);
    $dot0_color = imagecolorallocate($img, 0, 0, 0);
    $dot1_color = imagecolorallocate($img, 255, 255, 255);
    $cur_x = 0;
    $cur_y = 0;
    $tiles = Array();
    $f = fopen("guard_sprites.mac", "r");
    while (!feof($f))
    {
        $s = fgets($f);
        $arr = explode(',', $s);
        if (count($arr) < 8) continue;
        $arr0 = explode("\t", $arr[0]);
        $arr15 = explode(" ", $arr[15]);
        // first sprite
        $bytes = Array();
        $bytes[0] = intval($arr0[2], 8);
        for ($i=1; $i<=7; $i++) $bytes[] = intval($arr[$i], 8);
        $tiles[] = $bytes;
        // second sprite
        $bytes = Array();
        for ($i=8; $i<=14; $i++) $bytes[] = intval($arr[$i], 8);
        $bytes[7] = intval($arr15[0], 8);        
        $tiles[] = $bytes;
    }
    writeTileSet(false);
    while ($gsize < 0x3000) { fwrite($g, chr(0), 1); $gsize++; }
    writeTileSet(true);
    while ($gsize < 0x4000) { fwrite($g, chr(0), 1); $gsize++; }
    fclose($f);
    imagepng($img, "guard_sprites.png");

    fclose($g);
?>