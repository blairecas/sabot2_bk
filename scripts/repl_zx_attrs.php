<?php
//
// replace ZX attribute byte to our own
// 00PPPIII -> 000PPII0 (P-paper, I-ink)
//

    $arr_repl = Array(0=>0, 1=>1, 2=>3, 3=>3, 4=>2, 5=>1, 6=>3, 7=>2);


    $f = fopen("backs.mac", "r");
    $g = fopen("backs_out.mac", "w");

    $k = 0;
    while (!feof($f))
    {
        $s = trim(fgets($f));
        if (strlen($s) == 0) continue;
        $s = str_replace(',', '', $s);
        $s = str_replace("\t", ' ', $s);
        $arr = explode(' ', $s);
        if (count($arr) < 13) {
            echo "$s\n";
            fputs($g, $s . "\n");
            continue;
        }
        $s2 = "\t.byte\t";
        for ($i=1; $i<=8; $i++) $s2 = $s2 . $arr[$i] . ", ";
        $zx_attr = octdec($arr[9]);
        $ink = $zx_attr & 0b111;
        $paper = ($zx_attr & 0b111000) >> 3;
        $bk_attr = (($arr_repl[$paper] << 2) + $arr_repl[$ink]) << 1;
        $s2 = $s2 . str_pad(decoct($bk_attr), 3, '0', STR_PAD_LEFT) . ", 0 ; " . str_pad(decoct($k), 3, '0', STR_PAD_LEFT) . "\n";
        fputs($g, $s2);
        $k++;
    }

    fclose($f);
    fclose($g);
?>