<?php

namespace MyApp;

class Controller {

  private $_errors;
  private $_values;


  public function __construct() {
    $this->_errors = new \stdClass();
    $this->_values = new \stdClass();
    if(!isset($_SESSION['token'])){
      $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }
  }
  // Set/Get error
  protected function setErr($key, $error){
    $this->_errors->$key = $error;
  }
  public function getErr($key){
    return isset($this->_errors->$key) ? $this->_errors->$key : '';
  }
  // Set/Get Value
  protected function setVal($key, $val){
    $this->_values->$key = $val;
  }
  public function getVal(){
    return $this->_values;
  }
 
  protected function hasErr(){
   return !empty(get_object_vars($this->_errors));
  }

  // validation
  protected function InvalidRequired($str, $key){
    if($str === ''){
      $this->setErr($key, '入力必須です。');
    }
  }

  protected function InvalidEmail($str, $key){
    if(!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/",$str)){
      $this->setErr($key, 'メール形式が正しくありません。');
    }
  }

  protected function InvalidHalf($str, $key){
    if(!preg_match("/^[0-9a-zA-Z]*$/",$str)){
      $this->setErr($key, '半角英数字６文字以上で。');
    }
  }
  protected function InvalidMaxLen($str, $key, $max=255){
    if(mb_strlen($str) >= $max){
      $this->setErr($key, $max.'文字未満で。');
    }
  }

  protected function InvalidMinLen($str, $key, $min=6){
    if(mb_strlen($str) < $min){
      $this->setErr($key, '6文字以上で。');
    }
  }
  // End of validation functions

  protected function isLoggedIn(){
  return isset($_SESSION['me']) && !empty($_SESSION['me']);
  }

  public function me() {
  return $this->isLoggedIn()? $_SESSION['me'] : null;
  }

  public function sendMail($from, $to, $subject, $comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        //文字化けしないように設定（お決まりパターン）
        mb_language("Japanese"); //現在使っている言語を設定する
        mb_internal_encoding("UTF-8"); //内部の日本語をどうエンコーディング（機械が分かる言葉へ変換）するかを設定
        
        //メールを送信（送信結果はtrueかfalseで返ってくる）
        $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
        //送信結果を判定
        if ($result) {
          debug('メールを送信しました。');
        } else {
          debug('【エラー発生】メールの送信に失敗しました。');
        }
    }
}
}
