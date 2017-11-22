<?php defined('SYSPATH') or die('No direct script access.');
class cine{

    /*
     *将大于新权重的数据id全部增加一
     *$table_name 单数形式 表名
     *$id 增加或修改数据 id
     *$new_weight 新数据权重
    */
	public static function weight($table_name, $id, $new_weight)
	{
        $if_exist = ORM::factory($table_name)->where(array('n_weight'=>$new_weight))->find()->id;
        $plural = Inflector::plural($table_name);
        if(!empty($if_exist)) //存在相同权重
        {
            $weight_add = new Database();
            $sql = 'update '.$plural.' set n_weight = n_weight + 1 where n_weight >= '.$new_weight.' and id != '.$id;
            $weight_add->query($sql);
        }
    }

    public static function upload_image($files, $dir, $width='', $height='', $if_crop = false)
    {
        $demand_rate = $width/$height;
        if(!empty($files['upload_pic']['name']))
        {
            $files = Validation::factory($files)->add_rules('upload_pic', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[2M]');
            if ($files->validate())
            {
                $filename = upload::save('upload_pic');
                $v_pic = $dir.basename($filename);
                $images = Image::factory($filename);
                $raw_rate = $images->width/$images->height;

                if($demand_rate>$raw_rate)
                {
                    $width = $images->width>$width?$width:$images->width;
                    $images->resize($width, $height, Image::WIDTH);
                }
                else
                {
                    $height = $images->height>$height?$height:$images->height;
                    $images->resize($width, $height, Image::HEIGHT);
                }
                if($if_crop)
                {
                    $images->crop($width, $height);
                }
                $images->save($v_pic);
                unlink($filename);
                return $v_pic;
            }
        }
    }

    public static function small_image($files, $dir, $width='', $height='')
    {
        if(!empty($files))
        {
            $demand_rate = $width/$height;

            $v_pic = $dir.'small_'.basename($files);

            $images = Image::factory($files);

            $raw_rate = $images->width/$images->height;

            if($demand_rate>$raw_rate)
            {
                $images->resize($width, $height, Image::WIDTH);
            }
            else
            {
                $images->resize($width, $height, Image::HEIGHT);
            }
            $images->crop($width, $height);
            $images->save($v_pic);
        }
        return $v_pic;
    }

    public static function width_image($files, $dir, $width='', $height='')
    {
        if(!empty($files))
        {
            $v_pic = $dir.'small_'.basename($files);
            $images = Image::factory($files);
            $images->resize($width, $height, Image::WIDTH);
            $images->save($v_pic);
        }
        return $v_pic;
    }

    //上传原始图片
    public static function original_image($files, $dir)
    {
        if(!empty($files['upload_pic']['name']))
        {
            $files = Validation::factory($files)->add_rules('upload_pic', 'upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[10M]');
            if ($files->validate())
            {
                $filename = upload::save('upload_pic');
                $new_name = time().'_'.rand(10000, 99999).'.jpg';
                $v_pic = $dir.$new_name;
                $images = Image::factory($filename);
                $images->save($v_pic);
                unlink($filename);
            }
        }
        return $v_pic;
    }


    //上传原始图片
    public static function original_images($files, $dir, $name='')
    {
        if(!empty($files[$name]['name']))
        {
            $files = Validation::factory($files)->add_rules($name, 'upload::valid', 'upload::type[gif,jpg,jpeg,png]', 'upload::size[10M]');
            if ($files->validate())
            {
                $filename = upload::save($name);
                $v_pic = $dir.basename($filename);
                $images = Image::factory($filename);
                $images->save($v_pic);
                unlink($filename);
            }
        }
        return $v_pic;
    }

    //上传原始图片
    public static function upqiniu($files, $dir, $name='')
    {
        if(!empty($files[$name]['name']))
        {
            $files = Validation::factory($files)->add_rules($name, 'upload::valid', 'upload::type[gif,jpg,png,jpeg]', 'upload::size[10M]');
            if ($files->validate())
            {
                $filename = upload::save($name);
                $v_pic = $dir.basename($filename);
                $images = Image::factory($filename);
                $images->save($v_pic);

                $qbox = new Qbox();
                $key = $qbox->upload($v_pic, $v_pic);
                $file_url = 'http://77fkxu.com1.z0.glb.clouddn.com/'.$key;

                unlink($filename);
            }
        }
        return $file_url;
    }




    public static function mod_image($file, $dir, $width, $height, $type = '', $if_crop=true)
    {
        if(!empty($file))
        {
            $demand_rate = $width/$height;

            $v_pic = $dir.$type.basename($file);

            $images = Image::factory($file);

            $raw_rate = $images->width/$images->height;

            if($demand_rate>$raw_rate)
            {
                $width = $images->width>$width?$width:$images->width;
                $images->resize($width, $height, Image::WIDTH);
            }
            else
            {
                $height = $images->height>$height?$height:$images->height;
                $images->resize($width, $height, Image::HEIGHT);
            }
            if($if_crop)
            {
                $images->crop($width, $height);
            }
            $images->save($v_pic);
        }
        return $v_pic;
    }


    public static function upload_images($files, $dir, $width='', $height='', $s_width, $s_height)
    {
        $image_urls = array();
		$files = Validation::factory($files)
			->add_rules('upload_pic', 'upload::valid', 'upload::type[gif,jpg,png]', 'upload::size[10M]');
		if ($files->validate())
		{
			foreach( arr::rotate($files['upload_pic']) as $file )
			{
				if(!empty($file['name']))
				{
                    $filename = upload::save($file);
                    $big_pic = self::mod_image($filename, $dir, $width, $height, 'b_', false);
                    $small_pic =self::mod_image($filename, $dir, $s_width, $s_height, 's_');
                    $image_urls['big'][] = $big_pic;
                    $image_urls['small'][] = $small_pic;
					unlink($filename);
				}

			}
		}

        return $image_urls;
    }

    public static function send_mail_test($data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).'/sendcloud_cacert.pem');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send.json');
        //不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            array('api_user' => 'paipianbang',
            'api_key' => 'cWerGy5PhsIc1Kaf',
            'from' => 'admin@107cine.com',
            'fromname' => '影视工业网',
            //'to' => 'tysun2007@qq.com;tysun2002@gmail.com',
            'to' => $data['to'],
            'subject' => $data['subject'],
            'html' => $data['html'],
            )
        );
        $result = curl_exec($ch);
        if($result === false) //请求失败
        {
            echo curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }


    public static function send_mail($data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
        curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).'/sendcloud_cacert.pem');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send.json');
        //不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            array('api_user' => 'postmaster@cinetrigger.sendcloud.org',
            'api_key' => 'cWerGy5PhsIc1Kaf',
            'from' => 'admin@107cine.com',
            'fromname' => '影视工业网',
            //'to' => 'tysun2007@qq.com;tysun2002@gmail.com',
            'to' => $data['to'],
            'subject' => $data['subject'],
            'html' => $data['html'],
            )
        );
        $result = curl_exec($ch);
        if($result === false) //请求失败
        {
            echo curl_error($ch);
            $a = curl_error($ch);
            $data = array('t_content'=>$a);
            ORM::factory('temp_content')->add($data);
        }
        curl_close($ch);
        return $result;
    }

    //获取真实ip
    public static function realip()
    {
        $realip = '';
        if (isset($_SERVER))
        {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }
            else if (isset($_SERVER["HTTP_CLIENT_IP"]))
            {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            }
            else
            {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        }
        else
        {
            if (getenv("HTTP_X_FORWARDED_FOR"))
            {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            }
            else if (getenv("HTTP_CLIENT_IP"))
            {
                $realip = getenv("HTTP_CLIENT_IP");
            }
            else
            {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }

    public static function getsub()
    {
        $base_host = '107cine.com';
        $host = $_SERVER['HTTP_HOST'];
        $pos = strpos($host, $base_host);
        $sub = ($pos>0) ? substr($host, 0, $pos-1) : '';
        return $sub;
    }

    public static function stream_des($content='')
    {
        $content = stripslashes($content);
        preg_match('|<img(.*?)src="(.*?)(?=")(.*?)>|',$content,$out);
        $pic = $out[2];
        $height = '100';
        if(strpos($pic,'qiniu'))
        {

            //$info = file_get_contents( $pic.'?imageInfo' );
            //$obj = json_decode($info);
            //$pic = ($obj->height > 100) ? $pic.'?imageView/0/h/100' : $pic;
            //$height = ($obj->height > 100) ? '100' : $obj->height;

        }
        $content = strip_tags($content);
        $content = mb_substr($content, 0, 100);
        $re = array('content'=>$content, 'pic'=>$pic, 'height'=>$height);
        return $re;
    }

    //支持数组，对象的addslashes
    public static function alladdslashes($mixed)
    {
        if(is_array($mixed))
        {
            $new = array();
            foreach($mixed as $key=>$value)
            {
                $mixed[$key] = trim(addslashes($value));
            }
        }
        else if(is_object($mixed))
        {
            foreach($mixed as $key=>$value)
            {
                $mixed->$key = trim(addslashes($value));
            }
        }
        else
        {
            $mixed = trim(addslashes($mixed));
        }
        return $mixed;
    }

    public function getprofile($member_id)
    {
        $base = ORM::factory('member_base')->where(array('member_id'=>$member_id))->find();
        $works = ORM::factory('member_work')->where(array('member_id'=>$member_id))->orderby('id','desc')->find_all();
        $educations = ORM::factory('member_education')->where(array('member_id'=>$member_id))->orderby('id','desc')->find_all();
        $careers = ORM::factory('member_career')->where(array('member_id'=>$member_id))->orderby('id','desc')->find_all();

        $work_array = array();$i = 0;
        foreach($works as $work)
        {
            if($i>5) break;
            $work_array[] = $work->work_name;
        }
        $work_text = implode(', ', $work_array);

        $education = ORM::factory('member_education')->where(array('member_id'=>$member_id))->orderby('begin_year', 'desc')->limit(1)->find();
        if(!empty($education->id))
        {
            $education_text = $education->end_year.'年毕业于'.$education->v_school.$education->v_special;
        }

        $career = ORM::factory('member_career')->where(array('member_id'=>$member_id))->orderby('begin_year', 'desc')->limit(1)->find();
        if(!empty($career->id))
        {
            if($base->current_status == '在职')
            {
                $career_text = '现就职于'.$career->v_company;
            }
            else
            {
                $career_text = '曾就职于'.$career->v_company;
            }
        }
        return array($work_text, $education_text, $career_text);
    }

    public function getbusiness($ids)
    {
        $business = array();
        if(!empty($ids))
        {
            $id_array = explode(',', $ids);
            $count = count($id_array);
            for( $i=0; $i<$count; $i++ )
            {
                $business[] = ORM::factory('company_type')->find($id_array[$i])->v_name;
            }
        }
        $business = implode(',', $business);
        return $business;
    }

    //友好显示时间
    public static function newtime($d_time)
    {
        $now = time();
        $last_time = strtotime($d_time);
        $diff = $now - $last_time;
        if($diff < 60)
        {
            $text = '刚刚';
        }
        else if($diff < 3600 )
        {
            $minute = floor($diff/60);
            $text = $minute.'分钟前';
        }
        else
        {
            $thisyear = date('Y');
            $year = substr($d_time, 0, 4);
            if($year != $thisyear)
            {
                $text = substr($d_time, 0, 16);
            }
            else
            {
                $last_day = date('Ymd',$last_time);
                $thisday = date('Ymd');
                if($last_day == $thisday)
                {
                    $text = '今天 '.date('H:i', $last_time);
                }
                else
                {
                    $text = date('n月j日 H:i', $last_time);
                }
            }
        }
        return $text;

    }

    public function changeid($member_id)
    {
        $link = ORM::factory('app_link')->where(array('member_id'=>$member_id))->find();
        $app_id = empty($link->app_member_id) ? $member_id : $link->app_member_id;
        return $app_id;
    }


    //发送短信
    public static function senddx($to, $data, $tempId)
    {
        if (self::isMobile($to)) {
            $new = new Sendmessage;
            $new->temp($to, $data, $tempId);
        }
    }
    //验证手机号
    public function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }


    //根据年月获取组合字符串
    public static function get_ym($y, $m, $next=false)
    {
        if ($m == 12) {
            $end_ym = ($y+1).'-01';
        }
        elseif ($m < 9) {
            $end_ym = $y.'-0'.($m+1);
        }
        else{
            $end_ym = $y.'-'.($m+1);
        }
        $m = (strlen($m)==1) ? '0'.$m : $m;
        $ym = $y.'-'.$m;
        return $next ? $end_ym : $ym;
    }

    public static function setcookie($member_id)
    {
        $cookie_params = array(
                       'name'   => 'member_id',
                       'value'  => $member_id,
                       'expire' => '2678400',
                       'domain' => '.107cine.com',
                       'path'   => '/'
                       );
        cookie::set($cookie_params);

        $msdm = md5($member_id.'107cine.com'.time());
        $cookie_params = array(
                       'name'   => 'msdm',
                       'value'  => $msdm,
                       'expire' => '2678400',
                       'domain' => '.107cine.com',
                       'path'   => '/'
                       );
        cookie::set($cookie_params);
        $member = ORM::factory('member')->find($member_id);
        if ($member->id) {
            $member->msdm = $msdm;
            $member->save();
        }
    }


    /*
    * 生成翻页用的offset
    */
    public static function offset($perpage, $page)
    {
        $perpage = intval($perpage);
        $page = intval($page);
        $page = ($page < 1) ? 1 : $page;
        $offset = ($page - 1) * $perpage;
        return $offset;
    }


    //时间格式化
    public static function format_time($start_time, $end_time)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        $date = date('m-d', $start_time);
        $start_time = date('H:i', $start_time);
        $end_time = date('H:i', $end_time);
        $time = $start_time.'-'.$end_time;
        return $date.' '.$time;
    }

    //状态判定
    public static function status($start_time, $end_time)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        $now = time();
        if ($now < $start_time) {
            $status = 'wait';
            $status_text = '了解我';
        }
        elseif ($now > $start_time and $now < $end_time) {
            $status = 'living';
            $status_text = '直播中';
        }
        else {
            $status = 'return';
            $status_text = '看回放';
        }
        return $status;
    }

    public static function encode_json($return)
    {
        echo json_encode($return);
        // echo json_encode($return, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function jsapi($params)
    {
        require_once "application/libraries/wxpaylib/WxPay.Api.php";
        require_once "application/libraries/wxpaylib/WxPay.JsApiPay.php";
        require_once 'application/libraries/wxpaylib/WxPay.Notify.php';
        require_once 'application/libraries/wxpaylib/WxPay.NativePay.php';
        require_once 'application/libraries/wxpaylib/phpqrcode/phpqrcode.php';
        require_once "application/libraries/wxpaylib/log.php";

        //①、获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        $body = $params['body'];
        $attach = $params['attach'];
        $outer_order_id = $params['outer_order_id'];
        $orderamount = $params['orderamount'];
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no($outer_order_id);
        $input->SetTotal_fee($orderamount*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("ppxy");
        $input->SetNotify_url("http://107cine.com/pay/wx_notify");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        // echo '<font color="#f00"><b>'.$product->v_title.': ¥'.$orderamount.'</b></font><br/>';
        return $tools->GetJsApiParameters($order);

    }


    /*
    * 为广告生成代码
    */
    public static function url_for_ads($params)
    {
        $timestamp = microtime(true);
        $sign = self::create_sign($timestamp);
        $url = 'http://107cine.com/ads/url?timestamp='.$timestamp.'&sign='.$sign;
        if (!empty($params['id'])) {
            $url .= '&id='.$params['id'];
        }
        if (!empty($params['page'])) {
            $url .= '&page='.$params['page'];
        }
        if (!empty($params['from'])) {
            $url .= '&from='.$params['from'];
        }
        return $url;
    }

    public static function create_sign($timestamp)
    {
        $key = Kohana::config('ajax.token_key');
        $sign = md5($key.$timestamp);
        return $sign;
    }

    public static function UUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
    }


    public static function listtable($fields, $objs, $array=array())
    {
        $th = array_keys($fields);
        self::tables('start');
        self::table_tr($th, 'th');
        foreach ($objs as $key => $obj) {
            $td = array();
            foreach ($fields as $key => $value) {
                eval('$td[] = '.$value.';');
            }
            self::table_tr($td, 'td');
        }
        self::tables('end');
    }

    protected function tables($type)
    {
        echo ($type=='start') ? '<table class="listable tabled" cellspacing="1" cellpadding="0" border="0">' : '</table>';
    }
    protected function table_tr($values, $type)
    {
        echo '<tr>';
        foreach ($values as $key => $value) {
            echo '<'.$type.'>'.$value.'</'.$type.'>';
        }
        echo '</tr>';
    }


    // 二维码生成
    public static function qrpic($url, $pic_name)
    {
        $pic = 'uploads/qrpic_mall/'.$pic_name.'.jpg';
        if(!file_exists($pic))
        {
            $apiurl = 'http://api.wwei.cn/wwei.html?data='.$url.'/&version=1.0&apikey=20150808159545';
            $img = @file_get_contents($apiurl);
            $img = json_decode($img);
            $str = $img->data->qr_filepath;
            $true_img = @file_get_contents($str);
            @file_put_contents('uploads/qrpic_mall/'.$pic_name.'.jpg',$true_img);
        }
        return $pic;
    }

    public static function alexa($url)
    {
        $url = 'http://data.alexa.com/data/+wQ411en8000lA?cli=10&dat=snba&ver=7.0&cdt=alx_vw=20&wid=12206&act=00000000000&ss=1680×1050&bw=964&t=0&ttl=35371&vis=1&rq=4&url='.$url;
        $xml = file_get_contents($url);

        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $world  = $vals[6]['attributes']['TEXT'];
        $cn     = $vals[9]['attributes']['RANK'];
        return array('world'=>$world, 'cn'=>$cn);
    }


    // poly code生成
    public static function polytoken($videoId)
    {
        $userId = '7ccb0fb086';       // polyv 提供的服务器间的通讯验证
        $secretkey = 'E8pt1iFFfr';     // polyv 提供的接口调用签名访问的key

        $ts = time() * 1000;      // 时间戳
        $viewerIp = self::get_client_ip();  // 用户 ip
        $viewerId = '12345';      // 自定义用户 id
        $viewerName = 'testUser';  // 用户昵称
        $extraParams = 'HTML5';  // 自定义参数

        /* 将参数 $userId、$secretkey、$videoId、$ts、$viewerIp、$viewerIp、$viewerId、$viewerName、$extraParams
            按照ASCKII升序 key + value + key + value ... +value 拼接
        */
        $concated =  'extraParams'.$extraParams.'ts'.$ts.'userId'.$userId.'videoId'.$videoId.'viewerId'.$viewerId.'viewerIp'.$viewerIp.'viewerName'.$viewerName;

        // 再首尾加上 secretkey
        $plain = $secretkey.$concated.$secretkey;
        // 取大写MD5
        $sign = strtoupper(md5($plain));

        // 然后将下列参数用post请求  https://hls.videocc.net/service/v1/token 获取 token
        $url = 'https://hls.videocc.net/service/v1/token';
        $data = array('userId' => $userId, 'videoId' => $videoId, 'ts' => $ts, 'viewerIp' => $viewerIp, 'viewerName' => $viewerName, 'extraParams' => $extraParams, 'viewerId' => $viewerId, 'sign' => $sign);

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // 获取返回结果的 token, 再传入 playsafe 中播放加密视频
        $token = json_decode($result)->data->token;
        return $token;
    }

    public static function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }
        return $ipaddress;
    }

    public static function pic($src, $width=100)
    {
        return '<img src="'.$src.'" width="'.$width.'">';
    }

}
