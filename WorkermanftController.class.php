<?php

namespace Home\Controller;
use Think\Server;
use Common\Common;
use Workerman\Worker;
//use GatewayWorker\Gateway;

header('content-type:text/html;charset=utf-8');

class WorkermanftController extends Server{
  // protected $gateway = new Gateway("tcp://0.0.0.0:10086");

	//protected $socket = "Websocket://0.0.0.0:10086";
protected $socket = 'websocket://0.0.0.0:10086';
	
	/*添加定时器
	 *监控连接状态
	 * */
	 
	public function onWorkerStart(){
	    $beginToday=strtotime('09:00:00');
		$endToday=strtotime("23:54:59");
		$jsft_state = M("fn_open")->where("type='1'")->limit(0,1)->order("id desc")->find();
	//	$data =  jsft_format($caiji);
		
		$time_interval = 1;
	//	$jsft_state = json_decode($data,true);
		$nexttime = strtotime($jsft_state['next_time'])-strtotime($jsft_state['time']);
		
	   // F('jsft_state',null);
		if(!F('jsft_state')){
			F('jsft_state',$jsft_state);
		}
		if(!F('is_send')){
			F('is_send',1);
		}
		
			//ping 统计人数
		\Workerman\Lib\Timer::add(1, function(){
        	//ping客户端(获取房间内所有用户列表 )
            
		
			$new_message = array(
				'type' => 'ping', 
				//'content' => $num,
				'time' => date('H:i:s'),
				'chat_id' => F('chat_id'),
			//	'open_info'=>json_encode($jsft_state)
			);
			//if($num!=F('online')){
				//F('online',$num);
				foreach ($this->worker->connections as $conn) {
					$conn -> send(json_encode($new_message));
				}
			//}
    	});
		
		//echo json_encode(F('jsft_state'))."\n";
		/*开奖*/
		\Workerman\Lib\Timer::add($time_interval, function(){
			
		    $beginToday=strtotime('09:00:00');
			$endToday=strtotime("23:54:59");
			F('game','jsft');
			$jsft_state = F('jsft_state');
			//$state = json_encode($jsft_state);
			if(!$jsft_state){
            $jsft_state = M("fn_open")->where("type=1")->limit(0,1)->order("id desc")->find();
		    
			}
			
			
			 $admin_headimg = M('fn_setting')->where('roomid = 188888')->getField('setting_robotsimg');
            $admin_head='https://wukonglc.com'.$admin_headimg;
			
			$next_time =strtotime($jsft_state['next_time']);
			$awardtime = $jsft_state['time'];
		
			$info_message = M("fn_infos")->where("game=1 and status=0")->select();
			//echo json_encode($info_message)."======================\n";
		//	echo $next_time-time();
			foreach($info_message as $k=>$v){
			    
			    if($next_time-time() == $v['times']){
			        if($v['type']==1){
    			        $content = '';
    			        // $pos1 = strpos($v['title'], "奖前消息1");
    			         
    			           $Arr_Str1 = $this->arr_split_zh('奖前消息1');
                                $Arr1 = $this->arr_split_zh($v['title']);
                                if($this->Str_Is_Equal($Arr_Str1,$Arr1)){
    			         
                        
                           // echo $pos2."type=1"."\n";
                                $con= M("fn_order")->where("type=1 and term={$jsft_state['next_term']}")->select();
                                $data = [];
                                $array = [];
                                foreach($con as $ks=>$vs) {
                                    if ($vs['status'] != '已撤单') {
                                        if (!isset($data[$vs['username']]['sum'])) {
                                            $data[$vs['username']]['sum'] = 0;
                                        }
                                        $data[$vs['username']]['sum'] += $vs['money'];
                                        $data[$vs['username']]['data'][$vs['mingci']][] = $vs;
                                    }
                                }
                                $txt = "竞猜列表核对:<br>";
                                foreach ($data as $key => $vv) {
                                    $txt .= "<br>[{$key}]汇总:{$vv['sum']}<br>";
                                    $d = $vv['data'];
                                    $txt .= $this->getcontent(isset($d['1']) ? $d['1'] : [], '冠军') . "
                            " . $this->getcontent(isset($d['和']) ? $d['和'] : [], '冠亚') . "
                            " . $this->getcontent(isset($d['2']) ? $d['2'] : [], '亚军') . "
                            " . $this->getcontent(isset($d['3']) ? $d['3'] : [], '第三名') . "
                            " . $this->getcontent(isset($d['4']) ? $d['4'] : [], '第四名') . "
                            " . $this->getcontent(isset($d['5']) ? $d['5'] : [], '第五名') . "
                            " . $this->getcontent(isset($d['6']) ? $d['6'] : [], '第六名') . "
                            " . $this->getcontent(isset($d['7']) ? $d['7'] : [], '第七名') . "
                            " . $this->getcontent(isset($d['8']) ? $d['8'] : [], '第八名') . "
                            " . $this->getcontent(isset($d['9']) ? $d['9'] : [], '第九名') . "
                            " . $this->getcontent(isset($d['0']) ? $d['0'] : [], '第十名');
                                } 
                                
                                $content=$txt;
                        }else{
                            $content=$v['content'];
                        }
    			       // echo $content;
    			        
    			        $message = array(
        					'game' => '1',
        					'headimg'=>$admin_head,
        					'content' => $content, 
        					'roomid' => '188888',
        					'addtime' => date('H:i:s'),
        					'userid'=>'1',
        					'username'=>'管理员',
        					'type'=>'U3'
        				    );
    			        //echo $content;
			        
			        $this->add_message($message);
			        
			        }elseif($v['type']==2){
			    //echo $next_time-time();
			         $txt1 = '';
			         //$pos2 = strpos($v['title'], "奖后消息1");
			                    $Arr_Str1 = $this->arr_split_zh('奖后消息1');
                                $Arr1 = $this->arr_split_zh($v['title']);
                        if($this->Str_Is_Equal($Arr_Str1,$Arr1)){
			         
                    //if($pos2){
                        //echo $pos2."type=2"."\n";
                            //select_query('fn_order', '*', "`roomid` = '{$roomid}' and `type` = {$type} and `term`='{$term}'");
                            $con=M("fn_order")->where("type=1 and term={$jsft_state['term']} and status > 0")->select();
                            $data1 = [];
                            foreach($con as $ks=>$vs) {
                                    if ($vs['status'] > 0) {
                                       // echo 222;
                                        if (!isset($data1[$vs['username']])) {
                                            $data1[$vs['username']]['earn'] = 0;
                                            $data1[$vs['username']]['money'] = 0;
                                        }
                                        //echo json_encode($vs['status']);
                                        //$data[$con['username']]['sum'] += $con['status'];
                                        $data1[$vs['username']]['earn'] += $vs['status'];
                                        $data1[$vs['username']]['money'] += $vs['status'] > 0 ? floatval($vs['money']) : 0;
                                        $data1[$vs['username']]['data'][$vs['mingci']][] = $vs;
                                    }
                            }
                            $txt1 = str_replace("{期号}",$jsft_state['term'],$v['content']);
                            
                           // $txt1 = "{$jsft_state['term']}期已开奖<br>中奖名单如下:<br>";
                            foreach ($data1 as $key => $vv) {
                                $sum = $vv['earn'] - $vv['money'];
                                $txt1 .= "<br>[{$key}]积分:{$sum}<br>";
                                $txt1 .=$v['money'];//改
                                $d = $vv['data'];
                                $txt1 .= $this->getcontent(isset($d['1']) ? $d['1'] : [], '冠军') . "
                        " . $this->getcontent(isset($d['和']) ? $d['和'] : [], '冠亚') . "
                        " . $this->getcontent(isset($d['2']) ? $d['2'] : [], '亚军') . "
                        " . $this->getcontent(isset($d['3']) ? $d['3'] : [], '第三名') . "
                        " . $this->getcontent(isset($d['4']) ? $d['4'] : [], '第四名') . "
                        " . $this->getcontent(isset($d['5']) ? $d['5'] : [], '第五名') . "
                        " . $this->getcontent(isset($d['6']) ? $d['6'] : [], '第六名') . "
                        " . $this->getcontent(isset($d['7']) ? $d['7'] : [], '第七名') . "
                        " . $this->getcontent(isset($d['8']) ? $d['8'] : [], '第八名') . "
                        " . $this->getcontent(isset($d['9']) ? $d['9'] : [], '第九名') . "
                        " . $this->getcontent(isset($d['0']) ? $d['0'] : [], '第十名');
                            }
                         
                    }else{
                                $Arr_Str1 = $this->arr_split_zh('奖后消息3');
                                $Arr_Str2 = $this->arr_split_zh('奖后消息44');
                                $Arr1 = $this->arr_split_zh($v['title']);
                                if($this->Str_Is_Equal($Arr_Str1,$Arr1)){ 
                                    $img = '<br/><a style="display:block;"><img src="http://gbapp.live/public/openhistory/1/1_'.$jsft_state['term'].'.jpg" width="100%" /></a>';
                                     $txt1 = $img;//str_replace("(选择)",$img,$v['content']);
                                }elseif($this->Str_Is_Equal($Arr_Str2,$Arr1)){
                                    $img = '<br/><a style="display:block;"><img src="http://gbapp.live/public/openhistory/1/1_'.$jsft_state['term'].'.jpg" width="100%"/></a>';
                                     $txt1 = $img;//str_replace("(选择)",$img,$v['content']);
                                }else{   
                        
                                $txt1=$v['content'];
                                }
                    
			        //
                    }
                        //echo $txt1;
			            $message = array(
        					'game' => '1',
        					'headimg'=>$admin_head,
        					'content' => $txt1, 
        					'roomid' => '188888',
        					'addtime' => date('H:i:s'),
        					'userid'=>'1',
        					'username'=>'管理员',
        					'type'=>'U3'
        				    );
        				    
			        $this->add_message($message);
    			        
			            
			        }
			    }
			}
		   
			
		
			    //echo  F('is_send')."\n";
			//echo json_encode(F('jsft_state'))."\n";
			
		   
		
    	});
		

		
	
		
		
		//存每期结果
		\Workerman\Lib\Timer::add(1, function(){
			$data = M('fn_open')->where("type='1'")->limit(0,1)->order("id desc")->find();
			//$data = json_decode(jsft_format($caiji),true);
		//	$yfksdata = F('yfksdata');
			
		//	echo "==".json_encode($data,1);
		 //	echo($next_time-time()."\n");
		
			if(F('yfksPeriodNumber')!=$data['term']){
				$today_time = date('Y-m-d',time()) . " 00:00:00";
				$res = M('fn_number')->where("game = '1' and periodnumber = {$data['term']} and awardtime > '{$today_time}'")->find();
				if(!$res){
					$map['awardnumbers'] = $data['code'];
					$map['awardtime'] = $data['time'];
					$map['time'] = strtotime( $data['time']);
					$map['periodnumber'] = $data['term'];
					$info = explode(',', $data['code']);
				
					$map['number'] = serialize($info);
					if($info[0]>$info[9]){
						$lh[0] = '龙';
					}else{
						$lh[0] = '虎';
					}
					if($info[1]>$info[8]){
						$lh[1] = '龙';
					}else{
						$lh[1] = '虎';
					}
					if($info[2]>$info[7]){
						$lh[2] = '龙';
					}else{
						$lh[2] = '虎';
					}
					if($info[3]>$info[6]){
						$lh[3] = '龙';
					}else{
						$lh[3] = '虎';
					}
					if($info[4]>$info[5]){
						$lh[4] = '龙';
					}else{
						$lh[4] = '虎';
					}
					$map['lh'] = serialize($lh);
					$map['tema'] = $info[0]+$info[1];

				
						if($map['tema'] % 2 == 0){
							$map['tema_ds'] = '双';
						}
						else{
							$map['tema_ds'] = '单';
						}
				
					

				
						if($map['tema']>=12){
							$map['tema_dx'] = '大';
						}else{
							$map['tema_dx'] = '小';
						}
				

					
					if($map['tema']>=3 && $map['tema']<=7){
						$map['tema_dw'] = 'A';
					}
					if($map['tema']>=8 && $map['tema']<=14){
						$map['tema_dw'] = 'B';
					}
					if($map['tema']>=15 && $map['tema']<=19){
						$map['tema_dw'] = 'C';
					}
					if($info[0]>$info[1]){
						$map['zx'] = '庄';
					}else{
						$map['zx'] = '闲';
					}
					$map['game'] = $data['type'];

					$res1 = M('fn_number')->add($map);
					
					
					if($res1){
						F('yfksPeriodNumber',$data['term']);
						F('jsft_state',$data);
						F('is_send',2);
						
						// if(F('is_send')==2){
			//	$number = M('fn_number')->where("game='1'")->order('id DESC')->find();
			//	echo json_encode($number);
				        $this->jiesuan($data['term'],$data['code']);
				    //echo $data['term']."\n";
				    //echo $data['code'];
		//	}
						
						
						
						

					
						
					}
				}
			}
    	});
    		//ping 聊天列表
		/**\Workerman\Lib\Timer::add($time_interval, function(){
        	//ping客户端(获取房间内所有用户列表 )
            $clients_list = $this->worker->connections;
			$num = count($clients_list);
		    
			 $last_id=F('chat_id');
			
			  $list=M('fn_chat')->where("game='1'")->limit(0,1)->order("id desc")->find();
        		if(!$last_id){
    			     $last_id = $list['id'];
    			 }
    			// echo $last_id.'===========================\n';
    			 if($list['id']>$last_id){
			 
    				    //echo $last_id.'\n';
    				 //if($last_id < $list['id']){
    				    F('chat_id',$list['id']);
    				    $jsft_state['chat_id']=$list['id'];
    				    $chat_list = M('fn_chat')->where("game='1' and id > ".$last_id)->order("id desc")->select();
    				    
    				   // if($chat_list){
    				    foreach($chat_list as $k=>$v){
    				        if($_COOKIE['uid']== $v['userid']){
    				            $v['is_my']=1;
    				        }else{
    				            $v['is_my']=0;
    				        }
            				  
    				        $jsft_state['chat_list'][]=$v;
            				  //  } 
            				  $new_message = array(
            					'send_type' => 'say',
            					'headimg'=>$admin_head,
            					'from_client_id' => $v['userid']?$v['userid']:1,
            					'from_client_name' => $v['username']?$v['username']:"管理员".$num,
            					'content' => $v['content'].$_COOKIE['uid'], 
            					'resault' => $jsft_state, 
            					'time' => date('H:i:s')
            				);
            				 foreach ($this->worker->connections as $conn) {
            				     
            					$conn -> send(json_encode($new_message));
        				        }
    				    }
    			}
				
			//	$connection -> send(json_encode($new_message)); 
				     
				// }
    	});***/
    
    	

	}
	
	/*
	 * 客户端连接时
	 * 
	 * */
	public function onConnect($connection){
		$connection->onWebSocketConnect = function($connection , $http_header)
		{
        
		};
		
		//$message_data = json_decode($http_header, true);
		// Gateway::sendToClient($message_data['client_id'], json_encode([
          //      'type'=>'init',//客户连接后向用户发送绑定uid请求（1）
           //     'client_id'=>$message_data['client_id']
          //    ]));
	}
	
	/**
	 * onMessage
	 * @access public
	 * 转发客户端消息
	 * @param  void
	 * @param  void
	 * @return void
	 */
	public function onMessage($connection, $data) {
	    //$user = session('user');
	//	echo 	$data;
		// 客户端传递的是json数据
		$message_data = json_decode($data, true);
		if (empty($message_data)) {
			return;
		}
		 $site_url="http://gbapp.live";
		//	echo $message_data['type'];	
		// 1:表示执行登陆操作 2:表示执行说话操作 3:表示执行退出操作
		// 根据类型执行不同的业务
		switch($message_data['type']){
			// 客户端回应服务端的心跳
			case 'pong' :
			    $userid = $connection->uid;
			    $last_id=$message_data['chat_id'];//F('chat_id');
		
			    //$lists=M('fn_chat')->where("game='1'")->limit(0,1)->order("id desc")->find();
        	//	if(!$last_id){
    			   //  $last_id = $lists['id'];
    			// }
    			 //echo $last_id.'==========='.$userid.'================\n';
    			 //if($list['id']>$last_id){
            			 if(empty($last_id)){
            			     $last=M('fn_chat')->where("game='1'")->limit(0,1)->order("id desc")->find();
                				 $last_id = $last['id'];
            			 }
    				   
    				   if(!$jsft_state){
				        $jsft_state = M("fn_open")->where("type='1'")->limit(0,1)->order("id desc")->find();
				        $jsft_state['open_time']=strtotime($jsft_state['next_time'])-time();
        				 }
        				 
				        $fnuser=M('fn_user')->where("userid='{$userid}' and roomid = 188888")->find();
				        	//echo $fnuser;
        				 F('jsft_state',$jsft_state);
        				 $chat_list = M('fn_chat')->where("game='1' and id > {$last_id}")->order("id desc")->select();
        				 $time=date('H:i:s');
        				 if($chat_list){
        				 $lasts=M('fn_chat')->where("game='1'")->limit(0,1)->order("id desc")->find();
        				 $last_id = $lasts['id'];
        				 
        				     F('chat_id',$last_id);
        				 }else{
        				     F('chat_id',$last_id);
        				 }
        				 $moneyInfo =$this->getinfo($userid);
        				 
        				
        				 
        				 $fl=M('fn_lottery1')->where("roomid = 188888")->getField('huishui');//get_query_val('fn_lottery'.$_COOKIE['game_id'],'huishui',array('roomid'=>$_SESSION['roomid']));
                        $huishui = $this->getinfo_huishui($userid);
             $all_huishui = $this->getall_huishui($fnuser['userid']);
                        $open_list=M('fn_number')->where("game='1'")->limit(0,10)->order("id desc")->select();
                        
        				$thisTerm=explode(',',$jsft_state['code']);
                        $h=intval($thisTerm[0])+intval($thisTerm[1]);
                        
				        $jsft_state['gyh']=$h;
				        if($h>=12){$jsft_state['gyh'].='大';}else{$jsft_state['gyh'].='小';}
				        if($h%2==0){$jsft_state['gyh'].='双';}else{$jsft_state['gyh'].='单';}
				        
        				 
        				 foreach($open_list as $k=>$v){
        				     $g = explode(",", $v['awardnumbers']);
        				        $adb = array();
                                $adb[] = ($g[0] > $g[9])?'龙':'虎';
                                $adb[] = ($g[1] > $g[8])?'龙':'虎';
                                $adb[] = ($g[2] > $g[7])?'龙':'虎';
                                $adb[] = ($g[3] > $g[6])?'龙':'虎';
                                $adb[] = ($g[4] > $g[5])?'龙':'虎';
                                $open_list[$k]['lh']=$adb;
                                
                                $open_list[$k]['h']=$g[0]+$g[1];
        				 }
        				// echo $userid;return;
        				$cons = M("fn_order")->where("roomid = 188888 and userid={$userid} and addtime like '" . date('Y-m-d') . "%'")->select();	 //$cons = array_reverse($cons);
                    $allZD = [];
                    foreach ($cons as $item) {
                        if ($item['status'] == '已撤单') continue;
                        $row = isset($allZD[$item['term']]) ? $allZD[$item['term']] : ['sn' => $item['term'], 'count' => 0, 'money' => 0, 'earn' => '结算中'];
                        $row['count']++;
                    
                        if (is_numeric($item['status'])) {
                            if (!is_numeric($row['earn'])) $row['earn'] = 0;
                            $row['earn'] += $item['status'];
                            //$row['money'] += floatval($item['money']);
                            $row['money'] +=  $item['status']>0?floatval($item['money']):0;
                            //print_r($item['status']);
                            //$row['money'] += $item['status']>0?floatval($item['money']):-floatval($item['money']);
                        }
                        $allZD[$item['term']] = $row;
                    }

$cl=M("fn_open")->where("type='1'")->limit(0,10)->order("id desc")->select();	

$pos=[];
$row=[];
$row['dx']='';
$row['dx_count']=0;
$row['ds']='';
$row['ds_count']=0;


foreach ($cl as $item){
    $opennum=explode(',',$item['code']);
    $gyh=$opennum[0]+$opennum[1];
    unset($opennum[0],$opennum[1]);
    $opennum[0]=$gyh;
    ksort($opennum);
    foreach ($opennum as $index=>$num){
        $lastRow=isset($pos[$index])?$pos[$index]:$row;
        if($num%2==0){
            if($lastRow['ds']=='双'){
                $lastRow['ds_count']++;
            }else $lastRow['ds_count']=1;
            $lastRow['ds']='双';
        }else{
            if($lastRow['ds']=='单'){
                $lastRow['ds_count']++;
            }else $lastRow['ds_count']=1;
            $lastRow['ds']='单';
        }

        $bigNum=($index==0)?10:5;
        if($num>=$bigNum){
            if($lastRow['dx']=='大'){
                $lastRow['dx_count']++;
            }else $lastRow['dx_count']=1;
            $lastRow['dx']='大';
        }else{
            if($lastRow['dx']=='小'){
                $lastRow['dx_count']++;
            }else $lastRow['dx_count']=1;
            $lastRow['dx']='小';
        }

        $pos[$index]=$lastRow;
    }
}
$showLong=[];
foreach ($pos as $index=>$item){
    $row=[];
    if($item['dx_count']>=3){
        $row['lx']=$item['dx'];
        $row['sn']=$index;
        $row['count']=$item['dx_count'];
        $showLong[]=$row;
    }
    if($item['ds_count']>=3){
        $row['lx']=$item['ds'];
        $row['sn']=$index;
        $row['count']=$item['ds_count'];
        $showLong[]=$row;
    }
}
$snName=[0=>'冠亚和',1=>'亚军',2=>'第三名',3=>'第四名',4=>'第五名',5=>'第六名',6=>'第七名',7=>'第八名',8=>'第九名',9=>'第十名'];
$i=1;
foreach($showLong as $k=>$vx){
    
    
    $showLong[$k]['xuhao']=$i++;
    $showLong[$k]['weizhi']=$snName[$vx['sn']];
    $showLong[$k]['jieguo']=$vx['lx'];
    $showLong[$k]['lianqi']=$vx['count'];
   
}
//select_query("fn_open", '*', "`type` = '$type' order by `next_time` desc limit 20");

        				 	
            				  //  }  
            				  
    $time = array();
    $time[0] = date('Y-m-d') . " 00:00:00";
    $time[1] = date('Y-m-d') . " 23:59:59";
            				  
		  
                            $user_money=M('fn_moneylog')->where("userid='{$userid}' and roomid = '188888' and status=1 and (`time` between '{$time[0]}' and '{$time[1]}')")->order('id desc')->limit(0,1)->getField("yk");
            				// echo $user_money['yk'];
            				  $new_message = array(
            				      
					                "changlong"=>$showLong,
            				        "user_order"=>$allZD,
            						"chat_list"=>$chat_list,
                					"open_info"=>$jsft_state,
                					"open_lists"=>$open_list,
                					"user_money"=> $fnuser['money'],
                					"username"=> $fnuser['username'],
                					"user_ls"=>$moneyInfo['liu'],
                					"user_sy"=>$user_money,
                					"user_hs"=>number_format($huishui['liu']*$fl,2),
					                "user_zhs"=>number_format($all_huishui['liu']*$fl,2),
                					"user_earn"=>$moneyInfo['yliuk'],
                					"chat_id"=>$last_id,
                					
                					"type"=>"say",
                					'userid'=>$userid
            				);
            				
            				
            					
            				
            				if(isset($this->worker->uidConnections[$connection->uid])){
                    	        $connection = $this->worker->uidConnections[$connection->uid];
                    	        $connection->send(json_encode($new_message));
                    	    }
                    	    
            				 /*foreach ($this->worker->connections as $conn) {
            				     
            					$conn -> send(json_encode($new_message));
            					
        				        }*/
    				 //   }
    			
			    
				break;
			case 'time' :
			    	$jsft_state = F('jsft_state');
			
        			$jsft_state['open_time']=strtotime($jsft_state['next_time'])-time();
        		//	echo $jsft_state['open_time']."=\n";
        			
        			$new_message = array(
				'type' => 'time', 
				'open_time'=>$jsft_state['open_time']
			);
			
			//if($num!=F('online')){
				//F('online',$num);
				if(isset($this->worker->uidConnections[$connection->uid])){
                    	        $connection = $this->worker->uidConnections[$connection->uid];
                    	        $connection->send(json_encode($new_message));
                    	    }
        			
        		break;	
        			
			case 'login' :
			   
				// 把昵称放到session中
				$client_name = htmlspecialchars($message_data['client_name']);
				
				/* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
		        * 实现针对特定uid推送数据
		        */
		        
				$connection->uid = $message_data['client_id'];
				$this->worker->uidConnections[$connection->uid] = $connection;
				
				$lice_user=M('users')->where("id='{$connection->uid}'")->find();
				
				
				$fnuser=M('fn_user')->where("userid='{$connection->uid}' and roomid = 188888")->find();
			//	echo json_encode($lice_user)."\n";
				
				$avgimg=$lice_user['avatar'];
				if(!$fnuser){
				    
				    
				    $userPass=md5("123456");
				    //$avgimg=$site_url.$lice_user['avatar'];//'http://gbapp.live/public/images/headicon.png';
				    
				    $attr=['userid'=>$connection->uid,'username'=>$lice_user['user_nicename'],'userpass'=>$lice_user['user_pass'],'headimg'=>$avgimg,'roomid'=>"188888",'money'=>'0','jia'=>'false','isagent'=>'false'];
				    $uid=M('fn_user')->add($attr);
				    $fnuser=M('fn_user')->where("id='$uid' and roomid = 188888")->find();
				    
				    
				    //$fnuser=M('fn_user')->where("id='$uid' and is_hui = 1")->find();
				    
				    
				    
				    
				    
				    
				}
				/**else{
				    $arr=array(
				        'username'=>$lice_user['user_nicename'],
				        'userpass'=>$lice_user['user_pass'],
				        'headimg'=>$avgimg,
				        'money'=>$lice_user['coin']
				        );
				    M("fn_user")->where("id = {$fnuser['id']}")->setField($arr);
				}**/
				
				    $time[0] = date('Y-m-d') . " 00:00:00";
                    $time[1] = date('Y-m-d') . " 23:59:59";
                    $log=M('fn_moneylog')->where("userid='{$fnuser["userid"]}' and is_hui=1 and status=1 and roomid = '188888' and (`time` between '{$time[0]}' and '{$time[1]}')")->order('id desc')->limit(0,1)->find();
                    if(empty($log)){
				     M('fn_moneylog')->add(array('userid' => $fnuser["userid"],'username'=>$fnuser["userid"], 'yuan_money' => $fnuser["money"], 'money' => $fnuser["money"],'content'=>"初始余额", 'time' => date("Y-m-d H:i:s",time()), 'roomid' => '188888','yk'=>0, 'is_hui' => '1','status' => '1'));
                    }
				//session($user,$meuidssage_data);
				 //$_COOKIE['uid']=$connection->uid;
				 
				 $jsft_state = F('jsft_state');
				// F('user_id')
				 if(!$jsft_state){
				     $jsft_state = M("fn_open")->where("type='1'")->limit(0,1)->order("id desc")->find();
		             //$data =  jsft_format($caiji);
			        //$jsft_state = json_decode($data,1);
				 }
				 
				      $jsft_state['open_time']=strtotime($jsft_state['next_time'])-time();
				 F('jsft_state',$jsft_state);
				 $chat_list = M('fn_chat')->where("game='1'")->limit(0,5)->order("id desc")->select();
				// $time=date('H:i:s');
				// $jsft_state['chat_list'] = M('fn_chat')->where("game='1' and addtime >{$time}")->limit(0,10)->order("id desc")->select();
				 $list=M('fn_chat')->where("game='1'")->limit(0,1)->order("id desc")->find();
				 //$jsft_state['last_id']=$list['id'];
				 
				 // $open_time=strtotime($jsft_state['next_time'])-time();
				  
				  //$jsft_state['open_time']=$fnuser['userid'];
				// $moneyInfo =$this->getinfo($fnuser['userid']);
			//	 $fl=M('fn_lottery1')->where("roomid = 188888")->getField('huishui');//get_query_val('fn_lottery'.$_COOKIE['game_id'],'huishui',array('roomid'=>$_SESSION['roomid']));
            // $huishui = $this->getinfo_huishui($fnuser['userid']);
           //  $all_huishui = $this->getall_huishui($fnuser['userid']);
			//	 $open_list=M('fn_number')->where("game='1'")->limit(0,10)->order("id desc")->select();
				 //print_r($open_list);
				 
               /* $thisTerm=explode(',',$jsft_state['code']);
                $h=intval($thisTerm[0])+intval($thisTerm[1]);
				$jsft_state['gyh']=$h;
				        if($h>=12){$jsft_state['gyh'].='大';}else{$jsft_state['gyh'].='小';}
				        if($h%2==0){$jsft_state['gyh'].='双';}else{$jsft_state['gyh'].='单';}
				  foreach($open_list as $k=>$v){
        				     $g = explode(",", $v['awardnumbers']);
        				        $adb = array();
                                $adb[] = ($g[0] > $g[9])?'龙':'虎';
                                $adb[] = ($g[1] > $g[8])?'龙':'虎';
                                $adb[] = ($g[2] > $g[7])?'龙':'虎';
                                $adb[] = ($g[3] > $g[6])?'龙':'虎';
                                $adb[] = ($g[4] > $g[5])?'龙':'虎';
                                $open_list[$k]['lh']=$adb;
                                $open_list[$k]['h']=$g[0]+$g[1];
        				 }*/
        				 
        	/*	$cons = M("fn_order")->where("roomid = 188888 and userid={$fnuser['userid']} and addtime like '" . date('Y-m-d') . "%'")->select();	 //$cons = array_reverse($cons);
$allZD = [];
foreach ($cons as $item) {
    if ($item['status'] == '已撤单') continue;
    $row = isset($allZD[$item['term']]) ? $allZD[$item['term']] : ['sn' => $item['term'], 'count' => 0, 'money' => 0, 'earn' => '结算中'];
    $row['count']++;

    if (is_numeric($item['status'])) {
        if (!is_numeric($row['earn'])) $row['earn'] = 0;
        $row['earn'] += $item['status'];
        //$row['money'] += floatval($item['money']);
        $row['money'] +=  $item['status']>0?floatval($item['money']):0;
        //print_r($item['status']);
        //$row['money'] += $item['status']>0?floatval($item['money']):-floatval($item['money']);
    }
    $allZD[$item['term']] = $row;
}*/
//==================================

//select_query("fn_open", '*', "`type` = '$type' order by `next_time` desc limit 20");
/*
$cl=M("fn_open")->where("type='1'")->limit(0,20)->order("id desc")->select();	

$pos=[];
$row=[];
$row['dx']='';
$row['dx_count']=0;
$row['ds']='';
$row['ds_count']=0;


foreach ($cl as $item){
    $opennum=explode(',',$item['code']);
    $gyh=$opennum[0]+$opennum[1];
    unset($opennum[0],$opennum[1]);
    $opennum[0]=$gyh;
    ksort($opennum);
    foreach ($opennum as $index=>$num){
        $lastRow=isset($pos[$index])?$pos[$index]:$row;
        if($num%2==0){
            if($lastRow['ds']=='双'){
                $lastRow['ds_count']++;
            }else $lastRow['ds_count']=1;
            $lastRow['ds']='双';
        }else{
            if($lastRow['ds']=='单'){
                $lastRow['ds_count']++;
            }else $lastRow['ds_count']=1;
            $lastRow['ds']='单';
        }

        $bigNum=($index==0)?10:5;
        if($num>=$bigNum){
            if($lastRow['dx']=='大'){
                $lastRow['dx_count']++;
            }else $lastRow['dx_count']=1;
            $lastRow['dx']='大';
        }else{
            if($lastRow['dx']=='小'){
                $lastRow['dx_count']++;
            }else $lastRow['dx_count']=1;
            $lastRow['dx']='小';
        }

        $pos[$index]=$lastRow;
    }
}
$showLong=[];
foreach ($pos as $index=>$item){
    $row=[];
    if($item['dx_count']>=3){
        $row['lx']=$item['dx'];
        $row['sn']=$index;
        $row['count']=$item['dx_count'];
        $showLong[]=$row;
    }
    if($item['ds_count']>=3){
        $row['lx']=$item['ds'];
        $row['sn']=$index;
        $row['count']=$item['ds_count'];
        $showLong[]=$row;
    }
}

$snName=[0=>'冠亚和',1=>'亚军',2=>'第三名',3=>'第四名',4=>'第五名',5=>'第六名',6=>'第七名',7=>'第八名',8=>'第九名',9=>'第十名'];
$i=1;
foreach($showLong as $k=>$vx){
    
    
    $showLong[$k]['xuhao']=$i++;
    $showLong[$k]['weizhi']=$snName[$vx['sn']];
    $showLong[$k]['jieguo']=$vx['lx'];
    $showLong[$k]['lianqi']=$vx['count'];
   
}
*/
// $time = array();
   // $time[0] = date('Y-m-d') . " 00:00:00";
  //  $time[1] = date('Y-m-d') . " 23:59:59";
            				  
//$user_money=M('fn_moneylog')->where("userid='{$fnuser['userid']}' and roomid = '188888' and status=1 and (`time` between '{$time[0]}' and '{$time[1]}')")->order('id desc')->limit(0,1)->getField("yk");
            				  //$shuying = number_format($user_money['money'] - $fnuser['money'], 2);
            		//echo json_decode($user_money);	 
				 $new_message = array(
				   
				//	"changlong"=>$showLong,
				//	"user_order"=>$allZD,
					"chat_list"=>$chat_list,
				//	"open_lists"=>$open_list,
					"open_info"=>$jsft_state,
				//	"user_money"=> $fnuser['money'],
				//	"username"=> $fnuser['username'],
				//	"user_ls"=>$moneyInfo['liu'],	
				//	"user_sy"=>$user_money,
				//	"user_hs"=>number_format($huishui['liu']*$fl,2),
				//	"user_zhs"=>number_format($all_huishui['liu']*$fl,2),
				//	"user_earn"=>$moneyInfo['yliuk'],
					"chat_id"=>$list['id'],
					"type"=>"login"
					
					
					
				);
				
				
			
				
				
			//$new_message['uid']=$message_data['client_id'];
			if(isset($this->worker->uidConnections[$connection->uid])){
    	        $connection = $this->worker->uidConnections[$connection->uid];
    	        $connection->send(json_encode($new_message));
    	    }
			/*	$message = array(
					
					'headimg'=>'http://gbapp.live/public/images/headicon.png',
					'content' => $client_name.'进入房间', 
					'roomid' => '188888',
					'addtime' => date('H:i:s'),
					'userid'=>1,
					'username'=>"管理员",
					'type'=>'U3'
				);
				**/
				
				
				//$this->add_message($message);
				break;	
			case 'touzhu' :
				$userid = $connection->uid;
				/*是否竞猜时间*/
			//	echo $userid."=\n";
				$jsft_state = F('jsft_state');
				$fnuser=M('fn_user')->where("userid='$userid' and roomid = 188888")->find();

				$nickname = $fnuser['username'];
                    $content = $message_data['content'];
                    $headimg = $fnuser['headimg'];         //'http://gbapp.live/public/images/headicon.png';
                    $type = 1;
                    $BetGame = 1;
                    $GameType=1;
                    
				//echo $content."==\n";
                  	$next_time =strtotime($jsft_state['next_time']); 
                  
                   
            			  //echo $next_time-time()."===\n";
				   
				if($next_time-time() < 0){
					
					
					 $info_messages = M("fn_infos")->where("type=3 and game=1")->select();
				$admin_headimg = M('fn_setting')->where('roomid = 188888')->getField('setting_robotsimg');
            $admin_head='https://wukonglc.com'.$admin_headimg;
					
                 
                   
						foreach($info_messages as $k=>$v){
            			        $str = '';
            			        $str1="竞猜无效";
            			        $Arr_Str1 = $this->arr_split_zh($str1);
                                $Arr1 = $this->arr_split_zh($v['title']);
                                if($this->Str_Is_Equal($Arr_Str1,$Arr1)){
                                     $str = str_replace("{用户名}",$nickname,$v['content']);
                                     $str = str_replace("{竞猜内容}",$content,$str);
            			         //echo $v['content'];
            			        // echo $nickname;
            			         //echo $str;
                                    $message1 = array(
                    					'game' => '1',
                    					'headimg'=>$admin_head,
                    					'content' => $str, 
                    					'roomid' => '188888',
                    					'addtime' => date('H:i:s'),
                    					'userid'=>'1',
                    					'username'=>'管理员',
                    					'type'=>'U3'
                    				    );
                			        
                			        $this->add_message($message1);
                			
                	            }
                                }
					
					
					
				}else{
				    

                    if ($content == '充值指引') {
                        $typeNum = 1;//array_flip($gmidAli);
                        $GameType = 1;//$typeNum[$BetGame];
                        //echo $GameType."== \n";
                       
                        $showRules = M('fn_lottery' . $GameType)->where("roomid='188888'")->limit(0,1)->getField("rules");
                        
                       // get_query_val('fn_lottery' . $GameType, 'rules', array('roomid' => '188888'));
                        //insert_query("fn_chat", array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => $_COOKIE['game'], 'headimg' => $headimg, 'type' => "U3", 'userid' => F('user_id'), 'roomid' => '188888'));
                       // M("fn_chat")->add(array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => 1, 'headimg' => $headimg, 'type' => "U3", 'userid' => F('user_id'), 'roomid' => '188888'));
                        
                        $this->admin_says($userid,"@" . $nickname . $showRules);
                       /*** $time_message = array(
						'userid'  => $userid,
						'type' => 'admin',
						'headimg'=>'http://gbapp.live/public/images/headicon.png',
						'from_client_name' => '管理员',
						'content' => $showRules, 
						'time' => date('H:i:s')
					);
                        
                        if(isset($this->worker->uidConnections[$connection->uid])){
                	        $connection = $this->worker->uidConnections[$connection->uid];
                	        $connection->send(json_encode($time_message));
                	    }*/
                        return;
                    }
                      
                    //处理投注
                    $gameTypeID = 1;//$this->getGameIdByCode($BetGame);
                 //echo $gameTypeID."== \n";
                   // return;
                    if(M('fn_lottery' . $GameType)->where("roomid='188888'")->getField("gameopen") == 'false') $BetGame = 'feng';
                     //M('fn_open' . $GameType)->where("type = {$gameTypeID}")->getField("next_term")->order('next_time desc')->select();
        
                    $BetTerm = M('fn_open')->where("type = {$gameTypeID}")->order('next_time desc')->getField("next_term");
                    $time = M('fn_lottery' . $gameTypeID)->where("roomid='188888'")->getField("fengtime");//后台设置的开盘时间
              
                    $djs = strtotime(M('fn_open')->where("type = {$gameTypeID}")->order('next_time desc')->getField("next_time")) - time();
                    
                    
                    
                    
                    
                    
                    
                    
                    if ($djs < 1) {
                        $fengpan = true;
                    } else {
                        $fengpan = false;
                    }

                    if (substr($content, 0, 1) == '@') {
                        $type = "U1";
                    } else {
                        $type = "U3";
                    }
                    if (!empty($content)) {
                    //insert_query("fn_chat", array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => $_COOKIE['game'], 'headimg' => $headimg, 'type' => $type, 'userid' => F('user_id'), 'roomid' => '188888'));
                         M("fn_chat")->add(array("username" => $fnuser['username'], 'content' => $content, 'addtime' => date('H:i:s'), 'game' => 1, 'headimg' => $headimg, 'type' => $type, 'userid' => $userid, 'roomid' => '188888'));
                      
                      
                        }
                        
                     if ($type == 'U3') {
                        if (substr($content, 0, 6) == '上分') {
                            $fenshuchange = true;
                            $sfmoney = substr($content, 6);
                            if ((int)$sfmoney > 0) $this->shang_points($fnuser['username'],$userid, $sfmoney);
                        } elseif (substr($content, 0, 3) == '查') {
                            $fenshuchange = true;
                            $sfmoney = substr($content, 3);
                            if ((int)$sfmoney > 0) $this->shang_points($fnuser['username'],$userid, $sfmoney);
                        } elseif (substr($content, 0, 6) == '下分') {
                            $fenshuchange = true;
                            $xfmoney = substr($content, 6);
                            if ((int)$xfmoney > 0) $this->xia_opints($fnuser['username'], $userid, $xfmoney);
                        } elseif (substr($content, 0, 3) == '回') {
                            $fenshuchange = true;
                            $xfmoney = substr($content, 3);
                            if ((int)$xfmoney > 0) $this->xia_opints($fnuser['username'], $userid, $xfmoney);
                        } else {
                            $fenshuchange = false;
                        }
                        
                        /*elseif (substr($content, 0, 3) == '上') {
                            $fenshuchange = true;
                            $sfmoney = substr($content, 3);
                            if ((int)$sfmoney > 0) $this->shang_points($fnuser['username'],$userid, $sfmoney);
                        } elseif (substr($content, 0, 3) == '下') {
                            $fenshuchange = true;
                            $xfmoney = substr($content, 3);
                            if ((int)$xfmoney > 0) $this->xia_opints($fnuser['username'], $userid, $xfmoney);
                        } */
            
                               /* if ($content == "取消") {
                                    $this->CancelBet(F('user_id'), $BetTerm, $BetGame, $fengpan);
                                    //echo json_encode(array("success" => true, "content" => $content));
                                   // insert_query("fn_chat", array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => $_COOKIE['game'], 'headimg' => $headimg, 'type' => $type, 'userid' => F('user_id'), 'roomid' => '188888'));
                                    M("fn_chat")->add(array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => 1, 'headimg' => $headimg, 'type' => $type, 'userid' => F('user_id'), 'roomid' => '188888'));
                                    break;
                                }**/
            
                    } 
      
                  

                   
                    $hasDoBet = false;
                        if ($type == 'U3' && $fenshuchange == false) {
                            $hasDoBet = true;
                            
                            $co = $this->addBet($userid, $fnuser['username'], $fnuser['headimg'], $content, $BetTerm, $fengpan);
                            
                 return json_encode(array("success" => true, "content" => $content, 'msg' => '操作成功'));
                    
                        }
        

                   
                    break;
				    
				}
				break;
				case 'wantou' :
				$userid = $connection->uid;
				/*是否竞猜时间*/
				
				$jsft_state = F('jsft_state');
				$fnuser=M('fn_user')->where("userid='$userid' and roomid = 188888")->find();

				$nickname = $fnuser['username'];
                    $content = $message_data['content'];
                    $headimg = $fnuser['headimg'];//'http://gbapp.live/public/images/headicon.png';
                    $type = 1;
                    $BetGame = 1;
                    $GameType=1;
                    	//echo json_encode($jsft_state);
                   	$next_time =strtotime($jsft_state['next_time']); 
                    
					if($next_time-time() <10){
					
					
					 $info_messages = M("fn_infos")->where("type=3 and game=1")->select();
				$admin_headimg = M('fn_setting')->where('roomid = 188888')->getField('setting_robotsimg');
            $admin_head='https://wukonglc.com'.$admin_headimg;
					
                 
                   
						foreach($info_messages as $k=>$v){
            			        $str = '';
            			        $str1="竞猜无效";
            			        $Arr_Str1 = $this->arr_split_zh($str1);
                                $Arr1 = $this->arr_split_zh($v['title']);
                                if($this->Str_Is_Equal($Arr_Str1,$Arr1)){
                                     $str = str_replace("{用户名}",$nickname,$v['content']);
                                     $str = str_replace("{竞猜内容}",$content,$str);
            			         //echo $v['content'];
            			        // echo $nickname;
            			        // echo $str;
                                    $message1 = array(
                    					'game' => '1',
                    					'headimg'=>$admin_head,
                    					'content' => $str, 
                    					'roomid' => '188888',
                    					'addtime' => date('H:i:s'),
                    					'userid'=>'1',
                    					'username'=>'管理员',
                    					'type'=>'U3'
                    				    );
                			        
                			        $this->add_message($message1);
                			
                	            }
                                }
				}else{
				    //echo $content;
				    $contents=explode(",", $content);
				   
				    
                   
                      
                    //处理投注
                    $gameTypeID = 1;//$this->getGameIdByCode($BetGame);
                 //echo $gameTypeID."== \n";
                   // return;
                    if(M('fn_lottery' . $GameType)->where("roomid='188888'")->getField("gameopen") == 'false') $BetGame = 'feng';
                     //M('fn_open' . $GameType)->where("type = {$gameTypeID}")->getField("next_term")->order('next_time desc')->select();
        
                    $BetTerm = M('fn_open')->where("type = {$gameTypeID}")->order('next_time desc')->getField("next_term");
                    $time = M('fn_lottery' . $gameTypeID)->where("roomid='188888'")->getField("fengtime");//后台设置的开盘时间
               // (int)get_query_val('fn_lottery' . $gameTypeID, 'fengtime', array('roomid' => '188888'));
                    $djs = strtotime(M('fn_open')->where("type = {$gameTypeID}")->order('next_time desc')->getField("next_time")) - time();
                    
                    
                    
                    
                    
                    
                    
                    
                    if ($djs < 1) {
                        $fengpan = true;
                    } else {
                        $fengpan = false;
                    }
                   
                    
                    


                    if (substr($content, 0, 1) == '@') {
                        $type = "U1";
                    } else {
                        $type = "U3";
                    }
    
                     if (!empty($content)) {
                         M("fn_chat")->add(array("username" => $fnuser['username'], 'content' => $content, 'addtime' => date('H:i:s'), 'game' => 1, 'headimg' => $headimg, 'type' => $type, 'userid' => $userid, 'roomid' => '188888'));
                      
                      
                        }
                        
                       
                        
                        
                     

                    if ($type == 'U3') {
                        
            
                               /* if ($content == "取消") {
                                    $this->CancelBet(F('user_id'), $BetTerm, $BetGame, $fengpan);
                                    //echo json_encode(array("success" => true, "content" => $content));
                                   // insert_query("fn_chat", array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => $_COOKIE['game'], 'headimg' => $headimg, 'type' => $type, 'userid' => F('user_id'), 'roomid' => '188888'));
                                    M("fn_chat")->add(array("username" => $nickname, 'content' => $content, 'addtime' => date('H:i:s'), 'game' => 1, 'headimg' => $headimg, 'type' => $type, 'userid' => F('user_id'), 'roomid' => '188888'));
                                    break;
                                }**/
            
                    }   
                    $hasDoBet = false;
                    $fenshuchange = false;
                        if ($type == 'U3' && $fenshuchange == false) {
                            $hasDoBet = true;
                            foreach($contents as $k=>$v){
                                $co = $this->addBet($userid, $fnuser['username'], $fnuser['headimg'], $v, $BetTerm, $fengpan);
                            }
                            
                           
                        }
                 
                    return;
        

                    
                    break;
				    
				}
				break;
					case 'huishui':
				    $userid = $connection->uid;
				        //$start_time = date("Y-m-d 00:00:00", time());
                        //$end_time = date("Y-m-d 23:59:59", time());
                             $fnuser=M('fn_user')->where("userid='$userid' and roomid = 188888")->find();
//echo json_encode($fnuser)."====";
				$nickname = $fnuser['username'];      
                           
                           $fl=M('fn_lottery1')->where("roomid = 188888")->getField('huishui');
                             $huishui = $this->getinfo_huishui($userid);
                             $Money =$huishui['liu']*$fl;
                            // echo $userid; exit;
                             $time = date("Y-m-d H:m:s", time());
                             if($Money > 0){
                                 $this->set_huishui($userid,$Money,$nickname,$addQihao);
                                   // $con='';
                                 //$this->admin_say($userid,$nickname,$addQihao,$Money,3,$con);  
                                   // ajaxMsg(['status'=>1,'msg'=>'操作成功']);
                                    return json_encode(array("success" => true, "content" => '操作成功', 'msg' => '操作成功'));
                             }else{
                                    
                                    return json_encode(array("success" => false, "content" => '操作失败', 'msg' => '操作失败'));
                             }
				    break;
		/*	case 'game_robot':
				if(C('game_robot')==1){
					$mess = $this->robot_message();
					$robot = $this->robot();
					$jsft_state = F('jsft_state');
					$arr=preg_split("/([a-zA-Z0-9]+)/", $mess[0]['content'], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
					$true_false=strpos($mess[0]['content'],'点');
				    if($true_false > 0){
					    $mess[0]['points']=$arr['0'].$arr['1'];
					    $mess[0]['content']=$arr['2'];
					}else{
						if(strpos( $mess[0]['content'],"/")>0){
						    if(strpos( $mess[0]['content'],"/")>0 && strpos( $mess[0]['content'],"/")<=3 && strpos( $mess[0]['content'],"龙")<=-1 && strpos( $mess[0]['content'],"虎")<=-1){
						        $fenge=explode("/", $mess[0]['content']);
							    $mess[0]['points']=$fenge['0'];
							    $mess[0]['content']=$fenge['1'];
						    }else{
						        $fenge=explode("/", $mess[0]['content']);
							    $mess[0]['points']=$fenge['0'].'/'.$fenge['1'];
							    $mess[0]['content']=$fenge['2'];
						    }
							
						}elseif (strpos($mess[0]['content'],'大')>0 || strpos($mess[0]['content'],'8双')===0  || (strpos($mess[0]['content'],'单')>0 && strpos($mess[0]['content'],'和单') <-1)) {
						    $mess[0]['points']=$arr['0'].$arr['1'];
					        $mess[0]['content']=$arr['2'];
						}else{
						    $mess[0]['points']=$arr['0'];
    					    $mess[0]['content']=$arr['1'];
						}

					}
					$countall+=$mess[0]['content'];
					$new_message = array(
						'type' => 'say',
						'headimg'=>'/Uploads'.$robot[0]['headimgurl'],
						'from_client_name' => $robot[0]['nickname'],
						'countNum'=>$countall,
						'points'=>$mess[0]['points'],
						'content'=>$mess[0]['content'],
						'time'=>date('H:i:s')
					);
					if($jsft_state==1 && $jsft_state!=0 ){
						foreach ($this->worker->uidConnections as $con) {
							$con -> send(json_encode($new_message));
						}
						$this->add_message($new_message);
					}
			}
				break;	*/
		}
	}
function getall_huishui($userid)
{
    $time = array();
    $time[0] = date('Y-m-d') . " 00:00:00";
    $time[1] = date('Y-m-d') . " 23:59:59";
   // $sf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and type = '上分' and is_hui= '0' and status = '已处理' and (time between '{$time[0]}' and '{$time[1]}')");
   /// $xf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and type = '下分' and is_hui= '0' and status = '已处理' and (time between '{$time[0]}' and '{$time[1]}')");
    $allm =  M('fn_order')->where("roomid = '188888' and userid = '{$userid}' and (`addtime` between '{$time[0]}' and '{$time[1]}')  and (status > 0 or status < 0) and is_hui= '1'")->sum('money');//(int)get_query_val('fn_order', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and (`addtime` between '{$time[0]}' and '{$time[1]}')  and (status > 0 or status < 0) and is_hui= '0'");
    $allz = M('fn_order')->where("roomid = '188888' and (`addtime` between '{$time[0]}' and '{$time[1]}') and status > 0 and userid = '{$userid}' and is_hui= '1'")->sum('status');//get_query_val('fn_order', 'sum(`status`)', "roomid = '{$_SESSION['roomid']}' and (`addtime` between '{$time[0]}' and '{$time[1]}') and status > 0 and userid = '{$userid}' and is_hui= '0'");
    $yk = $allz - $allm;
    $yk = round($yk, 2);
    return array("yk" => $yk, 'liu' => $allm);
}	
function getinfo_huishui($userid)
{
    $time = array();
    $time[0] = date('Y-m-d') . " 00:00:00";
    $time[1] = date('Y-m-d') . " 23:59:59";
   // $sf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and type = '上分' and is_hui= '0' and status = '已处理' and (time between '{$time[0]}' and '{$time[1]}')");
   /// $xf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and type = '下分' and is_hui= '0' and status = '已处理' and (time between '{$time[0]}' and '{$time[1]}')");
    $allm =  M('fn_order')->where("roomid = '188888' and userid = '{$userid}' and (`addtime` between '{$time[0]}' and '{$time[1]}')  and (status > 0 or status < 0) and is_hui= '0'")->sum('money');//(int)get_query_val('fn_order', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and (`addtime` between '{$time[0]}' and '{$time[1]}')  and (status > 0 or status < 0) and is_hui= '0'");
    $allz = M('fn_order')->where("roomid = '188888' and (`addtime` between '{$time[0]}' and '{$time[1]}') and status > 0 and userid = '{$userid}' and is_hui= '0'")->sum('status');//get_query_val('fn_order', 'sum(`status`)', "roomid = '{$_SESSION['roomid']}' and (`addtime` between '{$time[0]}' and '{$time[1]}') and status > 0 and userid = '{$userid}' and is_hui= '0'");
    $yk = $allz - $allm;
    $yk = round($yk, 2);
    return array("yk" => $yk, 'liu' => $allm);
}
	
	
	public function robot_message(){
		$count = M('caiji_game_robot_message')->where("type=1")->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('caiji_game_robot_message')->where("type=1")->limit($limit)->select();  
		return $data;
	}
	
	public function robot(){
		$count = M('caiji_game_robot')->count();
     	$rand = mt_rand(0,$count-1); //产生随机数。
     	$limit = $rand.','.'1'; 
    	$data = M('caiji_game_robot')->limit($limit)->select();  
		return $data;
	}
	 function getinfo($userid)
{
    $time = array();
    $time[0] = date('Y-m-d') . " 00:00:00";
    $time[1] = date('Y-m-d') . " 23:59:59";
  //  $sf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and type = '上分' and status = '已处理' and (time between '{$time[0]}' and '{$time[1]}')");
    
    //$xf = (int)get_query_val('fn_upmark', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and type = '下分' and status = '已处理' and (time between '{$time[0]}' and '{$time[1]}')");
    
    $allm = M('fn_order')->where("roomid = '188888' and userid = '{$userid}' and (`addtime` between '{$time[0]}' and '{$time[1]}')  and (status > 0 or status < 0)")->sum('money');//(int)get_query_val('fn_order', 'sum(`money`)', "roomid = '{$_SESSION['roomid']}' and userid = '{$userid}' and (`addtime` between '{$time[0]}' and '{$time[1]}')  and (status > 0 or status < 0)");
    
    $allz = M('fn_order')->where("roomid = '188888' and (`addtime` between '{$time[0]}' and '{$time[1]}') and status > 0 and userid = '{$userid}'")->sum('status');//get_query_val('fn_order', 'sum(`status`)', "roomid = '{$_SESSION['roomid']}' and (`addtime` between '{$time[0]}' and '{$time[1]}') and status > 0 and userid = '{$userid}'");
    $yk = $allz - $allm;
    $yk = round($yk, 2);
    return array("yk" => $yk, 'liu' => $allm);
}
	/**
	 * onClose
	 * 关闭连接
	 * @access public
	 * @param  void
	 * @return void
	 */
	public function onClose($connection) {
		$user = session($connection->id);
		foreach ($this->worker->uidConnections as $con) {
			if (!empty($user)) {
				$new_message = array(
					'type' => 'logout',
					'from_client_name' => $user,
					'time' => date('H:i:s')
				);
				$con -> send(json_encode($new_message));
			}
		}
		
		if(isset($connection->uid)){
	        // 连接断开时删除映射
	        unset($this->worker->uidConnections[$connection->uid]);
    	}
	}
	function sendMessageByUid($uid, $message)
    {
        if(isset($this->uidConnections[$uid]))
        {
            $connection = $this->uidConnections[$uid];
            $connection->send($message);
            return true;
        }
        return false;
}
	
	
	/*存竞猜记录和信息*/
	protected function add_order($mew_message){
		$res = M("caiji_game_order")->add($mew_message);
		return $res;
	}
	protected function add_message($new_message){

		if (!empty($new_message)) {
			$new_message['game'] = '1';
			$res = M('fn_chat')->add($new_message);
			return $res;
		}
	}
	/*
	 * 竞猜成功  加分
	 * */
/*	public function add_points($order_id,$userid,$points){
		$res = M('users')->where("id = $userid")->setInc('coin',$points);
		if($res){
			//$userBalance = M('users')->where("id = $userid")->getField("points");
//			$sql = 'update think_order set is_add=1,add_points='.$points.',balance='.($userBalance).' where id='.$order_id;
//			$Model = M();
//			$res1 = $Model->execute($sql);
			$res1 = M("caiji_game_order")->where("id = $order_id")->setField(array('is_add'=>'1','add_points'=>$points));
		}
		if($res && $res1){
			return 1;
		}
	}*/
	
	public function add_fanshui($order_id,$userid){
		return 1;
		//$order_info=M("caiji_game_order")->where("id = $order_id")->find();
//		$user_info=M('users')->where("id = $userid")->find();
//		if($order_info){
//			if($order_info['del_points']>0 && $order_info['del_points']<=1000){
//				$fanshui_money=$order_info['del_points']*0.05;
//				$fanshui=$fanshui_money+$user_info['fanshui'];
//			}elseif($order_info['del_points']>=1001 && $order_info['del_points']<=3000){
//				$fanshui_money=$order_info['del_points']*0.08;
//				$fanshui=$fanshui_money+$user_info['fanshui'];
//			}elseif($order_info['del_points']>3000){
//				$fanshui_money=$order_info['del_points']*0.1;
//				$fanshui=$fanshui_money+$user_info['fanshui'];
//			}
//			$points=$fanshui_money+$user_info['points'];
//			$res = M('users')->where("id = $userid")->setField(array('fanshui'=>$fanshui,'points'=>$points));
//			$sql = 'update gold_order set fanshui='.$fanshui_money.' where id='.$order_id;
//			$Model = M();
//			$Model->execute($sql);
//			
//		}
//		if($res){
//			return 1;
//		}
	}
	
	
	/*竞猜成功通知
	 * */
	public function send_msg($type,$points,$userid){
		$message_points = array(
			'type' => $type,
			'points'=>	$points,
			'time'=>date('H:i:s')
		);
	//	if(isset($this->worker->uidConnections[$userid])){
	   //     $connection = $this->worker->uidConnections[$userid];
	    //    $connection->send(json_encode($message_points));
	   // }
	}
	function jiesuan($term,$opencode)
{
    //"term"=>$term
   
   // $limit = '0,1'; 
    $cons = M('fn_order')->where(array("status" => "未结算","type"=>1))->select();
    
    //while ($con = db_fetch_array()) {
    //    $cons[] = $con;
   // }
  // echo json_encode($cons);
   
        foreach ($cons as $k=>$con) {
            if($con['jia'] == "false"){
            M("fn_moneylog")->where("userid = {$con['userid']} and status = 0")->setField(array('status'=>1));
            }
        $id = $con['id'];
        $roomid = $con['roomid'];
        $user = $con['userid'];
        $term = $con['term'];
        $zym_1 = $con['mingci'];
        $zym_8 = $con['content'];
        $zym_7 = $con['money'];
        $gameTypeNum = $con['type'];
        $table = 'fn_lottery' . $gameTypeNum;
        $game = $this->getGameTxtName($gameTypeNum);
        
        $gametype = $gameTypeNum;
       // $opencode = get_query_val('fn_open', 'code', "`term` = '$term' and `type` = '$gametype'");
        $opencode = str_replace('10', '0', $opencode);
        if ($opencode == "") continue;
        $code = explode(',', $opencode);
         //$peilv = M($table)->where("roomid = $roomid")->getField('da');
        
        if ($zym_1 == '1') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');//M($table)->where("roomid = $roomid")->getField('da');
                
                if ((int)$code[0] > 5 || $code[0] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[0] < 6 && $code[0] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    //update_querys("fn_order", array("status" => $zym_11), array('id' => $id));
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[0] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[0] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[0] % 2 != 0 && (int)$code[0] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[0] % 2 == 0 && (int)$code[0] > 5 || (int)$code[0] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[0] % 2 == 0 && (int)$code[0] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[0] % 2 != 0 && (int)$code[0] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '龙') {
                $peilv = M($table)->where("roomid = $roomid")->getField('long');//get_query_val($table, '`long`', "`roomid` = '$roomid'");
                if ((int)$code[0] > (int)$code[9] && $code[9] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[0] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '虎') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hu');//get_query_val($table, 'hu', "`roomid` = '$roomid'");
                if ((int)$code[0] < (int)$code[9] && $code[0] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[9] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[0]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '2') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[1] > 5 || $code[1] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[1] < 6 && $code[1] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[1] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[1] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[1] % 2 != 0 && (int)$code[1] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[1] % 2 == 0 && (int)$code[1] > 5 || (int)$code[1] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[1] % 2 == 0 && (int)$code[1] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[1] % 2 != 0 && (int)$code[1] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '龙') {
                $peilv = M($table)->where("roomid = $roomid")->getField('long');//get_query_val($table, '`long`', "`roomid` = '$roomid'");
                if ((int)$code[1] > (int)$code[8] && $code[8] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[1] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '虎') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hu');//get_query_val($table, 'hu', "`roomid` = '$roomid'");
                if ((int)$code[1] < (int)$code[8] && $code[1] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[8] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[1]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '3') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[2] > 5 || $code[2] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[2] < 6 && $code[2] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[2] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[2] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[2] % 2 != 0 && (int)$code[2] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[2] % 2 == 0 && (int)$code[2] > 5 || (int)$code[2] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[2] % 2 == 0 && (int)$code[2] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[2] % 2 != 0 && (int)$code[2] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '龙') {
                $peilv = M($table)->where("roomid = $roomid")->getField('long');//get_query_val($table, '`long`', "`roomid` = '$roomid'");
                if ((int)$code[2] > (int)$code[7] && $code[7] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[2] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '虎') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hu');//get_query_val($table, 'hu', "`roomid` = '$roomid'");
                if ((int)$code[2] < (int)$code[7] && $code[2] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[7] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[2]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '4') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[3] > 5 || $code[3] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[3] < 6 && $code[3] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[3] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[3] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[3] % 2 != 0 && (int)$code[3] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[3] % 2 == 0 && (int)$code[3] > 5 || (int)$code[3] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[3] % 2 == 0 && (int)$code[3] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[3] % 2 != 0 && (int)$code[3] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '龙') {
                $peilv = M($table)->where("roomid = $roomid")->getField('long');//get_query_val($table, '`long`', "`roomid` = '$roomid'");
                if ((int)$code[3] > (int)$code[6] && $code[6] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[3] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '虎') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hu');//get_query_val($table, 'hu', "`roomid` = '$roomid'");
                if ((int)$code[3] < (int)$code[6] && $code[3] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[6] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[3]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '5') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[4] > 5 || $code[4] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[4] < 6 && $code[4] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[4] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[4] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[4] % 2 != 0 && (int)$code[4] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[4] % 2 == 0 && (int)$code[4] > 5 || (int)$code[4] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[4] % 2 == 0 && (int)$code[4] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[4] % 2 != 0 && (int)$code[4] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '龙') {
                $peilv = M($table)->where("roomid = $roomid")->getField('long');//get_query_val($table, '`long`', "`roomid` = '$roomid'");
                if ((int)$code[4] > (int)$code[5] && $code[5] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[4] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '虎') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hu');//get_query_val($table, 'hu', "`roomid` = '$roomid'");
                if ((int)$code[4] < (int)$code[5] && $code[4] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($code[5] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[4]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '6') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[5] > 5 || $code[5] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[5] < 6 && $code[5] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[5] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[5] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[5] % 2 != 0 && (int)$code[5] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[5] % 2 == 0 && (int)$code[5] > 5 || (int)$code[5] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[5] % 2 == 0 && (int)$code[5] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[5] % 2 != 0 && (int)$code[5] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[5]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '7') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[6] > 5 || $code[6] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[6] < 6 && $code[6] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[6] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[6] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[6] % 2 != 0 && (int)$code[6] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[6] % 2 == 0 && (int)$code[6] > 5 || (int)$code[6] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[6] % 2 == 0 && (int)$code[6] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[6] % 2 != 0 && (int)$code[6] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[6]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '8') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[7] > 5 || $code[7] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[7] < 6 && $code[7] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[7] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[7] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[7] % 2 != 0 && (int)$code[7] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[7] % 2 == 0 && (int)$code[7] > 5 || (int)$code[7] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[7] % 2 == 0 && (int)$code[7] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[7] % 2 != 0 && (int)$code[7] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[7]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '9') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[8] > 5 || $code[8] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[8] < 6 && $code[8] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[8] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[8] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[8] % 2 != 0 && (int)$code[8] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[8] % 2 == 0 && (int)$code[8] > 5 || (int)$code[8] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[8] % 2 == 0 && (int)$code[8] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[8] % 2 != 0 && (int)$code[8] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[8]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '0') {
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('da');
                if ((int)$code[9] > 5 || $code[9] == '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiao');
                if ((int)$code[9] < 6 && $code[9] != '0') {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dan');
                if ((int)$code[9] % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('shuang');
                if ((int)$code[9] % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dadan');
                if ((int)$code[9] % 2 != 0 && (int)$code[9] > 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '大双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('dashuang');
                if ((int)$code[9] % 2 == 0 && (int)$code[9] > 5 || (int)$code[9] == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaoshuang');
                if ((int)$code[9] % 2 == 0 && (int)$code[9] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('xiaodan');
                if ((int)$code[9] % 2 != 0 && (int)$code[9] < 5) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == $code[9]) {
                $peilv = M($table)->where("roomid = $roomid")->getField('tema');
                $zym_11 = $peilv * (int)$zym_7;
                $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
        if ($zym_1 == '和') {
            if ($code[0] == "0" || $code[1] == "0") {
                $hz = (int)$code[0] + (int)$code[1] + 10;
            } else {
                $hz = (int)$code[0] + (int)$code[1];
            }
            if ($zym_8 == '大') {
                $peilv = M($table)->where("roomid = $roomid")->getField('heda');
                if ($hz > 11) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '小') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hexiao');
                if ($hz < 12) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '单') {
                $peilv = M($table)->where("roomid = $roomid")->getField('hedan');
                if ($hz % 2 != 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ($zym_8 == '双') {
                $peilv = M($table)->where("roomid = $roomid")->getField('heshuang');
                if ($hz % 2 == 0) {
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } else {
                    $zym_11 = '-' . $zym_7;
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } elseif ((int)$zym_8 == $hz) {
                if ($hz == 3 || $hz == 4 || $hz == 18 || $hz == 19) {
                    $peilv = M($table)->where("roomid = $roomid")->getField('he341819');
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($hz == 5 || $hz == 6 || $hz == 16 || $hz == 17) {
                    $peilv = M($table)->where("roomid = $roomid")->getField('he561617');
                    $zym_11 = $peilv * (int)$zym_7;
                    add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($hz == 7 || $hz == 8 || $hz == 14 || $hz == 15) {
                    $peilv = M($table)->where("roomid = $roomid")->getField('he781415');
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($hz == 9 || $hz == 10 || $hz == 12 || $hz == 13) {
                    $peilv = M($table)->where("roomid = $roomid")->getField('he9101213');
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                } elseif ($hz == 11) {
                    $peilv = M($table)->where("roomid = $roomid")->getField('he11');
                    $zym_11 = $peilv * (int)$zym_7;
                    $this->add_points($user, $zym_11, $roomid, $game, $term, $zym_1 . '/' . $zym_8 . '/' . $zym_7);
                    M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                    continue;
                }
            } else {
                $zym_11 = '-' . $zym_7;
                M("fn_order")->where("id = $id")->setField(array('status'=>$zym_11));
                continue;
            }
        }
    }
    
    
}
function getcontent($data, $name)
{
    $array = [];
    foreach ($data as $val) {
        if (!isset($array[$val['content']])) {
            $array[$val['content']] = 0;
        }
        $array[$val['content']] += $val['money'];
    }
    $text = '';

    foreach ($array as $key => $v) {
        $text .= $key . '/' . $v . ' ';
    }
    if (!empty($text)) {
        $text = $name . "[{$text}]<br>";
    }

    return $text;
}
function getGameTxtName($gameid=''){
    $gmidName=[];
    $gmidName[1]='极速飞艇';
    $gmidName[2]='幸运飞艇';
    $gmidName[3]='极速飞艇';
    $gmidName[4]='极速赛车';
    $gmidName[5]='加拿大28';
    $gmidName[6]='极速摩托/飞艇';
    /*$gmidName[7]='jssc';
    $gmidName[8]='jsssc';*/
    if(empty($gameid)) return $gmidName;
    else return (isset($gmidName[$gameid])?$gmidName[$gameid]:'----');
}
  
    

function addBet($userid, $nickname, $headimg, $content, $addQihao, $fengpan)
{
    //$_COOKIE['game'] = 1;
    //global $gmidAli;
   // if ($fengpan) {
      //  $this->admin_say($userid,"@" . $nickname . " ,[$addQihao]期已经停止投注！下注无效！");
                        /**$time_message = array(
						'uid'  => $userid,
						'type' => 'admin',
						'headimg'=>$headimg,
						'from_client_name' => $nickname,
						'content' => "[$addQihao]期已经停止投注！下注无效！", 
						'time' => date('H:i:s')
					    );
                        
                        if(isset($this->worker->uidConnections[$userid])){
                	        $connection = $this->worker->uidConnections[$userid];
                	        $connection->send(json_encode($time_message));
                	    }*/
      //  return false;
  //  }
    $typeNum = 1;//array_flip($gmidAli);
    $gameName = '1';//$_COOKIE['game'];
    $gameTypeId = 1;//$typeNum[$gameName];
    
    $content = str_replace("冠亚和", "和", $content);
    $content = str_replace("冠亚", "和", $content);
    $content = str_replace("冠军", "1/", $content);
    $content = str_replace("亚军", "2/", $content);
    $content = str_replace("冠", "1/", $content);
    $content = str_replace("亚", "2/", $content);
    $content = str_replace("一", "1/", $content);
    $content = str_replace("二", "2/", $content);
    $content = str_replace("三", "3/", $content);
    $content = str_replace("四", "4/", $content);
    $content = str_replace("五", "5/", $content);
    $content = str_replace("六", "6/", $content);
    $content = str_replace("七", "7/", $content);
    $content = str_replace("八", "8/", $content);
    $content = str_replace("九", "9/", $content);
    $content = str_replace("十", "0/", $content);
    $content = str_replace(".", "/", $content);
    $content = preg_replace("/[位名各-]/u", "/", $content);
    $content = preg_replace("/(和|合|H|h)\//u", "$1", $content);
    $content = preg_replace("/[和合Hh]/u", "和/", $content);
    $content = preg_replace("/(大单|小单|大双|小双|大|小|单|双|龙|虎)\//u", "$1", $content);
    $content = preg_replace("/\/(大单|小单|大双|小双|大|小|单|双|龙|虎)/u", "$1", $content);
    $content = preg_replace("/(大单|小单|大双|小双|大|小|单|双|龙|虎)/u", "/$1/", $content);
  //  if ($_COOKIE['game'] == '1') {
       // $table = 'fn_lottery1';
  //  } elseif ($_COOKIE['game'] == 'xyft') {
  //  } elseif ($_COOKIE['game'] == 'jsmt') {
  //  } elseif ($_COOKIE['game'] == 'jssc') {
   // }
    $table = 'fn_lottery1';
    $ordertable = "fn_order";
            /**
    switch ($_COOKIE['game']) {
        case 'pk10':
            $table = 'fn_lottery1';
            $ordertable = "fn_order";
            break;
        case "xyft":
            $table = 'fn_lottery2';
            $ordertable = "fn_order";
            break;
        case "cqssc":
            $table = 'fn_lottery3';
            $ordertable = "fn_order";
            break;
        case "xy28":
            $table = 'fn_lottery4';
            $ordertable = "fn_order";
            break;
        // case "jnd28":
        //     $table = 'fn_lottery5';
        //     $ordertable = "fn_order";
        //     break;
        case "jsmt":
            $table = 'fn_lottery6';
            $ordertable = "fn_mtorder";
            break;
        case "jssc":
            $table = 'fn_lottery7';
            $ordertable = "fn_jsscorder";
            break;
    }**/
    
     
    $dx_min = M($table)->where("roomid = 188888")->getField('daxiao_min');
    $dx_max = M($table)->where("roomid = 188888")->getField('daxiao_max');
    $ds_min = M($table)->where("roomid = 188888")->getField('danshuang_min');
    $ds_max = M($table)->where("roomid = 188888")->getField('danshuang_max');
    $lh_min = M($table)->where("roomid = 188888")->getField('longhu_min');
    $lh_max = M($table)->where("roomid = 188888")->getField('longhu_max');
    $tm_min = M($table)->where("roomid = 188888")->getField('tema_min');
    $tm_max = M($table)->where("roomid = 188888")->getField('tema_max');
    $hz_min = M($table)->where("roomid = 188888")->getField('he_min');
    $hz_max = M($table)->where("roomid = 188888")->getField('he_max');
    $zh_min = M($table)->where("roomid = 188888")->getField('zuhe_min');
    $zh_max = M($table)->where("roomid = 188888")->getField('zuhe_max');
    
    $zym_8 = M("fn_user")->where("roomid = 188888 and userid={$userid}")->getField('jia');
    //get_query_val('fn_user', 'jia', array('userid' => $userid, 'roomid' => '188888')) == 'true' ? 'true' : 'false';
    //if($zym_8 == 'true'){
        
   // }
 
    $touzhu = false;
    $A = explode(" ", $content);
    $zym_2 = "";
    $con = $content;
    foreach ($A as $ai) {
        $ai = str_replace(" ", "", $ai);
        if (empty($ai)) continue;
        if (substr($ai, 0, 1) == '/') $ai = '1' . $ai;
        $b = explode("/", $ai);
        if (count($b) == 2) {
            $ai = '1/' . $ai;
            $b = explode("/", $ai);
        }

        if (count($b) != 3) continue;
        if ($b[0] == "" || $b[1] == "" || (int)$b[2] < 1) continue;
        $zym_9 = $this->check_points($userid);
                    
        $zym_10 = $b[0];
        $zym_6 = $b[1];
        $zym_5 = (int)$b[2];
         
        if ($zym_6 == '和') {
            $this->admin_say($userid,$nickname,$addQihao,"下注格式出错！冠亚和值下注格式为:和3/100",1,$con);
                   
                	    
            continue;
        }
        if ($zym_10 == '和') {
            if ($zym_6 == "大单" || $zym_6 == "大双" || $zym_6 == "小双" || $zym_6 == "小单") {
                $this->admin_say($userid,$nickname,$addQihao,"下注格式出错！冠亚和值无此类型下注！",1,$con);
                continue;
            }
            if ($zym_6 == "大" || $zym_6 == "小" || $zym_6 == "单" || $zym_6 == "双") {
                //echo $addQihao.'余额 \n';
                if ((int)$zym_9 < (int)$zym_5) {
                    $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                    continue;
                } elseif ($zym_5 < $hz_min || $this->sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $hz_max) {
                    $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                }
                $this->set_points($userid, $zym_5);
              //  insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
                M($ordertable)->add(array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
                
                
                $touzhu = true;
                continue;
            }
            $zym_6_分割 = $this->and_value_split($zym_6);
           // echo $zym_6_分割;
            foreach ($zym_6_分割 as $ii) {
                if ($ii < 3 || $ii > 19) {
                    $this->admin_say($userid,$nickname,$addQihao,"下注格式出错！冠亚和值为3 - 19！入单失败！",1,$con);
                    break;
                }
                if (!is_numeric($ii)) {
                    continue;
                } elseif ((int)$zym_9 < count($zym_6_分割) * (int)$zym_5) {
                    $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                    break;
                } elseif ($zym_5 < $hz_min || $this->sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $hz_max) {
                    $zym_2 .= $zym_10 . "/" . $ii . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                }
                $touzhu = true;
                $this->set_points($userid, $zym_5);
                //insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $ii, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
                //echo $nickname.'\n';
                M($ordertable)->add(array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $zym_10, 'content' => $ii, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
                continue;
            }
            continue;
        }
        if ($zym_6 == "大单" || $zym_6 == "大双" || $zym_6 == "小双" || $zym_6 == "小单") {
            $zym_10_分割 = $this->text_split($zym_10);
            foreach ($zym_10_分割 as $ii) {
                if (!is_numeric($ii)) {
                    continue;
                } elseif ($zym_9 < count($zym_10_分割) * (int)$zym_5) {
                    $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                    break;
                } elseif ($zym_5 < $zh_min || $this->sum_betmoney($ordertable, $zym_10, $zym_6, $userid, $addQihao) + $zym_5 > $zh_max) {
                    $zym_2 .= $zym_10 . "/" . $ii . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                }
                $touzhu = true;
                $this->set_points($userid, $zym_5);
                //insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
                M($ordertable)->add(array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
            }
            continue;
        }
        if ($zym_6 == "大" || $zym_6 == "小" || $zym_6 == "单" || $zym_6 == "双" || $zym_6 == "龙" || $zym_6 == "虎") {
            $zym_10_分割 = $this->text_split($zym_10);
            foreach ($zym_10_分割 as $ii) {
                if (!is_numeric($ii)) {
                    continue;
                } elseif ($zym_9 < count($zym_10_分割) * (int)$zym_5) {
                    $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                    break;
                } elseif ($zym_5 < $dx_min || $this->sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $dx_max && $zym_6 == "大") {
                    $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                } elseif ($zym_5 < $dx_min || $this->sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $dx_max && $zym_6 == "小") {
                    $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                } elseif ($zym_5 < $ds_min || $this->sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $ds_max && $zym_6 == "单") {
                    $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                } elseif ($zym_5 < $ds_min || $this->sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $ds_max && $zym_6 == "双") {
                    $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                } elseif ($zym_5 < $lh_min || $this->sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $lh_max && $zym_6 == "龙") {
                    $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                } elseif ($zym_5 < $lh_min || $this->sum_betmoney($ordertable, $ii, $zym_6, $userid, $addQihao) + $zym_5 > $lh_max && $zym_6 == "虎") {
                    $zym_2 .= $ii . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                }
                if ((int)$ii > 5 && $zym_6 == '龙' || (int)$ii > 5 && $zym_6 == '虎') {
                    $this->admin_say($userid,$nickname,$addQihao,"龙虎投注仅限1~5名！",1,$con);
                    continue;
                }
                $touzhu = true;
                $this->set_points($userid, $zym_5);
                //insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
                M($ordertable)->add(array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $zym_6, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算', 'jia' => $zym_8,'is_hui'=>0, 'type' => $gameTypeId));
            }
            continue;
        }
        $zym_6_分割 = $this->text_split($zym_6);
        //print_r($zym_6_分割);
        $zym_10_分割 = $this->text_split($zym_10);
        foreach ($zym_10_分割 as $ii) {
            if ($zym_9 < count($zym_10_分割) * count($zym_6_分割) * (int)$zym_5) {
                $zym_2 = $zym_2 . $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                break;
            } else if (!is_numeric($ii)) {
                continue;
            }
            foreach ($zym_6_分割 as $iii) {
                if (!is_numeric($iii)) {
                    continue;
                } else if ($zym_5 < $tm_min || $this->sum_betmoney($ordertable, $ii, $iii, $userid, $addQihao) + $zym_5 > $tm_max) {
                    $zym_2 .= $zym_10 . "/" . $zym_6 . "/" . $zym_5 . " ";
                    $chaozhu = true;
                    continue;
                }
                $touzhu = true;
                $this->set_points($userid, $zym_5);
                //insert_query($ordertable, array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $iii, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算','is_hui'=>0, 'jia' => $zym_8, 'type' => $gameTypeId));
                M($ordertable)->add(array('term' => $addQihao, 'userid' => $userid, 'username' => $nickname, 'headimg' => $headimg, 'mingci' => $ii, 'content' => $iii, 'money' => $zym_5, 'addtime' => date("Y-m-d H:i:s",time()), 'roomid' => '188888', 'status' => '未结算','is_hui'=>0, 'jia' => $zym_8, 'type' => $gameTypeId));
            }
        }
    }
    if ($zym_2 != "") {
        if ($chaozhu) {
            $this->admin_say($userid,$nickname,$addQihao,"您的:{$zym_2}未接<br>您的投注已超出限制！<br>本房投注限制如下:<br>大小最低{$dx_min}起,最高{$dx_max}<br>单双最低{$ds_min}起,最高{$ds_max}<br>龙虎最低{$lh_min}起,最高{$lh_max}<br>特码最低{$tm_min}起,最高{$tm_max}<br>和值最低{$hz_min}起,最高{$hz_max}<br>------------<br>最高投注均为已下注总注",1,$con);
            return;
        } else {
            $this->admin_say($userid,$nickname,$addQihao,"余额不足，您的余额：" . $this->check_points($userid),1,$con);
            return;
        }
    } elseif ( M("fn_setting")->where("roomid = 188888")->getField("setting_tishi") == 'open' && $touzhu == true){
        
    ///get_query_val("fn_setting", "setting_tishi", array("roomid" => '188888')) == 'open' && $touzhu == true) {
        $this->admin_say($userid,$nickname,$addQihao,$content,2,$con);
        return true;
    } elseif ($touzhu) {
        return true;
    }
    return false;
}

function shang_points($username, $userid, $money)
{
    
    $jia = M("fn_user")->where("userid = {$userid}")->getField("jia");
    
  //  insert_query("fn_upmark", array("userid" => $userid, 'headimg' => $_SESSION['headimg'], 'username' => $username, 'type' => '上分', 'money' => $money, 'status' => '未处理', 'time' => 'now()', 'game' => $_COOKIE['game'], 'roomid' => $_SESSION['roomid'],'is_hui'=>0, 'jia' => $jia));
     $count = M("fn_user")->where("userid = {$userid}")->find();
     
     $user_money = M("users")->where("id = {$userid}")->getField('coin');
     
     if($money <= $user_money){
         M("fn_upmark")->add(array("userid" => $userid, 'headimg' => $count['headimg'], 'username' => $username, 'type' => '上分', 'money' => $money, 'status' => '已处理', 'time' => date("Y-m-d H:i:s",time()), 'game' => 1, 'roomid' => "188888",'is_hui'=>0, 'jia' => $jia));
         
         //=======================
         //$yuan = M("fn_moneylog")->where("userid = $userid")->order('id desc')->limit(0,1)->find();
       // M('fn_moneylog')->add(array('userid' => $userid,'username'=>$userid, 'yuan_money' => $yuan['yuan_money'], 'money' => $yuan['money']+$money,'content'=>"上分", 'time' => date("Y-m-d H:i:s",time()), 'roomid' => '188888','yk'=>$yuan['yk']+$money, 'is_hui' => '0','status' => '1'));
     M("fn_user")->where("userid = $userid and roomid = 188888")->setInc('money',$money);
     M("users")->where("id = $userid")->setDec('coin',$money);
    M("fn_marklog")->add(array("userid" => $userid, 'type' => '上分', 'content' => '上分', 'money' => $money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
         //=======================
         $addQihao='0';
         
         $con="";
         $this->admin_say($userid,$username,$addQihao,$money,4,$con);
                        /**$message1 = array(
                    					'game' => '1',
                    					'headimg'=>$count['headimg'],
                    					'content' => "@$username $money已添加,您当前积分:{用户积分}！ ", 
                    					'roomid' => '188888',
                    					'addtime' => date('H:i:s'),
                    					'userid'=>$userid,
                    					'username'=>$username,
                    					'type'=>'U1'
                    				    );
                			        
                			        $this->add_message($message1);**/
         
         
     }else{
    M("fn_upmark")->add(array("userid" => $userid, 'headimg' => $count['headimg'], 'username' => $username, 'type' => '上分', 'money' => $money, 'status' => '未处理', 'time' => date("Y-m-d H:i:s",time()), 'game' => 1, 'roomid' => "188888",'is_hui'=>0, 'jia' => $jia));
     //$content = "";
     //$this->admin_say($userid,$username,$addQihao,$content,4,$con);
    
     }       
    
    
                       
                
    
    
}

function xia_opints($username, $userid, $money)
{
   
      $count = M("fn_user")->where("userid = {$userid}")->find();
    $jia = M("fn_user")->where("userid = {$userid}")->getField("jia");
    //$user_money = M("users")->where("id = {$userid}")->getField('coin');
     
     //echo $count['money']."==\n";
     if($money <= $count['money']){
         
        M("fn_upmark")->add(array("userid" => $userid, 'headimg' => $count['headimg'], 'username' => $username, 'type' => '下分', 'money' => $money, 'status' => '已处理', 'time' => date("Y-m-d H:i:s",time()), 'game' => 1, 'roomid' => 188888,'is_hui'=>0, 'jia' => $jia));
         
         //=======================
        // $yuan = M("fn_moneylog")->where("userid = $userid")->order('id desc')->limit(0,1)->find();
       // M('fn_moneylog')->add(array('userid' => $userid,'username'=>$userid, 'yuan_money' => $yuan['yuan_money'], 'money' => $yuan['money']-$money,'content'=>"下分", 'time' => date("Y-m-d H:i:s",time()), 'roomid' => '188888','yk'=>$yuan['yk']+$money, 'is_hui' => '0','status' => '1'));
     M("fn_user")->where("userid = $userid and roomid = 188888")->setDec('money',$money);
     M("users")->where("id = $userid")->setInc('coin',$money);
    M("fn_marklog")->add(array("userid" => $userid, 'type' => '下分', 'content' => '下分', 'money' => $money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
         //=======================
         $addQihao='0';
         
         $con="";
         $this->admin_say($userid,$username,$addQihao,$money,5,$con);
     }else{
    
        //M("fn_upmark")->add(array("userid" => $userid, 'headimg' => $count['headimg'], 'username' => $username, 'type' => '下分', 'money' => $money, 'status' => '未处理', 'time' => date("Y-m-d H:i:s",time()), 'game' => 1, 'roomid' => 188888,'is_hui'=>0, 'jia' => $jia));
         $addQihao='0';
         
         $con="";
        $this->admin_say($userid,$username,$addQihao,$money,6,$con);
                        
     }
                			        
                			       // $this->add_message($message1);
}


function set_points($Userid, $Money)
{
    //update_query('fn_user', array('money' => '-=' . $Money), array('userid' => $Userid, 'roomid' => '188888'));
      $yuan = M("fn_moneylog")->where("userid = $Userid")->order('id desc')->limit(0,1)->find();
      //$chushi = M("fn_moneylog")->where("userid = $Userid")->order('id desc')->limit(0,1)->getField("money");
      
        M('fn_moneylog')->add(array('userid' => $Userid,'username'=>$Userid, 'yuan_money' => $yuan['yuan_money'], 'money' => $yuan['money']-$Money,'content'=>"彩票投注", 'time' => date("Y-m-d H:i:s",time()), 'roomid' => '188888','yk'=>$yuan['yk']-$Money, 'is_hui' => '0','status' => '0'));
        
     M("fn_user")->where("userid = $Userid and roomid = 188888")->setDec('money',$Money);
     
     //M("users")->where("id = $Userid")->setDec('coin',$Money);
    //insert_query("fn_marklog", array("userid" => $Userid, 'type' => '下分', 'content' => '彩票投注', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
    M("fn_marklog")->add(array("userid" => $Userid, 'type' => '下分', 'content' => '彩票投注', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
    
}

function add_points($Userid, $Money)
{
    //update_query('fn_user', array('money' => '+=' . $Money), array('userid' => $Userid, 'roomid' => '188888'));
   // $user=M("fn_user")->where("userid = $Userid and roomid = 188888")->find();
      if($Userid != "robot"){
          $yuan = M("fn_moneylog")->where("userid = $Userid")->order('id desc')->limit(0,1)->find();
        M('fn_moneylog')->add(array('userid' => $Userid,'username'=>$Userid, 'yuan_money' => $yuan['yuan_money'], 'money' => $yuan['money']+$Money,'content'=>"盈利加分", 'time' => date("Y-m-d H:i:s",time()), 'roomid' => '188888','yk'=>$yuan['yk']+$Money, 'is_hui' => '0','status' => '1'));
     M("fn_user")->where("userid = $Userid and roomid = 188888")->setInc('money',$Money);
     //M("users")->where("id = $Userid")->setInc('coin',$Money);
    M("fn_marklog")->add(array("userid" => $Userid, 'type' => '上分', 'content' => '盈利加分', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
      }

}

function admin_says($userid,$Content)
{
    $headimg = M("fn_setting")->where("roomid = 188888")->getField('setting_robotsimg');
    //get_query_val('fn_setting', 'setting_robotsimg', array('roomid' => '188888'));
    
    //insert_query("fn_chat", array("userid" => "system", "username" => "游戏管理员", "game" => $_COOKIE['game'], 'headimg' => $headimg, 'content' => $Content, 'addtime' => date('H:i:s'), 'type' => 'S3', 'roomid' => '188888'));
    $id=M("fn_chat")->add(array("userid" => "1", "username" => "游戏管理员", "game" => 1, 'headimg' => $headimg, 'content' => $Content, 'addtime' => date('H:i:s'), 'type' => 'S3', 'roomid' => '188888'));
   
   
}

function admin_say($userid,$nickname,$addQihao,$Content,$type,$con)
{
    $headimg = M("fn_setting")->where("roomid = 188888")->getField('setting_robotsimg');
    //get_query_val('fn_setting', 'setting_robotsimg', array('roomid' => '188888'));
    
    //insert_query("fn_chat", array("userid" => "system", "username" => "游戏管理员", "game" => $_COOKIE['game'], 'headimg' => $headimg, 'content' => $Content, 'addtime' => date('H:i:s'), 'type' => 'S3', 'roomid' => '188888'));
    //$id=M("fn_chat")->add(array("userid" => "1", "username" => "游戏管理员", "game" => 1, 'headimg' => $headimg, 'content' => $Content, 'addtime' => date('H:i:s'), 'type' => 'S3', 'roomid' => '188888'));
    if($type == 1){
        $str1="竞猜失败";
        $change = "{失败错误}";
    }elseif($type == 2){
        $str1="竞猜成功";
    }elseif($type == 3){
        $str1="回水成功";
    }elseif($type == 4){
        $str1="上分提示";
    }elseif($type==5){
        $str1="下分提示";
    }elseif($type==6){
        $str1="下分失败";
    }
    
     $start_time = date("Y-m-d 00:00:00", time());
$end_time = date("Y-m-d 23:59:59", time());
   
                        $info_messages = M("fn_infos")->where("type=3 and game=1 and status=0")->select();
						foreach($info_messages as $k=>$v){
            			        $str = '';
            			       // $str1="竞猜无效";
            			        $Arr_Str1 = $this->arr_split_zh($str1);
                                $Arr1 = $this->arr_split_zh($v['title']);
                                //echo $v['title'];
                                if($this->Str_Is_Equal($Arr_Str1,$Arr1)){
                                    
                                    if($type==2){
                                        //竞猜成功
                                         $str = str_replace("{用户名}",$nickname,$v['content']);
                                        $str = str_replace("{游戏期数}",$addQihao,$str);
                                        
                                        $allmoney=M("fn_order")->where("userid = '$userid' and roomid = '188888' and term = '$addQihao'")->sum('money');
                                        $str = str_replace("{当前积分}",$this->check_points($userid),$str);
                                        $str = str_replace("{竞猜总分}",'本次'.$allmoney,$str);
                                        $str = str_replace("{竞猜内容}",'<br/>'.$con,$str);
                                       // $this->catch_content($con);
                                        $contents= M("fn_order")->where("type=1 and term={$addQihao}")->select();
                                            $data = [];
                                            $array = [];
                                            foreach($contents as $ks=>$vs) {
                                                if ($vs['status'] != '已撤单') {
                                                    if (!isset($data[$vs['username']]['sum'])) {
                                                        $data[$vs['username']]['sum'] = 0;
                                                    }
                                                    $data[$vs['username']]['sum'] += $vs['money'];
                                                    $data[$vs['username']]['data'][$vs['mingci']][] = $vs;
                                                }
                                            }
                                            $txt = "竞猜列表核对:<br>";
                                            foreach ($data as $key => $vv) {
                                                $d = $vv['data'];
                                                $txt .= $this->getcontent(isset($d['1']) ? $d['1'] : [], '冠军') . "
                                        " . $this->getcontent(isset($d['和']) ? $d['和'] : [], '冠亚') . "
                                        " . $this->getcontent(isset($d['2']) ? $d['2'] : [], '亚军') . "
                                        " . $this->getcontent(isset($d['3']) ? $d['3'] : [], '第三名') . "
                                        " . $this->getcontent(isset($d['4']) ? $d['4'] : [], '第四名') . "
                                        " . $this->getcontent(isset($d['5']) ? $d['5'] : [], '第五名') . "
                                        " . $this->getcontent(isset($d['6']) ? $d['6'] : [], '第六名') . "
                                        " . $this->getcontent(isset($d['7']) ? $d['7'] : [], '第七名') . "
                                        " . $this->getcontent(isset($d['8']) ? $d['8'] : [], '第八名') . "
                                        " . $this->getcontent(isset($d['9']) ? $d['9'] : [], '第九名') . "
                                        " . $this->getcontent(isset($d['0']) ? $d['0'] : [], '第十名');
                                            } 
                                            
                                            $str.=$txt;
                                       
                                       
                                       
                                       
                                       echo $str;
                                       
                                        
                                        $str = str_replace("{当前时间}",date("H:i:s",time()),$str);
                                        
                                        
                                    }elseif($type==3){
                                          $str = str_replace("{用户名}",$nickname,$v['content']);
                                       // $str = str_replace("{游戏期数}",$addQihao,$str);
                                        
                                        $allmoney=M("fn_order")->where("userid = '$userid' and roomid = '188888' and is_hui = '1' and (addtime between '$start_time' and '$end_time')")->sum('money');
                                        $str = str_replace("{用户积分}",$this->check_points($userid),$str);  
                                        
                                        $str = str_replace("{总流水积分}",$allmoney,$str); 
                                        $str = str_replace("{退还积分}",$Content,$str);
                                        $str = str_replace("{积分变动}","+".$Content,$str);
                                        
                                    }elseif($type == 4){
                                        $str = str_replace("{用户名}",$nickname,$v['content']);
                                       // $str = str_replace("{游戏期数}",$addQihao,$str);
                                        
                                       // $allmoney=M("fn_order")->where("userid = '$userid' and roomid = '188888' and is_hui = '1' and (addtime between '$start_time' and '$end_time')")->sum('money');
                                        $str = str_replace("{增加积分}",$Content,$str);  
                                        
                                        $str = str_replace("{用户积分}",$this->check_points($userid),$str); 
                                        
                                    }elseif($type == 5){
                                        $str = str_replace("{用户名}",$nickname,$v['content']);
                                       // $str = str_replace("{游戏期数}",$addQihao,$str);
                                        
                                       // $allmoney=M("fn_order")->where("userid = '$userid' and roomid = '188888' and is_hui = '1' and (addtime between '$start_time' and '$end_time')")->sum('money');
                                        $str = str_replace("{减少积分}",$Content,$str);  
                                        
                                        $str = str_replace("{用户积分}",$this->check_points($userid),$str); 
                                        
                                    }elseif($type == 6){
                                        $str = str_replace("{用户名}",$nickname,$v['content']);
                                       // $str = str_replace("{游戏期数}",$addQihao,$str);
                                        
                                       // $allmoney=M("fn_order")->where("userid = '$userid' and roomid = '188888' and is_hui = '1' and (addtime between '$start_time' and '$end_time')")->sum('money');
                                        $str = str_replace("{减少积分}",$this->check_points($userid),$str);  
                                        
                                        //$str = str_replace("{用户积分}",$this->check_points($userid),$str); 
                                        //break;
                                        
                                    }else{
                                     $str = str_replace("{用户名}",$nickname,$v['content']);
                                     $str = str_replace($change,$Content,$str);
                                    }
            			         
            			        // echo $str;
                                    $message1 = array(
                    					'game' => '1',
                    					'headimg'=>$headimg,
                    					'content' => $str, 
                    					'roomid' => '188888',
                    					'addtime' => date('H:i:s'),
                    					'userid'=>'system',
                    					'username'=>'管理员',
                    					'type'=>'U3'
                    				    );
                			        
                			        $this->add_message($message1);
                			
                	            }
                        }
   
   
}

function text_split($str, $split_len = 1)
{
    if (!preg_match('/^[0-9]+$/', $split_len) || $split_len < 1) return FALSE;
    $len = mb_strlen($str, 'UTF-8');
    if ($len <= $split_len) return array($str);
    preg_match_all("/.{" . $split_len . '}|[^x00]{1,' . $split_len . '}$/us', $str, $ar);
    return $ar[0];
}

function middle_split($str)
{
    $arr = $this->text_split($str);
    $new = array();
    foreach ($arr as $ii) {
        if ($ii == "前") {
            $new[] = "前三";
            continue;
        }
        if ($ii == "中") {
            $new[] = "中三";
            continue;
        }
        if ($ii == "后") {
            $new[] = "后三";
            continue;
        }
        continue;
    }
    return $new;
}
function and_value_split($str)
{
    $arr = $this->text_split($str);
    $new = array();
    $ii_1_b = true;
    $ii_1 = '';
    foreach ($arr as $ii) {
        if (!$ii_1_b && $ii_1 == "1") $ii = "1" . $ii;
        $ii_1 = $ii;
        if ($ii_1_b) $ii_1_b = false;
        if ($ii == "1") continue;
        array_push($new, $ii);
    }
    return $new;
}
function check_points($Userid)
{
    return M("fn_user")->where(array('userid' => $Userid, 'roomid' => '188888'))->getField("money");//(int)get_query_val('fn_user', 'money', array('userid' => $Userid, 'roomid' => '188888'));
}
function set_huishui($Userid, $Money,$nickname,$addQihao)
{
    //update_query('fn_user', array('money' => '-=' . $Money), array('userid' => $Userid, 'roomid' => '188888'));
    
            $start_time = date("Y-m-d 00:00:00", time());
$end_time = date("Y-m-d 23:59:59", time());
     M("fn_user")->where("userid = $Userid and roomid = '188888'")->setInc('money',$Money);
     
    // M("users")->where("id = $Userid")->setInc('coin',$Money);
    //insert_query("fn_marklog", array("userid" => $Userid, 'type' => '下分', 'content' => '彩票投注', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
    M("fn_marklog")->add(array("userid" => $Userid, 'type' => '上分', 'content' => '回水积分', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
    
    M("fn_order")->where("userid = '$Userid' and roomid = '188888' and is_hui = '0' and (addtime between '$start_time' and '$end_time')")->setField('is_hui',1);
    $con='';
       $this->admin_say($Userid,$nickname,$addQihao,$Money,3,$con); 
    
}
function sum_betmoney($table, $mc, $cont, $user, $term)
{
    $re = M($table)->where('userid='.$user.' and term = '.$term.' and mingci = "'.$mc.'"and content = "'.$cont.'"')->sum("money");
    //get_query_val($table, 'sum(`money`)', array('userid' => $user, 'term' => $term, 'mingci' => $mc, 'content' => $cont));
   // echo $re.'余额 \n';
    return (int)$re;
}
function getGameIdByCode($code=''){
    global $gmidAli;
    $typeNum=$gmidAli;
    $typeNum=array_flip($typeNum);
    if(empty($code)) return $typeNum;
    else return (isset($typeNum[$code])?$typeNum[$code]:'----');
}
function wordkeys($content)
{
    $keys = M("fn_setting")->where("roomid",'188888')->getField('setting_wordkeys');//get_query_val('fn_setting', 'setting_wordkeys', array('roomid' => '188888'));
    $arr = explode("|", $keys);
    foreach ($arr as $con) {
        if ($con == "") {
            continue;
        }
        if (preg_match("/$con/", $content)) {
            return false;
        }
    }
    return true;
}

function CancelBet($userid, $term, $game, $fengpan)
{
    $chedan = M("fn_setting")->where("roomid",'188888')->getField('setting_cancelbet') == 'open' ? true : false;
    if ($chedan) {
        return;
    } else {
        if ($fengpan) {
            $this->admin_say($userid,$nickname,$term,"[$term]期已经停止投注！无法取消！",3,$con);
            return false;
        }
        switch ($game) {
            case '1':
                $table = "fn_order";
                break;
            case "jnd28":
                $table = "fn_order";
                break;
            case "jsmt":
                $table = "fn_order";
                break;
            case "jssc":
                $table = "fn_order";
                break;
            case "jsssc":
                $table = "fn_order";
                break;
            case "cqssc":
                $table = "fn_order";
                break;
            case "pk10":
                $table = "fn_order";
                break;
            case "xyft":
                $table = "fn_order";
                break;
        }


        /* switch($game){
            case 'xy28': $table = "fn_pcorder";
                break;
            case "jnd28": $table = "fn_pcorder";
                break;
            case "jsmt": $table = "fn_jsmtorder";
                break;
            case "jssc": $table = "fn_jsscorder";
                break;
            case "jsssc": $table = "fn_jsscorder";
                break;
            case "cqssc": $table = 'fn_jsmtorder';//"fn_sscorder";
                break;
            case "pk10": $table = "fn_order";
                break;
            case "xyft": $table = "fn_order";
                break;
        }  */
        /* switch($game){
            case 'xy28': $table = "fn_pcorder";
                break;
            case "jnd28": $table = "fn_pcorder";
                break;
            case "jsmt": $table = "fn_jsmtorder";
                break;
            case "jssc": $table = "fn_jsscorder";
                break;
            case "jsssc": $table = "fn_jssscorder";
                break;
            case "cqssc": $table = "fn_sscorder";
                break;
            case "pk10": $table = "fn_order";
                break;
            case "xyft": $table = "fn_order";
                break;
        } */
       // $all = (int)get_query_val($table, 'sum(`money`)', "userid = '$userid' and term = '$term' and status = '未结算' and roomid = 188888");
         $all =M($table)->where("userid = '$userid' and term = '$term' and status = '未结算' and roomid = 188888")->sum('money');
        
       // update_query($table, array('status' => '已撤单'), "userid = '$userid' and term = '$term' and roomid = 188888");
        
         M($table)->where("userid = '$userid' and term = '$term' and roomid = 188888")->setField('status','已撤单');
        $this->user_points($userid, $all);
        $this->admin_say($userid,$nickname,$term,"[$term]期投注已经退还!");
    }
}
function user_set_points($Userid, $Money)//xiafen 
{
    
                                
        $yuan=$this->check_points($Userid);
        M('fn_moneylog')->add(array('userid' => $Userid, 'yuan_money' => $yuan, 'money' => $yuan+$Money,'content'=>"彩票投注", 'time' => date("Y-m-d",time()), 'roomid' => '188888', 'is_hui' => '0'));


    $res = M('fn_user')->where("userid = $Userid and roomid => 188888")->setDec('money',$Money);
    //update_query('fn_user', array('money' => '-=' . $Money), array('userid' => $Userid, 'roomid' => '188888'));
    //insert_query("fn_marklog", array("userid" => $Userid, 'type' => '下分', 'content' => '彩票投注', 'money' => $Money, 'roomid' => '188888', 'addtime' => 'now()'));
     M('fn_marklog')->add(array("userid" => $Userid, 'type' => '下分', 'content' => '彩票投注', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
     
}

function user_points($Userid, $Money)//shanfen 
{
    M('fn_user')->where("userid = $Userid and roomid => 188888")->setInc('money',$Money);
    M('users')->where("id = $Userid")->setInc('coin',$Money);
    //update_query('fn_user', array('money' => '+=' . $Money), array('userid' => $Userid, 'roomid' => '188888'));
    
   // insert_query("fn_marklog", array("userid" => $Userid, 'type' => '上分', 'content' => '投注撤单退还', 'money' => $Money, 'roomid' => '188888', 'addtime' => 'now()'));
    M('fn_marklog')->add(array("userid" => $Userid, 'type' => '上分', 'content' => '投注撤单退还', 'money' => $Money, 'roomid' => '188888', 'addtime' => date("Y-m-d H:i:s",time())));
}

function arr_split_zh($tempaddtext){
$cind = 0;
$arr_cont=array();
for($i=0;$i<strlen($tempaddtext);$i++)
{
if(strlen(substr($tempaddtext,$cind,1)) > 0){
if(ord(substr($tempaddtext,$cind,1)) < 0xA1 ){ //如果为英文则取1个字节
array_push($arr_cont,substr($tempaddtext,$cind,1));
$cind++;
}else{
array_push($arr_cont,substr($tempaddtext,$cind,2));
$cind+=2;
}
}
}
return $arr_cont;
}

function Str_Is_Equal($mystr1,$mystr2){
$result = 0;
for($i=0;$mystr1[$i];$i++){
if($mystr1[$i] !=$mystr2[$i]){
$result = 0;
break;
}
$result = 1;
}
return $result;
}
	

}
?>