<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 直播房间竞猜模型
 * 
 */
class Liaoqiu_prizes_model extends CI_Model 
{

    /**
     * 表名
     * 
     * @var string
     */
    const TABLE_NAME = '';
    
    /**
     * 比分类型：全场比分
     * 
     * @var int
     */
    const TYPE_FULL_SCORE = 1;
    
    /**
     * 比分类型：进球数
     * 
     * @var int
     */
    const TYPE_GLOBAL_NUMBER = 2;
    
    /**
     * 比分类型：球员类
     * 
     * @var int
     */
    const TYPE_PLAYER = 3;
    
    /**
     * 比分类型：球队类
     * 
     * @var int
     */
    const TYPE_BALL = 4;
    
    /**
     * 比分类型：人工设置
     * 
     * @var int
     */
    const TYPE_OTHER = 5;
    
    public $prizes_types = array(
      self::TYPE_FULL_SCORE => '全场比分',
      self::TYPE_GLOBAL_NUMBER => '进球类',
      self::TYPE_PLAYER => '球员类',
      self::TYPE_BALL => '球队类',
      self::TYPE_OTHER => '人工设置',
    );
    
    /**
     *全场比分选项
     * 
     * @var array 
     */
    public $options_full_score = array(
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => '5或以上',
    );
    
    public $options_global_number = array(
        
    );
    public function __construct()
    {
        $this->load->database();
    }
    
}
