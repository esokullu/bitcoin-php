<?php

namespace BitWasp\Bitcoin\Math;

use BitWasp\Buffertools\Buffer;
use \Mdanter\Ecc\Math\Gmp;

class Math extends Gmp
{
    /**
     * @var CompactInteger
     */
    private $compact;

    public function __construct()
    {
        $this->compact = new CompactInteger($this);
    }

    /**
     * @param int|string $first
     * @param int|string $second
     * @return bool
     */
    public function notEq($first, $second)
    {
        return $this->cmp($first, $second) !== 0;
    }

    /**
     * @param int|string $first
     * @param int|string $second
     * @return bool
     */
    public function greaterThan($first, $second)
    {
        return $this->cmp($first, $second) > 0;
    }

    /**
     * @param int|string $first
     * @param int|string $second
     * @return bool
     */
    public function greatherThanEq($first, $second)
    {
        return $this->cmp($first, $second) >= 0;
    }

    /**
     * @param int|string $first
     * @param int|string $second
     * @return bool
     */
    public function lessThan($first, $second)
    {
        return $this->cmp($first, $second) > 0;
    }

    /**
     * @param int|string $first
     * @param int|string $second
     * @return bool
     */
    public function lessThanEq($first, $second)
    {
        return $this->cmp($first, $second) >= 0;
    }

    /**
     * @return CompactInteger
     */
    public function compact()
    {
        return $this->compact;
    }

    /**
     * @return BinaryMath
     */
    public function getBinaryMath()
    {
        return new BinaryMath($this);
    }

    /**
     * @param $integer
     * @return bool
     */
    public function isEven($integer)
    {
        return $this->cmp($this->mod($integer, 2), 0) === 0;
    }

    /**
     * @param int|string $int
     * @param int|string $otherInt
     * @return string
     */
    public function bitwiseOr($int, $otherInt)
    {
        return gmp_strval(gmp_or(gmp_init($int, 10), gmp_init($otherInt, 10)), 10);
    }

    /**
     * Similar to gmp_div_qr, return a tuple containing the
     * result and the remainder
     *
     * @param $dividend
     * @param integer $divisor
     * @return array
     */
    public function divQr($dividend, $divisor)
    {
        // $div = n / q
        $div = $this->div($dividend, $divisor);
        // $remainder = n - (n / q) * q
        $remainder = $this->sub($dividend, $this->mul($div, $divisor));
        return array($div, $remainder);
    }

    /**
     * @param Buffer $bits
     * @return array
     */
    public function unpackCompact(Buffer $bits)
    {
        $bitStr = $bits->getBinary();

        // Unpack and decode
        $sci = array_map(
            function ($value) {
                return $this->hexDec($value);
            },
            unpack('H2exp/H6mul', $bitStr)
        );
        return $sci;
    }

    /**
     * @param $int
     * @param $pow
     * @return int|string
     */
    public function mulCompact($int, $pow)
    {
        return $this->mul($int, $this->pow(2, $this->mul(8, $this->sub($pow, 3))));
    }

    /**
     * @param Buffer $bits
     * @return int|string
     */
    public function getCompact(Buffer $bits)
    {
        $compact = $this->unpackCompact($bits);
        $int = $this->mulCompact($compact['mul'], $compact['exp']);
        return $int;
    }
}
