<?php

namespace lb\tests\components\widget;

use lb\components\widget\Pagination;
use lb\tests\BaseTestCase;

class PaginationTest extends BaseTestCase
{
    public function testRender()
    {
        $expectedPagination = <<<EOF
<nav>
  <ul class="pagination">
    <li><a href="http://www.baidu.com?page=1" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li><li><a href="http://www.baidu.com?page=1">1</a></li><li class="active"><a href="http://www.baidu.com?page=2">2 <span class="sr-only">(current)</span></a></li><li><a href="http://www.baidu.com?page=3">3</a></li><li><a href="http://www.baidu.com?page=4">4</a></li><li><a href="http://www.baidu.com?page=3" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
  </ul>
</nav>
EOF;


        $this->assertEquals($expectedPagination, Pagination::component()->setUrl('http://www.baidu.com')
            ->setPage(2)
            ->setDataTotal(31)
            ->render());
    }
}
