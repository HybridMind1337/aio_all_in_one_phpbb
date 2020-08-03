<?php
namespace hybridmind\aio\event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class listener implements EventSubscriberInterface
{
    
    protected $config;
    protected $template;
    protected $user;
    protected $db;
    
    public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db)
    {
        
        $this->config   = $config;
        $this->template = $template;
        $this->user     = $user;
        $this->db       = $db;
    }
    
    static public function getSubscribedEvents()
    {
        return array(
            'core.ucp_register_data_before' => 'checker_acc',
            'core.viewtopic_assign_template_vars_before' => 'check_userpost',
            'core.viewtopic_get_post_data' => 'check_post_list'
        );
    }
    public function checker_acc($event)
    {
        global $request;
        $useripz     = $request->server('REMOTE_ADDR');
        $sql2        = "SELECT user_ip FROM phpbb_users WHERE user_ip='$useripz'";
        $result2     = $this->db->sql_query($sql2);
        $forum_data2 = $this->db->sql_fetchrow($result2);
        $this->db->sql_freeresult($result2);
        
        if ($user->data['is_registered'] || isset($_REQUEST['not_agreed']) || $forum_data2 > 0) {
            trigger_error("<div class='rules'>Вече имаш регистация с този IP адресс: $useripz</div>");
        }
        
    }
    public function check_userpost($event)
    {
        global $user;
        
        if ($user->data['user_id'] == ANONYMOUS) {
            $event['total_posts'] = 1;
        }
        
    }
    
    
    public function check_post_list($event)
    {
        global $user;
        
        if ($user->data['user_id'] == ANONYMOUS) {
            $event['post_list'] = array_slice($event['post_list'], 0, 1);
        }
    }
} 