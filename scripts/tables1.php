<?php
//
// create various helper tables for BANK3
//

    $f = fopen("s2bank3.mac", "w");
    $addr = 040000;

    // signed byte mirroring table (+0 is at center)
    fputs($f, "; ".decoct($addr)."\n");
    for ($i=0, $b=0200; $i<256; $i++)
    {        
        $rb = (($b & 128) >> 7) + 
              (($b &  64) >> 5) + 
              (($b &  32) >> 3) + 
              (($b &  16) >> 1) + 
              (($b &   8) << 1) +
              (($b &   4) << 3) + 
              (($b &   2) << 5) + 
              (($b &   1) << 7);
        $b++;
        if (($i%8)==0) fputs($f, "\t.byte\t");
        fputs($f, str_pad(decoct($rb),3,"0",STR_PAD_LEFT));
        $addr++;
        if (($i%8)==7) fputs($f, "\n"); else fputs($f, ", ");
        if ($i == 127) fputs($f, "; ".decoct($addr)."\nReflectionTable:\n");
    }
    fputs($f, "; ".decoct($addr)."\n\n");

    // byte to word conversion (red)
    fputs($f, "; ".decoct($addr)."\n");
    fputs($f, "Byte2WordR:\n");
    for ($b=0; $b<256; $b++)
    {
        $w = ($b &   1 ? 0b0000000000000011 : 0) + 
             ($b &   2 ? 0b0000000000001100 : 0) +
             ($b &   4 ? 0b0000000000110000 : 0) +
             ($b &   8 ? 0b0000000011000000 : 0) +
             ($b &  16 ? 0b0000001100000000 : 0) +
             ($b &  32 ? 0b0000110000000000 : 0) +
             ($b &  64 ? 0b0011000000000000 : 0) +
             ($b & 128 ? 0b1100000000000000 : 0);
        if (($b%8)==0) fputs($f, "\t.word\t");
        fputs($f, str_pad(decoct($w),6,"0",STR_PAD_LEFT)); 
        $addr+=2;
        if (($b%8)==7) fputs($f, "\n"); else fputs($f, ", ");
    }
    fputs($f, "; ".decoct($addr)."\n\n");

    // background tiles addr array
    fputs($f, "BackTilesTbl:\n");
    for ($i=0, $addr=0; $i<=0376; $i++, $addr+=10)
    {
        if (($i%8)==0) fputs($f, "\t.word\t");
        fputs($f, "BackTiles+".str_pad(decoct($addr),6,"0",STR_PAD_LEFT));
        if (($i%8)==7) fputs($f, "\n"); else fputs($f, ", ");
    }
    fputs($f, "\n\n");

    // foreground tiles addr array
    fputs($f, "ForeTilesTbl:\n");
    for ($i=0, $addr=0; $i<=0215; $i++, $addr+=18)
    {
        if (($i%8)==0) fputs($f, "\t.word\t");
        fputs($f, "ForeTiles+".str_pad(decoct($addr),6,"0",STR_PAD_LEFT));
        if (($i%8)==7) fputs($f, "\n"); else fputs($f, ", ");
    }
    fputs($f, "\n\n");

    // index to screen coords (from 104000)
    $addr = 0104000;
    $i = 0;
    fputs($f, "Index2Vaddr:\n");
    for ($y=0; $y<18; $y++)
    {
        for ($x=0; $x<32; $x++)
        {
            if (($i%8)==0) fputs($f, "\t.word\t");
            fputs($f, str_pad(decoct($addr),6,"0",STR_PAD_LEFT));
            if (($i%8)==7) fputs($f, "\n"); else fputs($f, ", ");
            $addr += 2;
            $i++;
        }
        $addr += (64*7);
    }
    fputs($f, "\n\n");

    fclose($f);
?>