<?php
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see   https://www.github.com/persiliao
 */

declare(strict_types=1);

namespace PersiLiao\Utils;

use function ceil;
use function implode;
use function max;

class Paginator
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $perPage;

    /**
     * @var int
     */
    private $pageTotal;

    /**
     * @var int
     */
    private $rowsTotalNum;

    /**
     * Paginator constructor.
     */
    public function __construct(int $rowsTotalNum, int $currentPage = 1, int $perPage = 10)
    {
        $this->currentPage = max(1, $currentPage);
        $this->perPage = $perPage;
        $this->rowsTotalNum = $rowsTotalNum;
        $this->init();
    }

    protected function init()
    {
        if ($this->rowsTotalNum > 0) {
            if ($this->rowsTotalNum < $this->perPage) {
                $this->pageTotal = 1;
            } else {
                if ($this->rowsTotalNum % $this->perPage) {
                    $this->pageTotal = ceil($this->rowsTotalNum / $this->perPage);
                } else {
                    $this->pageTotal = $this->rowsTotalNum / $this->perPage;
                }
            }
        } else {
            $this->pageTotal = 0;
        }
        $this->start = ($this->currentPage - 1) * $this->perPage;
        $this->end = $this->perPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): Paginator
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): Paginator
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getRowsTotalNum(): int
    {
        return $this->rowsTotalNum;
    }

    public function setRowsTotalNum(int $rowsTotalNum): Paginator
    {
        $this->rowsTotalNum = $rowsTotalNum;

        return $this;
    }

    public function getPageTotal(): int
    {
        return (int) round($this->pageTotal);
    }

    public function setPageTotal(int $pageTotal): Paginator
    {
        $this->pageTotal = $pageTotal;

        return $this;
    }

    public function getSQLLimitToString()
    {
        return implode(',', [
            $this->getStart(),
            $this->getEnd(),
        ]);
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $start): Paginator
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function setEnd(int $end): Paginator
    {
        $this->end = $end;

        return $this;
    }
}
