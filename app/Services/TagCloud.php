<?php

declare(strict_types=1);

namespace App\Services;

class TagCloud
{
    /**
     * Generate cloud tags
     *
     * @param array $tags
     *
     * @return array|int[]
     */
    public function generate(array $tags): array
    {
        $min = min($tags);
        $max = max($tags);

        $links = [];

        $i = 0;
        foreach ($tags as $tag => $count) {
            $size = $this->getSize($count, $min, $max);

            if ($i & 1) {
                $links[$tag] = $size;
            } else {
                $links = [$tag => $size] + $links;
            }

            $i++;
        }


        return $links;
    }

    /**
     * Get size
     *
     * @param int $count
     * @param int $min
     * @param int $max
     * @param int $minSize
     * @param int $maxSize
     *
     * @return int
     */
    public function getSize(int $count, int $min, int $max, int $minSize = 10, int $maxSize = 30): int
    {
        $minCount = log($min);
        $maxCount = log($max);

        $diffSize  = $maxSize - $minSize;
        $diffCount = $maxCount - $minCount;

        if (empty($diffCount)) {
            $diffCount = 1;
        }

        return (int) round($minSize + (log($count) - $minCount) * ($diffSize / $diffCount));
    }
}
