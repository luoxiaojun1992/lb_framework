<?php

namespace lb\components\widget;

use lb\components\traits\Singleton;
use lb\Lb;

class Pagination extends Base
{
    use Singleton;

    protected $page;
    protected $dataTotal;
    protected $pageSize = 10;
    protected $pageLen = 10;

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataTotal()
    {
        return $this->dataTotal;
    }

    /**
     * @param mixed $dataTotal
     * @return $this
     */
    public function setDataTotal($dataTotal)
    {
        $this->dataTotal = $dataTotal;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageLen(): int
    {
        return $this->pageLen;
    }

    /**
     * @param int $pageLen
     * @return $this
     */
    public function setPageLen(int $pageLen)
    {
        $this->pageLen = $pageLen;
        return $this;
    }

    public function render()
    {
        $pagination_tpl = <<<Pagination
<nav>
  <ul class="pagination">
    %s
  </ul>
</nav>
Pagination;

        $page_total = ceil($this->getDataTotal() / $this->getPageSize());
        if (!$this->getPage() || !is_int($this->getPage()) || $this->getPage() < 1) {
            $this->setPage(1);
        }
        if ($this->getPage() > $page_total) {
            $this->setPage($page_total);
        }

        //页码范围计算
        $init = 1;//起始页码数
        $max = $page_total;//结束页码数
        $pagelen = ($this->getPageLen() % 2) ? $this->getPageLen() : $this->getPageLen() + 1;//页码个数
        $pageoffset = ($pagelen - 1) / 2;//页码个数左右偏移量
        //分页数大于页码个数时可以偏移
        if ($page_total > $pagelen) {
            //如果当前页小于等于左偏移
            if ($this->getPage() <= $pageoffset) {
                $init = 1;
                $max = $pagelen;
            } else {//如果当前页大于左偏移
                //如果当前页码右偏移超出最大分页数
                if ($this->getPage() + $pageoffset >= $page_total + 1) {
                    $init = $page_total - $pagelen + 1;
                } else {
                    //左右偏移都存在时的计算
                    $init = $this->getPage() - $pageoffset;
                    $max = $this->getPage() + $pageoffset;
                }
            }
        }

        $page_code = '';
        $uri = Lb::app()->getUri();
        if ($this->getPage() > 1) {
            $page_code .= "<li>
      <a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $this->getPage() - 1]) . "\" aria-label=\"Previous\">
        <span aria-hidden=\"true\">&laquo;</span>
      </a>
    </li>";
        }
        for ($i = $init; $i <= $max; ++$i) {
            if ($this->getPage() != $i) {
                $page_code .= "<li><a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $i]) . "\">{$i}</a></li>";
            } else {
                $page_code .= "<li class=\"active\"><a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $i]) . "\">{$i} <span class=\"sr-only\">(current)</span></a></li>";
            }
        }
        if ($this->getPage() < $page_total) {
            $page_code .= "<li>
      <a href=\"" . Lb::app()->createAbsoluteUrl($uri, ['page' => $this->getPage() + 1]) . "\" aria-label=\"Next\">
        <span aria-hidden=\"true\">&raquo;</span>
      </a>
    </li>";
        }

        return sprintf($pagination_tpl, $page_code);
    }
}
