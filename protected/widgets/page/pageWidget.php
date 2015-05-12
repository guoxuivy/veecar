<?php
namespace page;
use Ivy\core\Widget;
use Ivy\core\CException;
class PageWidget extends Widget
{
    
	public function run($data) {
        $tag='page';//分页参数
		$res = $this->_show($data,$tag);
		return $res;
	}
    /**
     * 分页渲染
     * @param  array &$data [description]
     * @param  string $tag   [description]
     * @return string
     */
    public function _show($data,$tag){
        if((!is_array($data)) || (empty($tag))) throw new CException(500,'分页无效参数！');
        $g_row=@(int)$_GET['row'];
        $data=$data['data']['pagener'];
        $str='<div class="fjgl_th_for clear">';
            if ($data['pageNums']>1) {
                if ($data['currentPage']!=1) {
                    $str.="<a href=\"".$this->getCUrl($tag,$data['currentPage']-1)."\" class=\"syy\">上一页</a>";
                }
                foreach ($data['linkList'] as $key => $value) {
                    $str.="<a href=\"".$this->getCUrl($tag,$value)."\" ".($data['currentPage']==$value?'class="fenye"':'').">{$value}</a>";
                }
                if (end($data['linkList'])<$data['pageNums']) {
                    $str.='<a href="javascript:">...</a>';
                    $str.='<a href="'.$this->getCUrl($tag,$data['pageNums']).'">'.$data['pageNums'].'</a>';
                }
                if ($data['pageNums']>$data['currentPage']) {
                    $str.='<a href="'.$this->getCUrl($tag,$data['currentPage']+1).'" class="syy">下一页</a>';
                }
                 $str.='<span style=" margin-left:10px;">共&nbsp;'.$data['pageNums'].'&nbsp;页&nbsp;&nbsp;到第</span>
                 <input name="topage" class="js_topage" type="text">页
                 <a href="javascript:" class="syy012 js_go" tag="'.$tag.'">GO</a> &nbsp;&nbsp;&nbsp;';
            }
        $str.='每页显示
                 <select class="syy013 js_row">
                 <option '.($g_row==20?'selected="selected"':'').' value=20>20</option>
                 <option '.($g_row==40?'selected="selected"':'').' value=40>40</option>
                 <option '.($g_row==60?'selected="selected"':'').' value=60>60</option>
                 <option '.($g_row==80?'selected="selected"':'').' value=80>80</option>
                 </select>&nbsp;条
                 </div>';
        return $str;
    }

    /**
     * 获取当前url并替换
     * @return [type] [description]
     */
    public function getCUrl($tag,$page=1){
        $param=(array)$_GET;
        unset($param['r']);
        $param[$tag]=$page;
        if(isset($param['t_search'])){
            foreach ($param['t_search'] as $key => $value) {
                $param['t_search['.$key.']']=$value;
            }
            unset($param['t_search']);
        }
    	return $this->url(implode('/',\Ivy::app()->_route->getRouter()),$param);
    }


}