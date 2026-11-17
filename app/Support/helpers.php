<?php

if (! function_exists('rupiah')) {
    /** 1500000 -> "Rp 1.500.000" */
    function rupiah(int|float|null $nominal): string
    {
        return 'Rp '.number_format((float) $nominal, 0, ',', '.');
    }
}
