<?php


namespace layui;

use think\Paginator;

class Layui extends Paginator
{
    protected $uri;
    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "上一页")
    {
        
        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }
        
        $url = $this->url(
            $this->currentPage() - 1
        );
        
        return $this->getPageLinkWrapper($url, $text);
    }
    
    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '下一页')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }
        
        $url = $this->url($this->currentPage() + 1);
        
        return $this->getPageLinkWrapper($url, $text);
    }
    
    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple)
            return '';
        
        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];
        
        $side   = 1;
        $window = $side;
        
        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            $block['last']  = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['last']  = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
        } else {
            $block['first']  = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last']   = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }
        
        $html = '';
        
        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }
        
        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }
        
        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }
        
        return $html;
    }
    
    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<ul class="pager">%s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                );
            } else {
                return sprintf(
                    '<div class="layui-laypage">%s %s %s %s %s</div>',
                    $this->getTotal($this->total),
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getNextButton(),
                    $this->goPage()
                );
            }
        }
    }
    
    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<a href="' . htmlentities($url) . '">' . $page . '</a>';
    }
    
    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<a class="layui-laypage-prev layui-disabled" >' . $text . '</a>';
    }
    
    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>' . $text . '</em></span>';
    }
    
    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }
    
    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';
        
        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }
        
        return $html;
    }
    
    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }
        
        return $this->getAvailablePageWrapper($url, $page);
    }
    
    /**
     *  生成总条数
     * @param $num
     * @return string
     */
    protected function getTotal($num)
    {
        return '<div id="pages" class="layui-box layui-laypage layui-laypage-molv"><span class="rows">共'. $num.'条记录</span></div>';
    }
    
    /**
     * 跳转
     * @return string
     */
    protected function goPage()
    {
        
        $this->getUri();
        //return '<span class="layui-laypage-skip">到第<input type="text" min="1" value="1" οnkeydοwn="javascript:if(event.keyCode==13){var page=(this.value>'.$this->lastPage.')?'.$this->lastPage.':this.value;location=\''.$this->uri.'page=\'+page+\'\'}" class="layui-input" ><button type="button" class="layui-laypage-btn" οnclick="javascript:var page =(this.previousSibling.value > '.$this->lastPage.') ? '.$this->lastPage.': this.previousSibling.value;location=\''.$this->uri.'page=\'+page+\'">确定</button></span>';
    }
    
    
    /**
     * 获取url
     */
    private function getUri(){
        $url=$_SERVER["REQUEST_URI"].(strpos($_SERVER["REQUEST_URI"], '?')?'':"?");
        $parse=parse_url($url);
        if(isset($parse["query"])){
            parse_str($parse['query'],$params);
            unset($params["page"]);
            $url=$parse['path'] . '?' . http_build_query($params) .'&';
        }else{
            $url=$parse['path'] . '?';
        }
        $this->uri = $url;
        
    }
}
