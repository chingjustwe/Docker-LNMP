<?php
/**
 * 评论过滤器
 * 
 * @package CommentFilter
 * @author Hanny
 * @version 1.0.2
 * @link http://www.imhan.com

 * 历史版本
 * version 1.0.2 at 2010-05-16
 * 修正发表评论成功后，评论内容Cookie不清空的Bug
 *
 * version 1.0.1 at 2009-11-29
 * 增加IP段过滤功能
 *
 * version 1.0.0 at 2009-11-14
 * 实现评论内容按屏蔽词过滤功能
 * 实现过滤非主文评论功能
 *
 */
class CommentFilter_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {    
        Typecho_Plugin::factory('Widget_Feedback')->comment = array('CommentFilter_Plugin', 'filter');
		return _t('评论过滤器启用成功，请配置需要过滤的内容');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
	{
        $opt_ip = new Typecho_Widget_Helper_Form_Element_Radio('opt_ip', array("none" => "无动作", "waiting" => "标记为待审核", "spam" => "标记为垃圾", "abandon" => "评论失败"), "none",
			_t('屏蔽IP操作'), "如果评论发布者的IP在屏蔽IP段，将执行该操作");
        $form->addInput($opt_ip);

        $words_ip = new Typecho_Widget_Helper_Form_Element_Textarea('words_ip', NULL, "0.0.0.0",
			_t('屏蔽IP'), _t('多条IP请用换行符隔开<br />支持用*号匹配IP段，如：192.168.*.*'));
        $form->addInput($words_ip);

        $opt_nocn = new Typecho_Widget_Helper_Form_Element_Radio('opt_nocn', array("none" => "无动作", "waiting" => "标记为待审核", "spam" => "标记为垃圾", "abandon" => "评论失败"), "none",
			_t('非中文评论操作'), "如果评论中不包含中文，则强行按该操作执行");
        $form->addInput($opt_nocn);

        $opt_ban = new Typecho_Widget_Helper_Form_Element_Radio('opt_ban', array("none" => "无动作", "waiting" => "标记为待审核", "spam" => "标记为垃圾", "abandon" => "评论失败"), "abandon",
			_t('禁止词汇操作'), "如果评论中包含禁止词汇列表中的词汇，将执行该操作");
        $form->addInput($opt_ban);

        $words_ban = new Typecho_Widget_Helper_Form_Element_Textarea('words_ban', NULL, "fuck\n操你妈\n[url\n[/url]",
			_t('禁止词汇'), _t('多条词汇请用换行符隔开'));
        $form->addInput($words_ban);

        $opt_chk = new Typecho_Widget_Helper_Form_Element_Radio('opt_chk', array("none" => "无动作", "waiting" => "标记为待审核", "spam" => "标记为垃圾", "abandon" => "评论失败"), "waiting",
			_t('敏感词汇操作'), "如果评论中包含敏感词汇列表中的词汇，将执行该操作");
        $form->addInput($opt_chk);

        $words_chk = new Typecho_Widget_Helper_Form_Element_Textarea('words_chk', NULL, "http://",
			_t('敏感词汇'), _t('多条词汇请用换行符隔开<br />注意：如果词汇同时出现于禁止词汇，则执行禁止词汇操作'));
        $form->addInput($words_chk);
	}
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 评论过滤器
     * 
     */
    public static function filter($comment, $post)
    {
        $options = Typecho_Widget::widget('Widget_Options');
		$filter_set = $options->plugin('CommentFilter');
		$opt = "none";
		$error = "";

		//屏蔽IP段处理
		if ($opt == "none" && $filter_set->opt_ip != "none") {
			if (CommentFilter_Plugin::check_ip($filter_set->words_ip, $comment['ip'])) {
				$error = "评论发布者的IP已被管理员屏蔽";
				$opt = $filter_set->opt_ip;
			}			
		}
		//纯中文评论处理
		if ($opt == "none" && $filter_set->opt_nocn != "none") {
			if (preg_match("/[\x{4e00}-\x{9fa5}]/u", $comment['text']) == 0) {
				$error = "评论内容请不少于一个中文汉字";
				$opt = $filter_set->opt_nocn;
			}
		}
		//检查禁止词汇
		if ($opt == "none" && $filter_set->opt_ban != "none") {
			if (CommentFilter_Plugin::check_in($filter_set->words_ban, $comment['text'])) {
				$error = "评论内容中包含禁止词汇";
				$opt = $filter_set->opt_ban;
			}
		}
		//检查敏感词汇
		if ($opt == "none" && $filter_set->opt_chk != "none") {
			if (CommentFilter_Plugin::check_in($filter_set->words_chk, $comment['text'])) {
				$error = "评论内容中包含敏感词汇";
				$opt = $filter_set->opt_chk;
			}
		}

		//执行操作
		if ($opt == "abandon") {
			Typecho_Cookie::set('__typecho_remember_text', $comment['text']);
            throw new Typecho_Widget_Exception($error);
		}
		else if ($opt == "spam") {
			$comment['status'] = 'spam';
		}
		else if ($opt == "waiting") {
			$comment['status'] = 'waiting';
		}
		Typecho_Cookie::delete('__typecho_remember_text');
        return $comment;
    }

    /**
     * 检查$str中是否含有$words_str中的词汇
     * 
     */
	private static function check_in($words_str, $str)
	{
		$words = explode("\n", $words_str);
		if (empty($words)) {
			return false;
		}
		foreach ($words as $word) {
            if (false !== strpos($str, trim($word))) {
                return true;
            }
		}
		return false;
	}

    /**
     * 检查$ip中是否在$words_ip的IP段中
     * 
     */
	private static function check_ip($words_ip, $ip)
	{
		$words = explode("\n", $words_ip);
		if (empty($words)) {
			return false;
		}
		foreach ($words as $word) {
			$word = trim($word);
			if (false !== strpos($word, '*')) {
				$word = "/^".str_replace('*', '\d{1,3}', $word)."$/";
				if (preg_match($word, $ip)) {
					return true;
				}
			} else {
				if (false !== strpos($ip, $word)) {
					return true;
				}
			}
		}
		return false;
	}
}
